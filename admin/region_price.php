<?php

include("../includes/common.php");
$title = '指定地区加价';
include './head.php';
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

use lib\RegionPrice;

$regionPrice = new RegionPrice();

$my = isset($_GET['my']) ? $_GET['my'] : 'list';

if ($my == 'add') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'region_name' => trim($_POST['region_name']),
            'region_keywords' => trim($_POST['region_keywords']),
            'add_price_type' => intval($_POST['add_price_type']),
            'add_price_value' => floatval($_POST['add_price_value']),
            'min_price' => floatval($_POST['min_price']),
            'max_price' => floatval($_POST['max_price']),
            'status' => intval($_POST['status']),
            'sort_order' => intval($_POST['sort_order'])
        ];

        if (empty($data['region_name']) || empty($data['region_keywords'])) {
            exit("<script language='javascript'>alert('地区名称和关键词不能为空！');history.go(-1);</script>");
        }

        if ($regionPrice->addRule($data) !== false) {
            // 清除缓存 - 博客地址：zhonguo.ren QQ群：915043052
            $regionPrice->clearCache();
            exit("<script language='javascript'>alert('添加成功！');window.location.href='region_price.php';</script>");
        } else {
            exit("<script language='javascript'>alert('添加失败！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'edit') {
    $id = intval($_GET['id']);
    $rule = $regionPrice->getRuleById($id);

    if (!$rule) {
        exit("<script language='javascript'>alert('规则不存在！');window.location.href='region_price.php';</script>");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'region_name' => trim($_POST['region_name']),
            'region_keywords' => trim($_POST['region_keywords']),
            'add_price_type' => intval($_POST['add_price_type']),
            'add_price_value' => floatval($_POST['add_price_value']),
            'min_price' => floatval($_POST['min_price']),
            'max_price' => floatval($_POST['max_price']),
            'status' => intval($_POST['status']),
            'sort_order' => intval($_POST['sort_order'])
        ];

        if (empty($data['region_name']) || empty($data['region_keywords'])) {
            exit("<script language='javascript'>alert('地区名称和关键词不能为空！');history.go(-1);</script>");
        }

        if ($regionPrice->updateRule($id, $data) !== false) {
            // 清除缓存 - 博客地址：zhonguo.ren QQ群：915043052
            $regionPrice->clearCache();
            exit("<script language='javascript'>alert('修改成功！');window.location.href='region_price.php';</script>");
        } else {
            exit("<script language='javascript'>alert('修改失败！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'delete') {
    $id = intval($_GET['id']);
    if ($regionPrice->deleteRule($id) !== false) {
        exit("<script language='javascript'>alert('删除成功！');window.location.href='region_price.php';</script>");
    } else {
        exit("<script language='javascript'>alert('删除失败！');history.go(-1);</script>");
    }
} elseif ($my == 'import') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
            $file = $_FILES['import_file']['tmp_name'];
            $content = file_get_contents($file);

            if (pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION) == 'json') {
                $rules_data = json_decode($content, true);
            } else {
                $lines = explode("\n", $content);
                $rules_data = [];
                $headers = [];
                foreach ($lines as $index => $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $fields = explode(',', $line);
                    if ($index == 0) {
                        $headers = array_map('trim', $fields);
                    } else {
                        $row = [];
                        foreach ($headers as $i => $header) {
                            $row[$header] = isset($fields[$i]) ? trim($fields[$i]) : '';
                        }
                        $rules_data[] = $row;
                    }
                }
            }

            if (!empty($rules_data) && is_array($rules_data)) {
                $result = $regionPrice->importRules($rules_data);
                exit("<script language='javascript'>alert('导入完成！成功{$result['success']}条，失败{$result['error']}条');window.location.href='region_price.php';</script>");
            } else {
                exit("<script language='javascript'>alert('文件格式错误或内容为空！');history.go(-1);</script>");
            }
        } else {
            exit("<script language='javascript'>alert('请选择要导入的文件！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'export') {
    $status = isset($_GET['status']) ? intval($_GET['status']) : null;
    $rules = $regionPrice->exportRules($status);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="region_price_rules_' . date('YmdHis') . '.json"');
    echo json_encode($rules, JSON_UNESCAPED_UNICODE);
    exit;
} elseif ($my == 'logs') {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $pagesize = 20;
    $offset = ($page - 1) * $pagesize;

    $filters = [];
    if (!empty($_GET['order_id'])) {
        $filters['order_id'] = $_GET['order_id'];
    }
    if (!empty($_GET['tid'])) {
        $filters['tid'] = intval($_GET['tid']);
    }
    if (!empty($_GET['region_id'])) {
        $filters['region_id'] = intval($_GET['region_id']);
    }
    if (!empty($_GET['start_time'])) {
        $filters['start_time'] = $_GET['start_time'];
    }
    if (!empty($_GET['end_time'])) {
        $filters['end_time'] = $_GET['end_time'];
    }

    if (isset($_GET['clear_logs'])) {
        $before_date = !empty($_GET['before_date']) ? $_GET['before_date'] : null;
        $regionPrice->clearLogs($before_date);
        exit("<script language='javascript'>alert('日志清理成功！');window.location.href='region_price.php?my=logs';</script>");
    }

    $logs = $regionPrice->getLogs($pagesize, $offset, $filters);
    $total = $logs['total'];
    $log_list = $logs['list'];
    $total_pages = ceil($total / $pagesize);
}
?>
<div class="col-md-12 center-block" style="float: none;">
    <?php if ($my == 'list') { ?>
    <div class="block">
        <div class="block-title clearfix">
            <h2>指定地区加价规则管理</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>地区名称</th>
                        <th>加价类型</th>
                        <th>加价数值</th>
                        <th>价格范围</th>
                        <th>状态</th>
                        <th>排序</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rules = $regionPrice->getRules();
                    if (!empty($rules)) {
                        foreach ($rules as $rule) {
                            $add_type_text = $rule['add_price_type'] == 1 ? '固定金额' : '百分比';
                            $add_value_text = $rule['add_price_type'] == 1 ? '¥' . $rule['add_price_value'] : $rule['add_price_value'] . '%';
                            $status_text = $rule['status'] == 1 ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';
                    ?>
                    <tr>
                        <td><?php echo $rule['id']; ?></td>
                        <td><?php echo htmlspecialchars($rule['region_name']); ?></td>
                        <td><?php echo $add_type_text; ?></td>
                        <td><?php echo $add_value_text; ?></td>
                        <td>¥<?php echo $rule['min_price']; ?> - ¥<?php echo $rule['max_price']; ?></td>
                        <td><?php echo $status_text; ?></td>
                        <td><?php echo $rule['sort_order']; ?></td>
                        <td>
                            <a href="region_price.php?my=edit&id=<?php echo $rule['id']; ?>" class="btn btn-xs btn-info">编辑</a>
                            <a href="javascript:if(confirm('确定要删除这条规则吗？'))location.href='region_price.php?my=delete&id=<?php echo $rule['id']; ?>'" class="btn btn-xs btn-danger">删除</a>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="8" class="text-center">暂无规则数据</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <a href="region_price.php?my=add" class="btn btn-primary"><i class="fa fa-plus"></i> 添加规则</a>
            <a href="region_price.php?my=import" class="btn btn-success"><i class="fa fa-upload"></i> 批量导入</a>
            <a href="region_price.php?my=export" class="btn btn-info"><i class="fa fa-download"></i> 导出规则</a>
            <a href="region_price.php?my=logs" class="btn btn-warning"><i class="fa fa-list-alt"></i> 查看日志</a>
        </div>
    </div>
    <?php } elseif ($my == 'add' || $my == 'edit') { ?>
    <div class="block">
        <div class="block-title clearfix">
            <h2><?php echo $my == 'add' ? '添加地区加价规则' : '编辑地区加价规则'; ?></h2>
        </div>

        <form method="POST" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">地区名称</label>
                <div class="col-sm-10">
                    <input type="text" name="region_name" class="form-control" value="<?php echo $my == 'edit' ? htmlspecialchars($rule['region_name']) : ''; ?>" placeholder="如：广东、北京" required>
                    <p class="help-block">地区显示名称</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">地区关键词</label>
                <div class="col-sm-10">
                    <textarea name="region_keywords" class="form-control" rows="3" placeholder="如：广东,广州,深圳,珠海" required><?php echo $my == 'edit' ? htmlspecialchars($rule['region_keywords']) : ''; ?></textarea>
                    <p class="help-block">多个关键词用逗号分隔，用于地址匹配</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">加价类型</label>
                <div class="col-sm-10">
                    <select name="add_price_type" class="form-control">
                        <option value="1" <?php echo $my == 'edit' && $rule['add_price_type'] == 1 ? 'selected' : ''; ?>>固定金额</option>
                        <option value="2" <?php echo $my == 'edit' && $rule['add_price_type'] == 2 ? 'selected' : ''; ?>>百分比</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">加价数值</label>
                <div class="col-sm-10">
                    <input type="number" name="add_price_value" class="form-control" value="<?php echo $my == 'edit' ? $rule['add_price_value'] : '0.00'; ?>" step="0.01" min="0" required>
                    <p class="help-block">固定金额填写具体数值，百分比填写如20表示20%</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">最低价格限制</label>
                <div class="col-sm-10">
                    <input type="number" name="min_price" class="form-control" value="<?php echo $my == 'edit' ? $rule['min_price'] : '0.00'; ?>" step="0.01" min="0">
                    <p class="help-block">商品价格低于此值时不加价</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">最高价格限制</label>
                <div class="col-sm-10">
                    <input type="number" name="max_price" class="form-control" value="<?php echo $my == 'edit' ? $rule['max_price'] : '999999.99'; ?>" step="0.01" min="0">
                    <p class="help-block">商品价格高于此值时不加价</p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">状态</label>
                <div class="col-sm-10">
                    <select name="status" class="form-control">
                        <option value="1" <?php echo $my == 'edit' && $rule['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                        <option value="0" <?php echo $my == 'edit' && $rule['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">排序</label>
                <div class="col-sm-10">
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $my == 'edit' ? $rule['sort_order'] : '0'; ?>" min="0">
                    <p class="help-block">数字越小越靠前</p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">保存</button>
                    <a href="region_price.php" class="btn btn-default">返回</a>
                </div>
            </div>
        </form>
    </div>
    <?php } elseif ($my == 'import') { ?>
    <div class="block">
        <div class="block-title clearfix">
            <h2>批量导入地区加价规则</h2>
        </div>

        <form method="POST" enctype="multipart/form-data" class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">选择文件</label>
                <div class="col-sm-10">
                    <input type="file" name="import_file" class="form-control" accept=".csv,.json" required>
                    <p class="help-block">支持CSV或JSON格式文件</p>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">开始导入</button>
                    <a href="region_price.php" class="btn btn-default">返回</a>
                </div>
            </div>
        </form>

        <div class="panel panel-default" style="margin-top: 20px;">
            <div class="panel-heading">
                <h4>文件格式说明</h4>
            </div>
            <div class="panel-body">
                <h5>CSV格式示例：</h5>
                <pre>region_name,region_keywords,add_price_type,add_price_value,min_price,max_price,status,sort_order
广东,广东,广州,深圳,珠海,1,10.00,0.00,999999.99,1,1
北京,北京,北京市,1,15.00,0.00,999999.99,1,2</pre>

                <h5>JSON格式示例：</h5>
                <pre>[
  {
    "region_name": "广东",
    "region_keywords": "广东,广州,深圳,珠海",
    "add_price_type": 1,
    "add_price_value": 10.00,
    "min_price": 0.00,
    "max_price": 999999.99,
    "status": 1,
    "sort_order": 1
  }
]</pre>
            </div>
        </div>
    </div>
    <?php } elseif ($my == 'logs') { ?>
    <div class="block">
        <div class="block-title clearfix">
            <h2>价格计算日志</h2>
        </div>

        <form method="GET" class="form-inline">
            <input type="hidden" name="my" value="logs">
            <div class="form-group">
                <input type="text" name="order_id" class="form-control" placeholder="订单ID" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="text" name="tid" class="form-control" placeholder="商品ID" value="<?php echo isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="text" name="start_time" class="form-control" placeholder="开始时间" value="<?php echo isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : ''; ?>">
            </div>
            <div class="form-group">
                <input type="text" name="end_time" class="form-control" placeholder="结束时间" value="<?php echo isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">查询</button>
            <a href="region_price.php?my=logs" class="btn btn-default">重置</a>
            <a href="region_price.php" class="btn btn-default">返回</a>
        </form>

        <div class="table-responsive" style="margin-top: 20px;">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>订单ID</th>
                        <th>商品名称</th>
                        <th>原价</th>
                        <th>地区</th>
                        <th>加价</th>
                        <th>最终价格</th>
                        <th>地址</th>
                        <th>时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($log_list)) {
                        foreach ($log_list as $log) {
                            $add_type_text = $log['add_price_type'] == 1 ? '固定' : '百分比';
                            $add_value_text = $log['add_price_type'] == 1 ? '¥' . $log['add_price_value'] : $log['add_price_value'] . '%';
                    ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo htmlspecialchars($log['order_id']); ?></td>
                        <td><?php echo htmlspecialchars($log['tool_name']); ?></td>
                        <td>¥<?php echo $log['original_price']; ?></td>
                        <td><?php echo htmlspecialchars($log['region_name']); ?></td>
                        <td><?php echo $add_value_text; ?> (¥<?php echo $log['add_price_amount']; ?>)</td>
                        <td><strong>¥<?php echo $log['final_price']; ?></strong></td>
                        <td><?php echo htmlspecialchars(mb_substr($log['address'], 0, 20)); ?>...</td>
                        <td><?php echo $log['create_time']; ?></td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="9" class="text-center">暂无日志数据</td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1) { ?>
        <nav>
            <ul class="pagination">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = $i == $page ? 'active' : '';
                    $params = $_GET;
                    $params['page'] = $i;
                    $url = '?' . http_build_query($params);
                ?>
                <li class="<?php echo $active; ?>"><a href="<?php echo $url; ?>"><?php echo $i; ?></a></li>
                <?php
                }
                ?>
            </ul>
        </nav>
        <?php } ?>

        <div class="form-group" style="margin-top: 20px;">
            <a href="javascript:if(confirm('确定要清理所有日志吗？'))location.href='region_price.php?my=logs&clear_logs=1'" class="btn btn-danger"><i class="fa fa-trash"></i> 清理所有日志</a>
            <a href="javascript:if(confirm('确定要清理30天前的日志吗？'))location.href='region_price.php?my=logs&clear_logs=1&before_date=<?php echo date('Y-m-d H:i:s', strtotime('-30 days')); ?>'" class="btn btn-warning"><i class="fa fa-trash"></i> 清理30天前日志</a>
        </div>
    </div>
    <?php } ?>
</div>
<?php include './foot.php'; ?>
