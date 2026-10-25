<?php
// +----------------------------------------------------------------------
// | 易客对接插件
// | 博客地址：zhonguo.ren
// | Q群：915043052
// | 开发者：教主
// +----------------------------------------------------------------------
namespace plugins;

class third_yike {
    protected $conf;
    
    // 插件配置信息 - 确保格式正确以便系统识别
    static public $info = array(
        'name'        => 'third_yike',
        'title'       => '易客对接',
        'description' => '支持与易客系统进行商品对接和订单处理',
        'status'      => 1,
        'sort'        => 100,
        'pricejk'     => 1, // 价格监控支持：1=支持，2=支持库存同步
        'showip'      => false, // 是否显示IP白名单提示
        'showedit'    => true, // 是否显示编辑按钮
        'batchgoods'  => true, // 支持批量对接商品
        'input'       => array(
            'url'      => '网站域名',
            'username' => 'AppId',
            'password' => 'AppSecret',
            'paypwd'   => '备注（可选）',
            'paytype'  => '支付方式'
        )
    );
    
    /**
     * 构造函数
     */
    public function __construct($shequ) {
        // 初始化配置
        $protocol = stripos($shequ['url'], 'http') === 0 ? '' : 'http://';
        $this->conf = array(
            'url'      => $protocol . $shequ['url'],
            'username' => $shequ['username'],
            'password' => $shequ['password'],
            'paypwd'   => $shequ['paypwd'],
            'paytype'  => $shequ['paytype']
        );
    }
    
    /**
     * 初始化插件配置
     */
    public static function init($url, $username, $password, $paypwd = '', $paytype = 1) {
        // 确保URL格式正确
        $protocol = stripos($url, 'http') === 0 ? '' : 'http://';
        return array(
            'url'      => $protocol . $url,
            'username' => $username,
            'password' => $password,
            'paypwd'   => $paypwd,
            'paytype'  => $paytype
        );
    }
    
