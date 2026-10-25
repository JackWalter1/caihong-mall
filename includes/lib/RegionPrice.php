<?php

namespace lib;

class RegionPrice {

    private $DB;

    private $rules = [];

    private $cache_enabled = true;

    private $cache_time = 3600;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->loadRules();
    }

    public function loadRules() {
        $cache_key = 'region_price_rules';
        $cache_file = SYSTEM_ROOT . 'cache/' . $cache_key . '.php';

        if ($this->cache_enabled && file_exists($cache_file) && (time() - filemtime($cache_file)) < $this->cache_time) {
            $this->rules = include $cache_file;
        } else {
            $rs = $this->DB->query("SELECT * FROM pre_region_price_rules WHERE status=1 ORDER BY sort_order ASC, id ASC");
            $this->rules = [];
            while ($row = $rs->fetch()) {
                $this->rules[$row['id']] = $row;
            }
            if ($this->cache_enabled) {
                $this->saveCache($cache_file, $this->rules);
            }
        }
    }

    private function saveCache($file, $data) {
        $content = '<?php return ' . var_export($data, true) . ';';
        file_put_contents($file, $content);
    }

    public function clearCache() {
        $cache_file = SYSTEM_ROOT . 'cache/region_price_rules.php';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }

    public function parseAddress($address) {
        if (empty($address)) {
            return null;
        }

        $address = trim($address);
        $address = str_replace(['收货地址', '收货人地址', '：', ':'], '', $address);

        foreach ($this->rules as $rule) {
            $keywords = explode(',', $rule['region_keywords']);
            // 将地区名称也作为关键词进行匹配 - 博客地址：zhonguo.ren QQ群：915043052
            $keywords[] = $rule['region_name'];
            
            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (!empty($keyword) && strpos($address, $keyword) !== false) {
                    return $rule;
                }
            }
        }

        return null;
    }

    public function calculatePrice($original_price, $address, $tid = 0, $tool_name = '', $order_id = '') {
        $result = [
            'original_price' => $original_price,
            'final_price' => $original_price,
            'add_price_amount' => 0,
            'region_id' => 0,
            'region_name' => '',
            'add_price_type' => 0,
            'add_price_value' => 0,
            'matched' => false
        ];

        if ($original_price <= 0) {
            return $result;
        }

        $rule = $this->parseAddress($address);

        if ($rule) {
            if ($original_price < $rule['min_price'] || $original_price > $rule['max_price']) {
                return $result;
            }

            $add_price_amount = 0;
            if ($rule['add_price_type'] == 1) {
                $add_price_amount = $rule['add_price_value'];
            } elseif ($rule['add_price_type'] == 2) {
                $add_price_amount = $original_price * ($rule['add_price_value'] / 100);
            }

            $final_price = $original_price + $add_price_amount;

            $result = [
                'original_price' => $original_price,
                'final_price' => round($final_price, 2),
                'add_price_amount' => round($add_price_amount, 2),
                'region_id' => $rule['id'],
                'region_name' => $rule['region_name'],
                'add_price_type' => $rule['add_price_type'],
                'add_price_value' => $rule['add_price_value'],
                'matched' => true
            ];

            if (!empty($order_id) && $tid > 0) {
                $this->logPriceCalculation($order_id, $tid, $tool_name, $result, $address);
            }
        }

        return $result;
    }

    public function logPriceCalculation($order_id, $tid, $tool_name, $price_result, $address) {
        global $DB;

        $user_ip = x_real_ip();

        $sql = "INSERT INTO pre_region_price_logs 
                (order_id, tid, tool_name, original_price, region_id, region_name, 
                 add_price_type, add_price_value, add_price_amount, final_price, address, user_ip, create_time)
                VALUES 
                (:order_id, :tid, :tool_name, :original_price, :region_id, :region_name,
                 :add_price_type, :add_price_value, :add_price_amount, :final_price, :address, :user_ip, NOW())";

        $data = [
            ':order_id' => $order_id,
            ':tid' => $tid,
            ':tool_name' => $tool_name,
            ':original_price' => $price_result['original_price'],
            ':region_id' => $price_result['region_id'],
            ':region_name' => $price_result['region_name'],
            ':add_price_type' => $price_result['add_price_type'],
            ':add_price_value' => $price_result['add_price_value'],
            ':add_price_amount' => $price_result['add_price_amount'],
            ':final_price' => $price_result['final_price'],
            ':address' => $address,
            ':user_ip' => $user_ip
        ];

        $DB->exec($sql, $data);
    }

    public function getRules($status = null) {
        if ($status === null) {
            return $this->rules;
        }

        $filtered = [];
        foreach ($this->rules as $rule) {
            if ($rule['status'] == $status) {
                $filtered[$rule['id']] = $rule;
            }
        }
        return $filtered;
    }

    public function getRuleById($id) {
        return isset($this->rules[$id]) ? $this->rules[$id] : null;
    }

    public function addRule($data) {
        $sql = "INSERT INTO pre_region_price_rules 
                (region_name, region_keywords, add_price_type, add_price_value, min_price, max_price, status, sort_order, create_time, update_time)
                VALUES 
                (:region_name, :region_keywords, :add_price_type, :add_price_value, :min_price, :max_price, :status, :sort_order, NOW(), NOW())";

        $params = [
            ':region_name' => $data['region_name'],
            ':region_keywords' => $data['region_keywords'],
            ':add_price_type' => $data['add_price_type'],
            ':add_price_value' => $data['add_price_value'],
            ':min_price' => $data['min_price'],
            ':max_price' => $data['max_price'],
            ':status' => $data['status'],
            ':sort_order' => $data['sort_order']
        ];

        $result = $this->DB->exec($sql, $params);
        if ($result !== false) {
            $this->clearCache();
            $this->loadRules();
        }
        return $result;
    }

    public function updateRule($id, $data) {
        $sql = "UPDATE pre_region_price_rules SET 
                region_name = :region_name,
                region_keywords = :region_keywords,
                add_price_type = :add_price_type,
                add_price_value = :add_price_value,
                min_price = :min_price,
                max_price = :max_price,
                status = :status,
                sort_order = :sort_order,
                update_time = NOW()
                WHERE id = :id";

        $params = [
            ':id' => $id,
            ':region_name' => $data['region_name'],
            ':region_keywords' => $data['region_keywords'],
            ':add_price_type' => $data['add_price_type'],
            ':add_price_value' => $data['add_price_value'],
            ':min_price' => $data['min_price'],
            ':max_price' => $data['max_price'],
            ':status' => $data['status'],
            ':sort_order' => $data['sort_order']
        ];

        $result = $this->DB->exec($sql, $params);
        if ($result !== false) {
            $this->clearCache();
            $this->loadRules();
        }
        return $result;
    }

    public function deleteRule($id) {
        $result = $this->DB->exec("DELETE FROM pre_region_price_rules WHERE id = :id", [':id' => $id]);
        if ($result !== false) {
            $this->clearCache();
            $this->loadRules();
        }
        return $result;
    }

    public function importRules($rules_data) {
        $success_count = 0;
        $error_count = 0;

        foreach ($rules_data as $row) {
            if (empty($row['region_name']) || empty($row['region_keywords'])) {
                $error_count++;
                continue;
            }

            $data = [
                'region_name' => $row['region_name'],
                'region_keywords' => $row['region_keywords'],
                'add_price_type' => isset($row['add_price_type']) ? intval($row['add_price_type']) : 1,
                'add_price_value' => isset($row['add_price_value']) ? floatval($row['add_price_value']) : 0,
                'min_price' => isset($row['min_price']) ? floatval($row['min_price']) : 0,
                'max_price' => isset($row['max_price']) ? floatval($row['max_price']) : 999999.99,
                'status' => isset($row['status']) ? intval($row['status']) : 1,
                'sort_order' => isset($row['sort_order']) ? intval($row['sort_order']) : 0
            ];

            if ($this->addRule($data) !== false) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        return [
            'success' => $success_count,
            'error' => $error_count
        ];
    }

    public function exportRules($status = null) {
        $rules = $this->getRules($status);
        return $rules;
    }

    public function getLogs($limit = 50, $offset = 0, $filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['order_id'])) {
            $where[] = "order_id LIKE :order_id";
            $params[':order_id'] = '%' . $filters['order_id'] . '%';
        }

        if (!empty($filters['tid'])) {
            $where[] = "tid = :tid";
            $params[':tid'] = $filters['tid'];
        }

        if (!empty($filters['region_id'])) {
            $where[] = "region_id = :region_id";
            $params[':region_id'] = $filters['region_id'];
        }

        if (!empty($filters['start_time'])) {
            $where[] = "create_time >= :start_time";
            $params[':start_time'] = $filters['start_time'];
        }

        if (!empty($filters['end_time'])) {
            $where[] = "create_time <= :end_time";
            $params[':end_time'] = $filters['end_time'];
        }

        $where_sql = implode(' AND ', $where);

        $total = $this->DB->getColumn("SELECT COUNT(*) FROM pre_region_price_logs WHERE {$where_sql}", $params);

        $sql = "SELECT * FROM pre_region_price_logs WHERE {$where_sql} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
        $rs = $this->DB->query($sql, $params);

        $logs = [];
        while ($row = $rs->fetch()) {
            $logs[] = $row;
        }

        return [
            'total' => $total,
            'list' => $logs
        ];
    }

    public function clearLogs($before_date = null) {
        if ($before_date) {
            $sql = "DELETE FROM pre_region_price_logs WHERE create_time < :before_date";
            $params = [':before_date' => $before_date];
        } else {
            $sql = "DELETE FROM pre_region_price_logs";
            $params = [];
        }
        return $this->DB->exec($sql, $params);
    }
}