    /**
     * 获取商品分类列表
     */
    public function getCategory() {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/category';
            $result = $this->request($url, 'GET', array());
            
            if ($result['code'] == 100) {
                $categories = array();
                foreach ($result['result']['data'] as $category) {
                    $categories[] = array(
                        'id'   => $category['categoryId'],
                        'name' => $category['categoryName']
                    );
                }
                return array('code' => 0, 'msg' => 'success', 'data' => $categories);
            }
            return array('code' => 1, 'msg' => $result['msg']);
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 获取分类列表（标准插件系统接口）
     * 用于批量对接商品功能
     */
    public function class_list() {
        try {
            // 调用现有的获取分类方法
            $result = $this->getCategory();
            
            if ($result['code'] == 0) {
                $categories = array();
                // 转换为标准插件系统期望的格式
                foreach ($result['data'] as $category) {
                    $categories[] = array(
                        'cid'  => $category['id'],
                        'name' => $category['name']
                    );
                }
                return $categories;
            }
            return $result['msg'];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * 获取商品详情
     */
    public function getGoodsDetail($goodsSN) {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/goods';
            $params = array(
                'goodsSN' => $goodsSN
            );
            
            $result = $this->request($url, 'GET', $params);
            
            if ($result['code'] == 100 && isset($result['result'])) {
                return $result['result'];
            } else {
                return array();
            }
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * 获取商品列表
     */
    public function getGoodsList($category = '', $page = 1, $pagesize = 50) {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/goods/list';
            $params = array(
                'categoryId' => $category,
                'page'       => $page,
                'limit'      => $pagesize
            );
            
            // 记录请求信息
            $request_start = microtime(true);
            $result = $this->request($url, 'GET', $params);
            $request_end = microtime(true);
            $request_time = round(($request_end - $request_start) * 1000, 2);
            
            // 检查响应状态
            if ($result['code'] == 100) {
                $goods = array();
                // 检查响应数据结构
                if (!isset($result['result']) || !isset($result['result']['data']) || !is_array($result['result']['data'])) {
                    throw new \Exception("响应数据结构错误: " . json_encode($result));
                }
                
                foreach ($result['result']['data'] as $item) {
                    // 直接使用列表接口返回的数据，不调用详情接口以提高性能
                    $goodsSN = $item['goodsSN'] ?? '';
                    
                    if (empty($goodsSN)) continue;
                    
                    // 构造商品数据，使用列表接口返回的基础信息
                    $goods_item = array(
                        'id'            => $goodsSN,
                        'name'          => $item['goodsName'] ?? '未知商品',
                        'price'         => 0, // 列表接口不返回价格，设置为0，后续需要时再获取
                        'stock'         => -1,
                        'minnum'        => 1,
                        'maxnum'        => 9999,
                        'min'           => 1,
                        'max'           => 9999,
                        'category'      => $item['categoryId'] ?? 0,
                        'category_name' => $item['categoryName'] ?? '未分类',
                        'shopimg'       => $item['goodsThumb'] ?? '',
                        'type'          => ''
                    );
                    
                    $goods[] = $goods_item;
                }
                return array('code' => 0, 'msg' => 'success', 'data' => $goods);
            } else {
                // 记录完整错误信息
                $error_msg = "API返回错误: " . ($result['msg'] ?? '未知错误') . " (code: " . ($result['code'] ?? '未知') . ")";
                return array('code' => 1, 'msg' => $error_msg);
            }
        } catch (\Exception $e) {
            $error_msg = "获取商品列表失败: " . $e->getMessage();
            return array('code' => 1, 'msg' => $error_msg);
        }
    }
    
    /**
     * 获取商品列表（兼容third_call函数）
     */
    public function goods_list($category = '', $page = 1, $pagesize = 100) {
        try {
            $result = $this->getGoodsList($category, $page, $pagesize);
            if ($result['code'] == 0) {
                $goodsList = $result['data'];
                // 确保返回格式正确，每个商品包含足够信息
                $formattedList = array();
                foreach ($goodsList as $goods) {
                    // 只返回基础信息，详细信息在用户选择商品时通过getGoodsParam获取
                    $formattedList[] = array(
                        'id' => $goods['id'],
                        'name' => $goods['name'],
                        'price' => $goods['price'],
                        'min' => $goods['min'],
                        'max' => $goods['max'],
                        'stock' => $goods['stock'],
                        'category' => $goods['category'],
                        'category_name' => $goods['category_name'],
                        'shopimg' => $goods['shopimg']
                    );
                }
                return $formattedList;
            } else {
                return $result['msg'];
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * 获取指定分类的商品列表（标准插件系统接口）
     * 用于批量对接商品功能
     */
    public function goods_list_by_cid($cid) {
        try {
            // 调用现有的获取商品列表方法
            $result = $this->getGoodsList($cid, 1, 200);
            
            if ($result['code'] == 0) {
                $goods = array();
                foreach ($result['data'] as $item) {
                    // 构造标准插件系统期望的商品数据格式
                    $goods_item = array(
                        'tid'    => $item['id'],                 // 商品ID
                        'name'   => $item['name'],               // 商品名称
                        'price'  => $item['price'],              // 商品价格
                        'input'  => 1,                           // 输入类型：1=文本输入
                        'inputs' => '输入内容',                   // 输入字段描述
                        'desc'   => '',                          // 商品描述
                        'alert'  => '',                          // 商品提示
                        'shopimg'=> $item['shopimg'],            // 商品图片
                        'repeat' => 1,                           // 是否支持重复下单：1=支持
                        'multi'  => 1,                           // 是否支持批量下单：1=支持
                        'min'    => $item['min'],                // 最小下单数量
                        'max'    => $item['max'],                // 最大下单数量
                        'validate'=> '',                         // 输入验证规则
                        'valiserv'=> 0,                          // 验证服务：0=不使用
                        'close'  => 0,                           // 商品状态：0=正常，1=下架
                        'isfaka' => 0,                           // 是否为发卡商品：0=否
                        // 添加原始分类信息，用于批量添加时的分类创建
                        'original_cid'   => $item['category'],
                        'original_cname' => $item['category_name']
                    );
                    $goods[] = $goods_item;
                }
                return $goods;
            }
            return '获取商品列表失败: ' . ($result['msg'] ?? '未知错误');
        } catch (\Exception $e) {
            return '获取商品列表失败: ' . $e->getMessage();
        }
    }
    
    /**
     * 获取商品列表（兼容third_call函数）
     */
    public function goods_list_old($category = '', $page = 1, $pagesize = 100) {
        try {
            $result = $this->getGoodsList($category, $page, $pagesize);
            if ($result['code'] == 0) {
                $goodsList = $result['data'];
                // 确保返回格式正确，每个商品包含足够信息
                $formattedList = array();
                foreach ($goodsList as $goods) {
                    // 只返回基础信息，详细信息在用户选择商品时通过getGoodsParam获取
                    $formattedList[] = array(
                        'id' => $goods['id'],
                        'name' => $goods['name'],
                        'price' => $goods['price'],
                        'min' => $goods['min'],
                        'max' => $goods['max'],
                        'stock' => $goods['stock'],
                        'category' => $goods['category'],
                        'category_name' => $goods['category_name'],
                        'shopimg' => $goods['shopimg'],
                        'minnum' => $goods['minnum'],
                        'maxnum' => $goods['maxnum'],
                        'input' => '', // 这些字段在用户选择商品时通过getGoodsParam获取
                        'inputs' => '',
                        'desc' => '',
                        'alert' => ''
                    );
                }
                return $formattedList;
            }
            // 返回详细的错误信息，包括API响应和请求详情
            return $result['msg'];
        } catch (\Exception $e) {
            $error_msg = '获取商品列表失败：' . $e->getMessage();
            // 记录错误到日志文件
            $log_file = __DIR__ . '/yike_error.log';
            $log_content = date('Y-m-d H:i:s') . ' - ' . $error_msg . "\n";
            file_put_contents($log_file, $log_content, FILE_APPEND);
            return $error_msg;
        }
    }
    
    /**
     * 获取商品详情
     */
    public function getGoodsInfo($id) {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/goods';
            $params = array('goodsSN' => $id);
            
            // 记录请求开始时间
            $request_start = microtime(true);
            $result = $this->request($url, 'GET', $params);
            $request_end = microtime(true);
            $request_time = round(($request_end - $request_start) * 1000, 2);
            
            if ($result['code'] == 100) {
                $goods = $result['result'];
                
                // 确保 ParamsTemplate 存在且为字符串
                $params_template = isset($goods['ParamsTemplate']) ? $goods['ParamsTemplate'] : '[]';
                if (empty($params_template)) {
                    $params_template = '[]';
                }
                
                // 解析参数模板
                $params = array();
                $template_data = json_decode($params_template, true);
                if (is_array($template_data)) {
                    foreach ($template_data as $p) {
                        // 支持不同格式的参数模板
                        $param_name = $p['name'] ?? $p['key'] ?? '';
                        $param_alias = $p['alias'] ?? $p['description'] ?? '';
                        $param_required = $p['required'] ?? true;
                        
                        // 确保参数名称不为空
                        if (empty($param_name)) {
                            $param_name = '参数' . (count($params) + 1);
                        }
                        
                        // 确保参数别名不为空
                        if (empty($param_alias)) {
                            $param_alias = '请输入' . $param_name;
                        }
                        
                        $params[] = array(
                            'name'     => $param_name,
                            'alias'    => $param_alias,
                            'required' => $param_required
                        );
                    }
                }
                
                // 如果没有解析到参数，添加默认参数
                if (empty($params)) {
                    $params[] = array(
                        'name'     => '账号',
                        'alias'    => '请输入账号',
                        'required' => true
                    );
                }
                
                // 构造返回数据，确保所有字段都有值
                $return_data = array(
                    'id'          => $goods['goodsSN'] ?? $id,
                    'name'        => $goods['goodsName'] ?? '未知商品',
                    'price'       => $goods['goodsPrice'] ?? 0,
                    'min'         => $goods['minOrderNum'] ?? 1,
                    'max'         => $goods['maxOrderNum'] ?? 9999,
                    'minnum'      => $goods['minOrderNum'] ?? 1,
                    'maxnum'      => $goods['maxOrderNum'] ?? 9999,
                    'stock'       => $goods['goodsStock'] ?? -1,
                    'params'      => $params,
                    'is_codepwd'  => ($goods['goodsType'] ?? 1) == 2 ? 1 : 0,
                    'description' => $goods['goodsDesc'] ?? $goods['goodsName'] ?? '未知商品',
                    'detail'      => $goods['goodsDetail'] ?? '',
                    'shopimg'     => $goods['goodsThumb'] ?? '',
                    'category'    => $goods['categoryId'] ?? 0,
                    'category_name' => $goods['categoryName'] ?? '未分类',
                    'goodsType'   => $goods['goodsType'] ?? 1
                );
                
                // 确保价格是数字类型
                if (is_string($return_data['price'])) {
                    $return_data['price'] = floatval($return_data['price']);
                }
                
                // 确保最小/最大下单数量是整数
                $return_data['min'] = intval($return_data['min']);
                $return_data['max'] = intval($return_data['max']);
                $return_data['minnum'] = intval($return_data['minnum']);
                $return_data['maxnum'] = intval($return_data['maxnum']);
                
                return array('code' => 0, 'msg' => 'success', 'data' => $return_data);
            }
            return array('code' => 1, 'msg' => $result['msg'] ?? '未知错误');
        } catch (\Exception $e) {
            $error_msg = "获取商品详情失败: " . $e->getMessage();
            // 记录错误到日志文件
            $log_file = __DIR__ . '/yike_error.log';
            $log_content = date('Y-m-d H:i:s') . ' - ' . $error_msg . "\n";
            file_put_contents($log_file, $log_content, FILE_APPEND);
            return array('code' => 1, 'msg' => $error_msg);
        }
    }
    
    /**
     * 获取商品详情（兼容third_call函数）
     */
    public function goods_info($id) {
        $result = $this->getGoodsInfo($id);
        if ($result['code'] == 0) {
            $data = $result['data'];
            
            // 提取参数信息
            $params = $data['params'];
            $main_input = $params[0]['name'] ?? '账号';
            $other_inputs = array_slice($params, 1);
            $other_input_names = array_column($other_inputs, 'name');
            
            // 调整返回格式，适应系统要求
            return array(
                'name' => $data['name'],
                'price' => $data['price'],
                'min' => $data['min'],
                'max' => $data['max'],
                'minnum' => $data['minnum'],
                'maxnum' => $data['maxnum'],
                'input' => $main_input,
                'inputs' => implode('|', $other_input_names),
                'desc' => $data['description'],
                'alert' => '', // 易客API没有返回提示内容，使用空字符串
                'shopimg' => $data['shopimg'], // 使用实际的商品图片
                'stock' => $data['stock'],
                'detail' => $data['detail'],
                'category' => $data['category'],
                'category_name' => $data['category_name'],
                'is_codepwd' => $data['is_codepwd']
            );
        }
        // 记录错误到日志文件
        $error_msg = '获取商品详情失败：' . $result['msg'];
        $log_file = __DIR__ . '/yike_error.log';
        $log_content = date('Y-m-d H:i:s') . ' - ' . $error_msg . "\n";
        file_put_contents($log_file, $log_content, FILE_APPEND);
        return $error_msg;
    }
    
    /**
     * 提交订单
     */
    public function submitOrder($gid, $num, $params, $order_no = '') {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/order';
            
            // 格式化参数
            $formatted_params = array();
            foreach ($params as $p) {
                $formatted_params[] = array(
                    'name'  => $p['name'],
                    'alias' => $p['alias'],
                    'value' => $p['value']
                );
            }
            
            $data = array(
                'goodsSN'       => $gid,
                'number'        => $num,
                'orderNote'     => '来自对接站点的订单',
                'customOrderSN' => $order_no ?: date('YmdHis') . rand(1000, 9999),
                'params'        => $formatted_params
            );
            
            $result = $this->request($url, 'POST', $data, 'json');
            
            if ($result['code'] == 100 && !empty($result['result']['orderSN'])) {
                return array('code' => 0, 'msg' => '下单成功', 'id' => $result['result']['orderSN']);
            }
            return array('code' => 1, 'msg' => $result['msg'] ?? '下单失败');
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 查询订单状态
     */
    public function query_order($trade_no) {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/order';
            $params = array('orderSN' => $trade_no);
            
            $result = $this->request($url, 'GET', $params);
            
            if ($result['code'] == 100) {
                $order = $result['result'];
                
                // 订单状态映射
                $status_map = array(
                    1 => 1,  // 待处理
                    2 => 2,  // 处理中
                    3 => 3,  // 已退单
                    4 => 4,  // 有异常
                    5 => 3,  // 退款中
                    6 => 4,  // 异常中
                    7 => 0,  // 已完成
                    9 => 1,  // 未使用
                    10 => 3  // 退款
                );
                
                $status = $status_map[$order['state'] ?? 0] ?? 1;
                $cardno = $order['cardNumber'] ?? '';
                $addtime = $order['createdAt'] ?? time();
                
                // 处理订单日志
                $log = array();
                if (!empty($order['logs'])) {
                    $logs = json_decode($order['logs'], true);
                    if (is_array($logs)) {
                        foreach ($logs as $l) {
                            $log[] = $l['createdAt'] . ' ' . $l['content'];
                        }
                    }
                }
                
                return array('code' => 0, 'msg' => '查询成功', 'data' => array(
                    'status'    => $status,
                    'orderno'   => $trade_no,
                    'num'       => $order['orderNum'] ?? 0,
                    'price'     => $order['price'] ?? 0,
                    'addtime'   => $addtime,
                    'endtime'   => $status == 0 ? time() : 0,
                    'cardno'    => $cardno,
                    'input'     => '',
                    'output'    => '',
                    'log'       => implode("\n", $log),
                    'refund'    => ($order['state'] ?? 0) == 3 || ($order['state'] ?? 0) == 5 || ($order['state'] ?? 0) == 10 ? 1 : 0,
                    'refund_fee' => $order['refundAmount'] ?? 0
                ));
            }
            return array('code' => 1, 'msg' => $result['msg']);
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 申请退款
     */
    public function refundOrder($trade_no) {
        try {
            $url = $this->conf['url'] . '/api/client/goods/v2/order/state';
            $data = array('orderSN' => $trade_no);
            
            $result = $this->request($url, 'POST', $data, 'json');
            
            if ($result['code'] == 100) {
                return array('code' => 0, 'msg' => '退款申请已提交');
            }
            return array('code' => 1, 'msg' => $result['msg'] ?? '退款申请失败');
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 检测连接
     */
    public function checkConnection() {
        try {
            $url = $this->conf['url'] . '/api/client/account/v2/profile';
            $result = $this->request($url, 'GET', array());
            
            if ($result['code'] == 100 && !empty($result['result'])) {
                return array('code' => 0, 'msg' => '连接成功', 'info' => '余额：' . ($result['result']['balance'] ?? 0));
            }
            return array('code' => 1, 'msg' => $result['msg'] ?? '连接失败');
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 通用请求函数 - 处理HTTP请求
     */
    private function request($url, $method = 'GET', $data = array(), $type = 'form') {
        $ch = curl_init();
        
        // 生成当前时间戳
        $timestamp = time();
        
        // 解析URL，获取请求URI（除域名外的部分）
        $parse_url = parse_url($url);
        $requestURI = $parse_url['path'];
        
        // 初始化请求头信息
        $headers = array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        );
        
        // 生成AppToken（根据易客文档要求）
        $AppId = $this->conf['username'];
        $AppSecret = $this->conf['password']; // 注意：这里的password实际是AppSecret
        
        // 处理请求参数和URL
        $full_url = $url;
        
        if ($method == 'GET') {
            // GET请求：将参数添加到URL和requestURI中
            if (!empty($data)) {
                $query_string = http_build_query($data);
                $requestURI .= '?' . $query_string;
                $full_url .= '?' . $query_string;
            }
        } else {
            // POST请求：根据类型处理参数
            if ($type == 'json') {
                $headers[] = 'Content-Type: application/json';
                $data_str = json_encode($data, JSON_UNESCAPED_UNICODE);
            } else {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                $data_str = http_build_query($data);
            }
        }
        
        // 生成AppToken - 严格按照文档要求的顺序
        $AppToken = sha1($AppId . $AppSecret . $requestURI . $timestamp);
        
        // 添加认证头 - 确保顺序正确
        $headers[] = 'AppId: ' . $AppId;
        $headers[] = 'AppTimestamp: ' . $timestamp;
        $headers[] = 'AppToken: ' . $AppToken;
        
        // 设置CURL选项
        // 自动跟随重定向（处理301/302等）
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // 最大重定向次数
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        // 保存重定向历史
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_str);
        }
        
        $response = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($curl_errno) {
            $error_msg = 'CURL请求失败：' . $curl_error . ' (错误码: ' . $curl_errno . ')';
            throw new \Exception($error_msg);
        }
        
        if ($http_code != 200) {
            $error_msg = 'HTTP错误：状态码 ' . $http_code . '，响应: ' . substr($response, 0, 200);
            throw new \Exception($error_msg);
        }
        
        $result = json_decode($response, true);
        if (!is_array($result)) {
            $error_msg = '响应格式错误：无法解析JSON，原始响应: ' . substr($response, 0, 200);
            throw new \Exception($error_msg);
        }
        
        // 检查是否认证失败
        if (isset($result['code']) && $result['code'] == 403) {
            $error_msg = 'API认证失败：' . ($result['msg'] ?? '请检查AppId和AppSecret配置');
            throw new \Exception($error_msg);
        }
        
        return $result;
    }
    
    /**
     * 处理订单（兼容third_call函数）
     */
    public function do_goods($gid, $goods_type, $goods_param, $num, $inputs, $money, $order_no, $inputsname) {
        try {
            // 解析输入参数
            $params = array();
            $input_array = explode('|', $inputsname);
            foreach ($input_array as $i => $name) {
                $params[] = array(
                    'name'  => $name,
                    'alias' => $name,
                    'value' => $inputs[$i]
                );
            }
            
            $result = $this->submitOrder($gid, $num, $params, $order_no);
            return $result;
        } catch (\Exception $e) {
            return array('code' => 1, 'msg' => $e->getMessage());
        }
    }
    
    /**
     * 批量获取商品列表（标准插件系统接口）
     * 用于批量对接商品功能
     */
    public function batch_goods_list() {
        try {
            // 获取所有分类
            $categories = $this->class_list();
            if (!is_array($categories)) {
                return $categories; // 返回错误信息
            }
            
            $all_goods = array();
            
            // 遍历每个分类，获取商品列表
            foreach ($categories as $category) {
                $cid = $category['cid'];
                $goods_list = $this->goods_list_by_cid($cid);
                
                if (is_array($goods_list)) {
                    foreach ($goods_list as $goods) {
                        $all_goods[] = $goods;
                    }
                }
            }
            
            return $all_goods;
        } catch (\Exception $e) {
            return '获取批量商品列表失败: ' . $e->getMessage();
        }
    }
    
    /**
     * 编辑页面JS - 预留扩展
     */
    public static function shopeditjs($goods) {
        return '';
    }
}