<?php


//请勿删除版权信息，否则出现问题将不再保持售后修复！
include "../includes/common.php";
$title = "后台管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?><div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">

	<?php 
$mod = isset($_GET["mod"]) ? $_GET["mod"] : null;
if ($mod == "cleanbom") {
	adminpermission("set", 1);
	$filename = ROOT . "config.php";
	$contents = file_get_contents($filename);
	$charset[1] = substr($contents, 0, 1);
	$charset[2] = substr($contents, 1, 1);
	$charset[3] = substr($contents, 2, 1);
	if (ord($charset[1]) == 239 && ord($charset[2]) == 187) {
		$rest = substr($contents, 3);
		file_put_contents($filename, $rest);
		showmsg("找到BOM并已自动去除", 1);
	} else {
		showmsg("没有找到BOM", 2);
	}
	echo "\t";
} elseif ($mod == "bind") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">管理员后台微信/QQ扫码登录</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">开启扫码登录</label>
						<div class="col-sm-10"><select class="form-control" name="thirdlogin_open" default="<?php echo $conf["thirdlogin_open"];?>">
								<option value="0">否</option>
								<option value="1">是</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">绑定微信</label>
						<div class="col-sm-10">
							<div class="input-group"><input type="text" name="thirdlogin_wx" value="<?php echo $conf["thirdlogin_wx"];?>" class="form-control" disabled /><span class="input-group-btn"><a href="javascript:thirdloginbind('wx')" class="btn btn-success">绑定</a><a href="javascript:thirdloginunbind('wx')" class="btn btn-danger">解绑</a></span></div>
						</div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">绑定QQ</label>
						<div class="col-sm-10">
							<div class="input-group"><input type="text" name="thirdlogin_qq" value="<?php echo $conf["thirdlogin_qq"];?>" class="form-control" disabled /><span class="input-group-btn"><a href="javascript:thirdloginbind('qq')" class="btn btn-success">绑定</a><a href="javascript:thirdloginunbind('qq')" class="btn btn-danger">解绑</a></span></div>
						</div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">关闭密码登录</label>
						<div class="col-sm-10"><select class="form-control" name="thirdlogin_closepwd" default="<?php echo $conf["thirdlogin_closepwd"];?>">
								<option value="0">否</option>
								<option value="1">是</option>
							</select></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
						</div>
					</div>
				</form>
			</div>
			<br/>
			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">高级防CC功能设置（IP重定向）</h3>
				</div>
				<div class="">
					<form action="./set.php?mod=defend_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
						<div class="form-group">
							<label class="col-sm-2 control-label">启用高级防CC</label>
							<div class="col-sm-10"><select class="form-control" name="cc_protect_enabled" default="<?php echo isset($conf['cc_protect_enabled']) ? $conf['cc_protect_enabled'] : '0';?>">
									<option value="0">关闭</option>
									<option value="1">开启</option>
								</select>
								<font color="green">开启后会自动检测异常IP访问频率并执行重定向</font>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">最大请求数</label>
							<div class="col-sm-10"><input type="number" name="cc_max_requests" value="<?php echo isset($conf['cc_max_requests']) ? $conf['cc_max_requests'] : '30';?>" class="form-control" />
								<font color="green">检测时间窗口内允许的最大请求数</font>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">检测时间窗口</label>
							<div class="col-sm-10"><input type="number" name="cc_check_time" value="<?php echo isset($conf['cc_check_time']) ? $conf['cc_check_time'] : '30';?>" class="form-control" />
								<font color="green">单位：秒</font>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">封禁时长</label>
							<div class="col-sm-10"><input type="number" name="cc_block_time" value="<?php echo isset($conf['cc_block_time']) ? $conf['cc_block_time'] : '300';?>" class="form-control" />
								<font color="green">单位：秒</font>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">重定向网址</label>
							<div class="col-sm-10"><input type="text" name="cc_redirect_url" value="<?php echo isset($conf['cc_redirect_url']) ? $conf['cc_redirect_url'] : 'https://www.baidu.com';?>" class="form-control" />
								<font color="green">被攻击时将恶意IP重定向到的网址，请输入完整URL（包含http://或https://）</font>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="保存设置" class="btn btn-primary btn-block" /><br />
							</div>
						</div>
					</form>
				</div>
				<div class="panel-footer">
					<span class="glyphicon glyphicon-info-sign"></span>高级防CC功能说明<br />
					此功能可自动检测异常IP访问频率，当发现恶意访问时将其重定向到指定网站<br />
					建议设置合理的参数，避免误拦截正常用户<br />
					默认将恶意IP重定向到百度，您可以设置为自己的其他网站或警告页面
				</div>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>
				此功能可以使用微信/QQ扫码快捷登录到后台。微信、QQ可以只绑定其中一个。登录的时候二维码可以同时被微信和QQ扫描。
			</div>
		</div>
		<script>
			$("select[name='thirdlogin_closepwd']").change(function() {
				if ($(this).val() == 1) {
					if ($("select[name='thirdlogin_open']").val() == '0') {
						alert('需要先开启快捷登录，才能关闭账号密码登录');
						$(this).val('0');
						return false;
					}
					if ($("input[name='thirdlogin_wx']").val() == '' && $("input[name='thirdlogin_qq']").val() == '') {
						alert('需要先绑定微信或QQ，才能关闭账号密码登录');
						$(this).val('0');
						return false;
					}
				}
			});
		</script>
        <?php 
} elseif ($mod == "sup_n" && $_POST["do"] == "submit") {
	$sup_bond = $_POST["sup_bond"];
	$sup_reg = $_POST["sup_reg"];
	$pass_sup_bond = $_POST["pass_sup_bond"];
	$sup_audit_free = $_POST["sup_audit_free"];
	$sup_tixian = $_POST["sup_tixian"];
	saveSetting("sup_bond", $sup_bond);
	saveSetting("sup_reg", $sup_reg);
	saveSetting("pass_sup_bond", $pass_sup_bond);
	saveSetting("sup_audit_free", $sup_audit_free);
	saveSetting("sup_tixian", $sup_tixian);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改供货商配置成功！", 1);
	} else {
		showmsg("修改供货商配置失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "sup") {
	?>        <div class="block">
            <div class="block-title">
                <h3 class="panel-title">供货商相关配置</h3>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#fenzhan_func" data-toggle="tab" aria-expanded="true">功能相关</a></li>
                    <li><a href="#fenzhan_tixian" data-toggle="tab" aria-expanded="true">提现相关</a></li>
<!--                    <li><a href="#fenzhan_recharge" data-toggle="tab" aria-expanded="true">充值相关</a></li>-->
<!--                    <li><a href="#fenzhan_workorder" data-toggle="tab" aria-expanded="true">工单相关</a></li>-->
                </ul>
            </div>
            <div class="">

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane fade active in" id="fenzhan_func">
                        <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">上架商品保证金</label>
                                <div class="col-sm-10"><input type="number" name="sup_bond" value="<?php echo $conf["sup_bond"];?>" class="form-control" required placeholder="保证金缴纳达到该数额即可上架商品"/></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">免审上架保证金</label>
                                <div class="col-sm-10"><input type="number" name="pass_sup_bond" value="<?php echo $conf["pass_sup_bond"];?>" class="form-control" required placeholder="保证金缴纳达到该数额即可免审核上架商品"/></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">免审核上架</label>
                                <div class="col-sm-10"><select class="form-control" name="sup_audit_free" default="<?php echo $conf["sup_audit_free"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">自助注册</label>
                                <div class="col-sm-10"><select class="form-control" name="sup_reg" default="<?php echo $conf["sup_reg"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">提现开关</label>
                                <div class="col-sm-10"><select class="form-control" name="sup_tixian" default="<?php echo $conf["sup_tixian"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">联系方式</label>
                                <div class="col-sm-10"><textarea class="form-control" name="sup_report" rows="8" style="width:100%;" placeholder="格式："跳转链接|按钮标题"，如"http://xxx/|联系QQ"，多个联系方式请换行"><?php echo htmlspecialchars($conf["sup_report"]);?></textarea></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">后台公告</label>
                                <div class="col-sm-10"><textarea class="form-control" name="sup_notice" rows="8" style="width:100%;" placeholder=""><?php echo htmlspecialchars($conf["sup_notice"]);?></textarea></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade in" id="fenzhan_tixian">
                        <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">开启提现</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_tixian" default="<?php echo $conf["sup_tixian"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">支付宝提现方式</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_tixian_alipay" default="<?php echo $conf["sup_tixian_alipay"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">微信提现方式</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_tixian_wx" default="<?php echo $conf["sup_tixian_wx"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">QQ提现方式</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_tixian_qq" default="<?php echo $conf["sup_tixian_qq"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">是否启用收款图</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_skimg" default="<?php echo $conf["sup_skimg"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">是否启用代付接口提现</label>
                                <div class="col-sm-9"><select class="form-control" name="sup_daifu" default="<?php echo $conf["sup_daifu"];?>">
                                        <option value="0">关闭</option>
                                        <option value="1">开启</option>
                                    </select></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">提现余额比例</label>
                                <div class="col-sm-9"><input type="text" name="sup_tixian_rate" value="<?php echo $conf["sup_tixian_rate"];?>" class="form-control" placeholder="填写百分数" /></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">提现最低余额</label>
                                <div class="col-sm-9"><input type="text" name="sup_tixian_min" value="<?php echo $conf["sup_tixian_min"];?>" class="form-control" /></div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
                                </div>
                            </div>
                        </form>
                    </div>
<!--                    <div class="tab-pane fade in" id="fenzhan_recharge">-->
<!--                        <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">开启加款卡充值</label>-->
<!--                                <div class="col-sm-9"><select class="form-control" name="sup_jiakuanka" default="--><!--">-->
<!--                                        <option value="0">关闭</option>-->
<!--                                        <option value="1">开启</option>-->
<!--                                    </select><a href="./kmlist.php">加款卡密列表</a></div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">充值赠送规则</label>-->
<!--                                <div class="col-sm-9"><input type="text" name="sup_gift" value="--><!--" class="form-control" placeholder="" />-->
<!--                                    <pre>例如：100:3|200:5|500:10 就是一次性充值100元以上，赠送3%，200元以上赠送5%，500元以上赠送10%（所有符号都是英文状态输入的，不会的请勿乱填！）</pre>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">最低充值金额</label>-->
<!--                                <div class="col-sm-9"><input type="text" name="recharge_min" value="--><!--" class="form-control" placeholder="不填写则不限制" /></div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </form>-->
<!--                    </div>-->
<!---->
<!--                    <div class="tab-pane fade in" id="fenzhan_workorder">-->
<!--                        <form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">开启工单功能</label>-->
<!--                                <div class="col-sm-9"><select class="form-control" name="workorder_open" default="--><!--">-->
<!--                                        <option value="1">开启</option>-->
<!--                                        <option value="0">关闭</option>-->
<!--                                    </select></div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">提交工单可上传图片</label>-->
<!--                                <div class="col-sm-9"><select class="form-control" name="workorder_pic" default="--><!--">-->
<!--                                        <option value="0">关闭</option>-->
<!--                                        <option value="1">开启</option>-->
<!--                                    </select></div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <label class="col-sm-3 control-label">工单可选问题类型</label>-->
<!--                                <div class="col-sm-9"><input type="text" name="workorder_type" value="--><!--" class="form-control" placeholder="多个类型用|隔开" /></div>-->
<!--                            </div>-->
<!--                            <div class="form-group">-->
<!--                                <div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </form>-->
<!--                    </div>-->
                </div>

            </div>
        </div>

        <script>
            $("select[name='fenzhan_buy']").change(function() {
                if ($(this).val() == 1) {
                    $("#frame_set1").css("display", "inherit");
                } else {
                    $("#frame_set1").css("display", "none");
                }
            });
            $("select[name='tixian_limit']").change(function() {
                if ($(this).val() == 1) {
                    $("#frame_set2").css("display", "inherit");
                } else {
                    $("#frame_set2").css("display", "none");
                }
            });
            $("select[name='fenzhan_page']").change(function() {
                if ($(this).val() == 1) {
                    alert('开启后必须保证主站预留域名已正确填写，否则主站将出现404无法访问。注意！！www.qq.com与qq.com是两个域名！！');
                }
            });
        </script>
	<?php 
} elseif ($mod == "account_n" && $_POST["do"] == "submit") {
	if ($admintypeid == 1) {
		$user = $_POST["user"];
		$oldpwd = $_POST["oldpwd"];
		$newpwd = $_POST["newpwd"];
		$newpwd2 = $_POST["newpwd2"];
		if ($user == NULL) {
			showmsg("用户名不能为空！", 3);
		}
		if (!empty($newpwd) && !empty($newpwd2)) {
				if ($oldpwd != $adminuserrow["password"]) {
					showmsg("旧密码不正确！", 3);
				}
				if ($newpwd != $newpwd2) {
					showmsg("两次输入的密码不一致！", 3);
				}
				$sql2 = ",`password`=:newpwd";
			}
			$params = array(':username' => $user, ':id' => $adminuserrow["id"]);
			if(isset($sql2)) {
				$params[':newpwd'] = $newpwd;
			}
			$sql = $DB->exec("UPDATE `pre_account` SET `username`=:username" . $sql2 . " WHERE `id`=:id", $params);
		if ($sql) {
			showmsg("修改成功！请重新登录", 1);
		} else {
			showmsg("修改失败！<br/>" . $DB->error(), 4);
		}
	} else {
		$user = $_POST["user"];
		$oldpwd = $_POST["oldpwd"];
		$newpwd = $_POST["newpwd"];
		$newpwd2 = $_POST["newpwd2"];
		if ($user == NULL) {
			showmsg("用户名不能为空！", 3);
		}
		saveSetting("admin_user", $user);
		if (!empty($newpwd) && !empty($newpwd2)) {
			if ($oldpwd != $conf["admin_pwd"]) {
				showmsg("旧密码不正确！", 3);
			}
			if ($newpwd != $newpwd2) {
				showmsg("两次输入的密码不一致！", 3);
			}
			saveSetting("admin_pwd", $newpwd);
		}
		$ad = $CACHE->clear();
		if ($ad) {
			showmsg("修改成功！请重新登录", 1);
		} else {
			showmsg("修改失败！<br/>" . $DB->error(), 4);
		}
	}
} elseif ($mod == "account") {
	if ($admintypeid == 1) {
		$user = $adminuserrow["username"];
	} else {
		$user = $conf["admin_user"];
	}
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title"><?php echo $admintypeid == 1 ? "员工" : "管理员";?>账号配置</h3>
			</div>
			<div class="">
				<form action="./set.php?mod=account_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-sm-2 control-label">用户名</label>
						<div class="col-sm-10"><input type="text" name="user" value="<?php echo $user;?>" class="form-control" required /></div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">旧密码</label>
						<div class="col-sm-10"><input type="password" name="oldpwd" value="" class="form-control" placeholder="请输入当前的管理员密码" /></div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">新密码</label>
						<div class="col-sm-10"><input type="password" name="newpwd" value="" class="form-control" placeholder="不修改请留空" /></div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">重输密码</label>
						<div class="col-sm-10"><input type="password" name="newpwd2" value="" class="form-control" placeholder="不修改请留空" /></div>
					</div><br />
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
						</div>
					</div>
				</form>
			</div>
			</div>
			
			<!-- 高级防CC功能配置 -->
			<div class="block" style="margin-top: 20px;">
				<div class="block-title">
					<h3 class="panel-title">高级防CC功能设置（IP重定向）</h3>
				</div>
				<div class="">
					<form action="./set.php?mod=defend_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
						<input type="hidden" name="defendid" value="<?php echo CC_Defender;?>" />
						
						<div class="form-group">
							<label class="col-sm-3 control-label">启用高级防CC功能</label>
							<div class="col-sm-9">
								<select class="form-control" name="cc_protect_enabled" default="<?php echo $conf["cc_protect_enabled"];?>">
									<option value="0">关闭</option>
									<option value="1">开启</option>
								</select>
							</div>
						</div><br />
						
						<div class="form-group">
							<label class="col-sm-3 control-label">最大请求数</label>
							<div class="col-sm-9">
								<input type="text" name="cc_max_requests" value="<?php echo isset($conf["cc_max_requests"]) ? $conf["cc_max_requests"] : '30';?>" class="form-control" />
								<span class="help-block">检测时间窗口内允许的最大请求数，默认30</span>
							</div>
						</div><br />
						
						<div class="form-group">
							<label class="col-sm-3 control-label">检测时间窗口（秒）</label>
							<div class="col-sm-9">
								<input type="text" name="cc_check_time" value="<?php echo isset($conf["cc_check_time"]) ? $conf["cc_check_time"] : '30';?>" class="form-control" />
								<span class="help-block">单位：秒，默认30秒</span>
							</div>
						</div><br />
						
						<div class="form-group">
							<label class="col-sm-3 control-label">封禁时长（秒）</label>
							<div class="col-sm-9">
								<input type="text" name="cc_block_time" value="<?php echo isset($conf["cc_block_time"]) ? $conf["cc_block_time"] : '300';?>" class="form-control" />
								<span class="help-block">单位：秒，默认300秒（5分钟）</span>
							</div>
						</div><br />
						
						<div class="form-group">
							<label class="col-sm-3 control-label">重定向网址</label>
							<div class="col-sm-9">
								<input type="text" name="cc_redirect_url" value="<?php echo isset($conf["cc_redirect_url"]) ? $conf["cc_redirect_url"] : 'https://www.baidu.com';?>" class="form-control" />
								<span class="help-block">发现CC攻击时重定向到的网址，默认百度</span>
							</div>
						</div><br />
						
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
								<input type="submit" name="submit" value="保存设置" class="btn btn-primary btn-block" />
							</div>
						</div>
					</form>
				</div>
				<div class="panel-footer">
					<span class="glyphicon glyphicon-info-sign"></span>高级防CC功能说明：<br />
					- 该功能通过检测IP访问频率识别CC攻击<br />
					- 当检测到异常访问时，自动将恶意IP重定向到指定网站<br />
					- 后台页面默认跳过检测，不影响管理员操作<br />
					- 支持搜索引擎爬虫自动识别并放行<br />
					- 采用文件缓存机制，不增加数据库负担<br />
				</div>
			</div>
	<?php 
} elseif ($mod == "site") {
	adminpermission("set", 1);
	?>		<link rel="stylesheet" href="<?php echo $cdnpublic;?>bootstrap-colorpicker/2.5.3/css/bootstrap-colorpicker.min.css">
		<script src="<?php echo $cdnpublic;?>bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
		<style>
			.colorpicker.dropdown-menu {
				-webkit-transform: scale(1) !important;
				transform: scale(1) !important;
				-webkit-transition: none !important;
				transition: none !important;
				min-width: 0 !important
			}

			.lt-ie10 .colorpicker.dropdown-menu.colorpicker-visible {
				display: block !important
			}
		</style>
		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">网站信息配置</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">网站名称</label>
						<div class="col-sm-10"><input type="text" name="sitename" value="<?php echo $conf["sitename"];?>" class="form-control" required /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">网站图标</label>
						<div class="col-sm-10">
							<input type="text" id="favicon_url" name="favicon" value="<?php echo $conf["favicon"];?>" class="form-control" placeholder="输入图标的URL地址，留空则使用默认图标" />
							<span class="help-block">可使用 <a href="https://www.favicon.cc/" target="_blank">favicon.cc</a> 免费生成.ico格式图标，支持.ico、png、jpg、jpeg、gif格式，建议尺寸16x16或32x32像素</span>
							<div id="favicon_preview" style="margin-top:10px;">
							<?php if(!empty($conf['favicon'])){
								echo '<img src="'.htmlspecialchars($conf['favicon']).'" style="max-width:64px;max-height:64px;">';
							}?>
							</div>
							<script type="text/javascript">
							$(document).ready(function(){
								// 当输入框内容变化时，更新预览
								$('#favicon_url').on('input', function(){
									var url = $(this).val();
									if(url){
										$('#favicon_preview').html('<img src="'+url+'" style="max-width:64px;max-height:64px;">');
									}else{
										$('#favicon_preview').html('');
									}
								});
							});
							</script>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">标题栏后缀</label>
						<div class="col-sm-10"><input type="text" name="title" value="<?php echo $conf["title"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">关键字</label>
						<div class="col-sm-10"><input type="text" name="keywords" value="<?php echo $conf["keywords"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">网站描述</label>
						<div class="col-sm-10"><input type="text" name="description" value="<?php echo $conf["description"];?>" class="form-control" /></div>
					</div>
                                        <div class="form-group">
                                                <label class="col-sm-2 control-label">ICP备案号</label>
                                                <div class="col-sm-10"><input type="text" name="icp_number" value="<?php echo htmlspecialchars((string)($conf['icp_number'] ?? ''));?>" class="form-control" placeholder="如：滇ICP备xxxxxxxx号-1；留空则前台不显示" /></div>
                                        </div>
                                        <div class="form-group">
                                                <label class="col-sm-2 control-label">公安备案号</label>
                                                <div class="col-sm-10"><input type="text" name="police_number" value="<?php echo htmlspecialchars((string)($conf['police_number'] ?? ''));?>" class="form-control" placeholder="如：滇公网安备xxxxxxxxxxxxx号；留空则前台不显示" /></div>
                                        </div>
                                        <div class="form-group">
                                                <label class="col-sm-2 control-label">增值电信许可证</label>
                                                <div class="col-sm-10"><input type="text" name="icp_license" value="<?php echo htmlspecialchars((string)($conf['icp_license'] ?? ''));?>" class="form-control" placeholder="如：滇B2-xxxxxxxx；留空则前台不显示" /></div>
                                        </div>
					<div class="form-group">
						<label class="col-sm-2 control-label">首页标题栏只显示后缀</label>
						<div class="col-sm-10"><select class="form-control" name="sitename_hide" default="<?php echo $conf["sitename_hide"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">客服ＱＱ</label>
						<div class="col-sm-10"><input type="text" name="kfqq" value="<?php echo $conf["kfqq"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">客服微信</label>
						<div class="col-sm-10">
							<div class="input-group"><input type="text" name="kfwx" value="<?php echo $conf["kfwx"];?>" class="form-control" placeholder="部分模板才显示" /><span class="input-group-btn"><a href="./set.php?mod=upwxqrcode" class="btn btn-default">上传二维码</a></span></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">下单验证模块</label>
						<div class="col-sm-10"><select class="form-control" name="verify_open" default="<?php echo $conf["verify_open"];?>">
								<option value="1">开启</option>
								<option value="0">关闭</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">显示搜索商品</label>
						<div class="col-sm-10"><select class="form-control" name="search_open" default="<?php echo $conf["search_open"];?>">
								<option value="1">开启</option>
								<option value="0">关闭</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">发卡商品下单标题</label>
						<div class="col-sm-10"><select class="form-control" name="faka_input" default="<?php echo $conf["faka_input"];?>">
								<option value="0">你的邮箱</option>
								<option value="1">手机号码</option>
								<option value="2">你的ＱＱ</option>
								<option value="4">取卡密码</option>
								<option value="5">自定义</option>
								<option value="3">(不填写内容)</option>
							</select></div>
					</div>
					<div class="form-group" id="frame_set3" style="<?php echo $conf["faka_input"] == 5 ? "display:none;" : NULL;?>">
						<label class="col-sm-2 control-label">发卡商品下单标题</label>
						<div class="col-sm-10"><input type="text" name="faka_inputname" value="<?php echo $conf["faka_inputname"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">发卡库存显示</label>
						<div class="col-sm-10"><select class="form-control" name="faka_showleft" default="<?php echo $conf["faka_showleft"];?>">
								<option value="0">精确数量</option>
								<option value="1">大概情况</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">订单详情显示投诉订单按钮</label>
						<div class="col-sm-10"><select class="form-control" name="show_complain" default="<?php echo $conf["show_complain"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">订单详情显示修改密码按钮</label>
						<div class="col-sm-10"><select class="form-control" name="show_changepwd" default="<?php echo $conf["show_changepwd"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">首页显示订单金额统计信息</label>
						<div class="col-sm-10"><select class="form-control" name="hide_tongji" default="<?php echo $conf["hide_tongji"];?>">
								<option value="0">开启</option>
								<option value="1">关闭</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">首页统计数据缓存时间(秒)</label>
						<div class="col-sm-10"><input type="text" name="tongji_time" value="<?php echo $conf["tongji_time"];?>" class="form-control" placeholder="留空或0则不缓存，设置缓存可提升网页打开速度" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">下单黑名单</label>
						<div class="col-sm-10"><input type="text" name="blacklist" value="<?php echo $conf["blacklist"];?>" class="form-control" placeholder="多个账号之间用|隔开" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">网站首页背景</label>
						<div class="col-sm-10"><select class="form-control" name="ui_bing" default="<?php echo $conf["ui_bing"];?>">
								<option value="0">自定义背景图片</option>
								<option value="1">随机美图</option>
								<option value="2">Bing每日壁纸</option>
								<option value="3">渐变背景色</option>
							</select></div>
					</div>
					<div id="frame_set1" style="<?php echo $conf["ui_bing"] == 0 ? "display:none;" : NULL;?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">背景图显示效果</label>
							<div class="col-sm-10"><select class="form-control" name="ui_background" default="<?php echo $conf["ui_background"];?>">
									<option value="0">纵向和横向重复</option>
									<option value="1">横向重复,纵向拉伸</option>
									<option value="2">纵向重复,横向拉伸</option>
									<option value="3">不重复,全屏拉伸</option>
								</select><a href="./set.php?mod=upbgimg">点此上传背景图</a></div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label">首页下单显示模式</label>
						<div class="col-sm-10"><select class="form-control" name="ui_shop" default="<?php echo $conf["ui_shop"];?>">
								<option value="0">经典模式</option>
								<option value="1">分类图片宫格</option>
								<option value="2">分类图片列表</option>
								<option value="3">分类图片宫格2</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">首页显示文章条数</label>
						<div class="col-sm-10"><input type="number" name="articlenum" value="<?php echo $conf["articlenum"];?>" class="form-control" placeholder="0或留空则不显示文章列表" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">分站后台界面风格</label>
						<div class="col-sm-10"><select class="form-control" name="ui_user" default="<?php echo $conf["ui_user"];?>">
								<option value="0">明亮风格（默认）</option>
								<option value="1">黑色风格</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">开启购物车功能</label>
						<div class="col-sm-10"><select class="form-control" name="shoppingcart" default="<?php echo $conf["shoppingcart"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">网站创建时间</label>
						<div class="col-sm-10"><input type="date" name="build" value="<?php echo $conf["build"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">QQ等级代挂开通网址</label>
						<div class="col-sm-10"><input type="text" name="daiguaurl" value="<?php echo $conf["daiguaurl"];?>" class="form-control" placeholder="填写后将在首页显示代挂功能，没有请留空" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">手机QQ微信打开网站跳转浏览器</label>
						<div class="col-sm-10"><select class="form-control" name="qqjump" default="<?php echo $conf["qqjump"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">API对接密钥</label>
						<div class="col-sm-10"><input type="text" name="apikey" value="<?php echo $conf["apikey"];?>" class="form-control" placeholder="用于下单软件，随便填写即可" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">商品分类不可售地区设置</label>
						<div class="col-sm-10"><select class="form-control" name="classblock" default="<?php echo $conf["classblock"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
								<option value="2">仅电脑访问开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">开启只能登录下单</label>
						<div class="col-sm-10"><select class="form-control" name="forcelogin" default="<?php echo $conf["forcelogin"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">开启只能使用余额下单</label>
						<div class="col-sm-10"><select class="form-control" name="forcermb" default="<?php echo $conf["forcermb"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">开启登录后才能访问首页</label>
						<div class="col-sm-10"><select class="form-control" name="forceloginhome" default="<?php echo $conf["forceloginhome"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">开启用户后台批量下单</label>
						<div class="col-sm-10"><select class="form-control" name="openbatchorder" default="<?php echo $conf["openbatchorder"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">只能查询本人订单</label>
						<div class="col-sm-10"><select class="form-control" name="queryorderlimit" default="<?php echo $conf["queryorderlimit"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select>
							<font color="green">开启后首页查询订单只能查到本人的，会根据浏览器缓存判断，会导致未注册用户1~7天前的订单无法查询</font>
						</div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label">商品简介编辑富文本输入框</label>
					<div class="col-sm-10"><select class="form-control" name="shopdesc_editor" default="<?php echo $conf["shopdesc_editor"];?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">开启卡密兑换功能</label>
					<div class="col-sm-10"><select class="form-control" name="iskami" default="<?php echo isset($conf["iskami"]) ? $conf["iskami"] : 0;?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select>
						<font color="green">开启后用户可以使用卡密兑换商品</font>
					</div>
				</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">待处理和异常订单允许自助退款</label>
						<div class="col-sm-10"><select class="form-control" name="selfrefund" default="<?php echo $conf["selfrefund"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
					<label class="col-sm-2 control-label">硬防墙</label>
					<div class="col-sm-10"><select class="form-control" name="parapet" default="<?php echo $conf["parapet"];?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select>
						<font color="green">不影响SEO</font>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">免费商品登录领取</label>
					<div class="col-sm-10"><select class="form-control" name="free_goods_login_receive" default="<?php echo isset($conf["free_goods_login_receive"]) ? $conf["free_goods_login_receive"] : 0;?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select>
						<font color="green">开启后免费商品需要登录后才能领取</font>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">获取空间说说列表接口</label>
					<div class="col-sm-10"><input type="text" name="sslist_api" value="<?php echo $conf["sslist_api"];?>" class="form-control" placeholder="留空则不启用" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">获取空间日志列表接口</label>
					<div class="col-sm-10"><input type="text" name="rzlist_api" value="<?php echo $conf["rzlist_api"];?>" class="form-control" placeholder="留空则不启用" /></div>
				</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
					<div class="form-group">
						高级功能：<a href="./set.php?mod=cleanbom">清理BOM头部</a>｜<a href="./set.php?mod=defend">防CC模块设置</a>｜<a href="./set.php?mod=proxy">代理服务器设置</a>
					</div>
				</form>
			</div>
		</div>
		<script>
			$("select[name='ui_bing']").change(function() {
				if ($(this).val() == 0) {
					$("#frame_set1").css("display", "inherit");
					$("#frame_set2").css("display", "none");
				} else if ($(this).val() == 3) {
					$("#frame_set1").css("display", "none");
					$("#frame_set2").css("display", "inherit");
				} else {
					$("#frame_set1").css("display", "none");
					$("#frame_set2").css("display", "none");
				}
			});
			$("select[name='faka_input']").change(function() {
				if ($(this).val() == 5) {
					$("#frame_set3").css("display", "inherit");
				} else {
					$("#frame_set3").css("display", "none");
				}
			});
			$('.input-colorpicker').colorpicker({
				format: 'hex'
			});
		</script>
	<?php 
} elseif ($mod == "defend_n" && $_POST["do"] == "submit") {
	adminpermission("set", 1);
	$defendid = $_POST["defendid"];
	$file = "<?php\r\n//防CC模块设置\r\ndefine('CC_Defender', " . $defendid . ");\r\n?>";
	file_put_contents(SYSTEM_ROOT . "base.php", $file);
	
	// 保存高级防CC设置
	saveSetting("cc_protect_enabled", $_POST["cc_protect_enabled"]);
	saveSetting("cc_max_requests", $_POST["cc_max_requests"]);
	saveSetting("cc_check_time", $_POST["cc_check_time"]);
	saveSetting("cc_block_time", $_POST["cc_block_time"]);
	saveSetting("cc_redirect_url", $_POST["cc_redirect_url"]);
	
	$ad = $CACHE->clear();
	showmsg("修改成功！", 1);
} elseif ($mod == "defend") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">防CC模块设置</h3>
			</div>
			<div class="">
				<form action="./set.php?mod=defend_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-sm-2 control-label">CC防护等级</label>
						<div class="col-sm-10"><select class="form-control" name="defendid" default="<?php echo CC_Defender;?>">
								<option value="0">关闭</option>
								<option value="1">低(推荐)</option>
								<option value="2">中</option>
								<option value="3">高</option>
								<option value="4">滑动验证码</option>
							</select></div>
					</div><br />
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>CC防护说明<br />
				滑动验证码：全局开启滑动验证码，验证通过后才能访问<br />
				高：全局使用防CC，会影响网站APP和对接软件的正常使用<br />
				中：会影响搜索引擎的收录，建议仅在正在受到CC攻击且防御不佳时开启<br />
				低：用户首次访问进行验证（推荐）<br />
			</div>
		</div>
		<br/>
		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">高级防CC功能设置（IP重定向）</h3>
			</div>
			<div class="">
				<form action="./set.php?mod=defend_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-sm-2 control-label">启用高级防CC</label>
						<div class="col-sm-10"><select class="form-control" name="cc_protect_enabled" default="<?php echo isset($conf['cc_protect_enabled']) ? $conf['cc_protect_enabled'] : '0';?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select>
							<font color="green">开启后会自动检测异常IP访问频率并执行重定向</font>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">最大请求数</label>
						<div class="col-sm-10"><input type="number" name="cc_max_requests" value="<?php echo isset($conf['cc_max_requests']) ? $conf['cc_max_requests'] : '30';?>" class="form-control" />
							<font color="green">检测时间窗口内允许的最大请求数</font>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">检测时间窗口</label>
						<div class="col-sm-10"><input type="number" name="cc_check_time" value="<?php echo isset($conf['cc_check_time']) ? $conf['cc_check_time'] : '30';?>" class="form-control" />
							<font color="green">单位：秒</font>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">封禁时长</label>
						<div class="col-sm-10"><input type="number" name="cc_block_time" value="<?php echo isset($conf['cc_block_time']) ? $conf['cc_block_time'] : '300';?>" class="form-control" />
							<font color="green">单位：秒</font>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">重定向网址</label>
						<div class="col-sm-10"><input type="text" name="cc_redirect_url" value="<?php echo isset($conf['cc_redirect_url']) ? $conf['cc_redirect_url'] : 'https://www.baidu.com';?>" class="form-control" />
							<font color="green">被攻击时将恶意IP重定向到的网址，请输入完整URL（包含http://或https://）</font>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="保存设置" class="btn btn-primary btn-block" /><br />
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>高级防CC功能说明<br />
				此功能可自动检测异常IP访问频率，当发现恶意访问时将其重定向到指定网站<br />
				建议设置合理的参数，避免误拦截正常用户<br />
				默认将恶意IP重定向到百度，您可以设置为自己的其他网站或警告页面
			</div>
		</div>
	<?php 
} elseif ($mod == "proxy_n" && $_POST["do"] == "submit") {
	adminpermission("set", 1);
	$proxy = $_POST["proxy"];
	$server_hash = md5($_SERVER["SERVER_SOFTWARE"] . $_SERVER["SERVER_ADDR"]);
	$proxy_server = $_POST["proxy_server"];
	$proxy_port = $_POST["proxy_port"];
	$proxy_user = $_POST["proxy_user"];
	$proxy_pwd = $_POST["proxy_pwd"];
	$proxy_port = $_POST["proxy_port"];
	saveSetting("proxy", $proxy);
	saveSetting("server_hash", $server_hash);
	saveSetting("proxy_server", $proxy_server);
	saveSetting("proxy_port", $proxy_port);
	saveSetting("proxy_user", $proxy_user);
	saveSetting("proxy_pwd", $proxy_pwd);
	saveSetting("proxy_type", $proxy_type);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "proxy") {
	adminpermission("set", 1);
	$server_hash = md5($_SERVER["SERVER_SOFTWARE"] . $_SERVER["SERVER_ADDR"]);
	if ($server_hash === $conf["server_hash"] && $conf["proxy"] == 1) {
		$is_proxy = 1;
	} else {
		$is_proxy = 0;
	}
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">代理服务器设置</h3>
			</div>
			<div class="">
				<form action="./set.php?mod=proxy_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<?php 
	if (check_china() == true) {
		?>						<div class="alert alert-info">你当前的服务器位于国内，无需使用代理服务器。</div>
					<?php 
	} else {
		?>						<div class="form-group">
							<label class="col-sm-2 control-label">代理服务器开关</label>
							<div class="col-sm-10"><select class="form-control" name="proxy" default="<?php echo $conf["proxy"];?>">
									<option value="0">关闭</option>
									<option value="1">开启</option>
								</select></div>
						</div><br />
						<div id="frame_set1" style="display:none;">
							<div class="form-group">
								<label class="col-sm-2 control-label">代理IP</label>
								<div class="col-sm-10"><input type="text" name="proxy_server" value="<?php echo $conf["proxy_server"];?>" class="form-control" /></div>
							</div><br />
							<div class="form-group">
								<label class="col-sm-2 control-label">代理端口</label>
								<div class="col-sm-10"><input type="text" name="proxy_port" value="<?php echo $conf["proxy_port"];?>" class="form-control" /></div>
							</div><br />
							<div class="form-group">
								<label class="col-sm-2 control-label">代理账号</label>
								<div class="col-sm-10"><input type="text" name="proxy_user" value="<?php echo $conf["proxy_user"];?>" class="form-control" /></div>
							</div><br />
							<div class="form-group">
								<label class="col-sm-2 control-label">代理密码</label>
								<div class="col-sm-10"><input type="text" name="proxy_pwd" value="<?php echo $conf["proxy_pwd"];?>" class="form-control" /></div>
							</div><br />
							<div class="form-group">
								<label class="col-sm-2 control-label">代理协议</label>
								<div class="col-sm-10"><select class="form-control" name="proxy_type" default="<?php echo $conf["proxy_type"];?>">
										<option value="http">HTTP</option>
										<option value="https">HTTPS</option>
										<option value="sock4">SOCK4</option>
										<option value="sock5">SOCK5</option>
									</select></div>
							</div><br />
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary form-control" /><br />
							</div>
						</div>
					<?php 
	}
	?>				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>本功能适用于国外服务器对接一些屏蔽国外访问的社区或者卡盟，开启后使用国内代理服务器进行对接。<br />
				自定义代理可以使用Windows服务器+CCProxy软件搭建<br />
				<b>注意：如果网站更换主机之后需要重新修改当前配置。</b><br />
			</div>
		</div>
		<script>
			$("select[name='proxy']").change(function() {
				if ($(this).val() == 1) {
					$("#frame_set1").css("display", "inherit");
				} else {
					$("#frame_set1").css("display", "none");
				}
			});
		</script>
	<?php 
} elseif ($mod == "fenzhan") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">分站相关配置</h3>
				<ul class="nav nav-tabs">
					<li class="active"><a href="#fenzhan_func" data-toggle="tab" aria-expanded="true">功能相关</a></li>
					<li><a href="#fenzhan_tixian" data-toggle="tab" aria-expanded="true">提现相关</a></li>
					<li><a href="#fenzhan_recharge" data-toggle="tab" aria-expanded="true">充值相关</a></li>
					<li><a href="#fenzhan_workorder" data-toggle="tab" aria-expanded="true">工单相关</a></li>
				</ul>
			</div>
			<div class="">

				<div id="myTabContent" class="tab-content">
					<div class="tab-pane fade active in" id="fenzhan_func">
						<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
							<div class="form-group">
								<label class="col-sm-3 control-label">分站排行榜</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_rank" default="<?php echo $conf["fenzhan_rank"];?>">
										<option value="1">开启</option>
										<option value="0">关闭</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">用户注册功能</label>
								<div class="col-sm-9"><select class="form-control" name="user_open" default="<?php echo $conf["user_open"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">新注册用户价格等级</label>
								<div class="col-sm-9"><select class="form-control" name="user_level" default="<?php echo $conf["user_level"];?>">
										<option value="0">商品售价</option>
										<option value="1">普及版价格</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">自助开通分站</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_buy" default="<?php echo $conf["fenzhan_buy"];?>">
										<option value="1">开启</option>
										<option value="0">关闭</option>
									</select></div>
							</div>
							<div id="frame_set1" style="<?php echo $conf["fenzhan_buy"] == 1 ? NULL : "display:none;";?>">
								<div class="form-group">
									<label class="col-sm-3 control-label">开通分站界面提示语</label>
									<div class="col-sm-9"><select class="form-control" name="fenzhan_regalert" default="<?php echo $conf["fenzhan_regalert"];?>">
											<option value="0">关闭</option>
											<option value="1">开启</option>
										</select></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">二级域名随机生成</label>
									<div class="col-sm-9"><select class="form-control" name="fenzhan_regrand" default="<?php echo $conf["fenzhan_regrand"];?>">
											<option value="0">关闭</option>
											<option value="1">开启</option>
										</select></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">分站默认有效期</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_expiry" value="<?php echo $conf["fenzhan_expiry"];?>" class="form-control" />
										<pre>填写月数，如果为0则是永久不过期</pre>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">专业版价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_price2" value="<?php echo $conf["fenzhan_price2"];?>" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">普及版价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_price" value="<?php echo $conf["fenzhan_price"];?>" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">专业版成本价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_cost2" value="<?php echo $conf["fenzhan_cost2"];?>" class="form-control" />
										<pre>注意：分站成本价格请勿低于初始赠送余额</pre>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">普及版成本价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_cost" value="<?php echo $conf["fenzhan_cost"];?>" class="form-control" />
										<pre>注意：分站成本价格请勿低于初始赠送余额</pre>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">初始赠送余额</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_free" value="<?php echo $conf["fenzhan_free"];?>" class="form-control" /></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">普及版升级价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_upgrade" value="<?php echo $conf["fenzhan_upgrade"];?>" class="form-control" placeholder="不填写则不开启自助升级专业版功能" /></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">自助修改域名价格</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_editd" value="<?php echo $conf["fenzhan_editd"];?>" class="form-control" placeholder="不填写则不开启自助修改域名" /></div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">分站可选择域名</label>
									<div class="col-sm-9"><input type="text" name="fenzhan_domain" value="<?php echo $conf["fenzhan_domain"];?>" class="form-control" />
										<pre>多个域名用,隔开！</pre>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">主站预留域名</label>
								<div class="col-sm-9"><input type="text" name="fenzhan_remain" value="<?php echo $conf["fenzhan_remain"];?>" class="form-control" />
									<pre>主站预留域名无法被分站绑定，多个域名用,隔开！</pre>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">未绑定域名显示404页面</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_page" default="<?php echo $conf["fenzhan_page"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">允许分站修改公告等排版</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_edithtml" default="<?php echo $conf["fenzhan_edithtml"];?>">
										<option value="1">开启</option>
										<option value="0">关闭</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">分站可更换首页模板</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_template" default="<?php echo $conf["fenzhan_template"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">允许专业版后台添加分站</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_adds" default="<?php echo $conf["fenzhan_adds"];?>">
										<option value="1">开启</option>
										<option value="0">关闭</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">允许分站设置自己客服QQ</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_kfqq" default="<?php echo $conf["fenzhan_kfqq"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">分站设置商品价格限制</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_pricelimit" default="<?php echo $conf["fenzhan_pricelimit"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select>
									<font color="green">开启后分站设置商品售价不能超过主站售价的2倍，低于1元的商品售价不能超过2元。</font>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane fade in" id="fenzhan_tixian">
						<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
							<div class="form-group">
								<label class="col-sm-3 control-label">开启提现</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_tixian" default="<?php echo $conf["fenzhan_tixian"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">支付宝提现方式</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_tixian_alipay" default="<?php echo $conf["fenzhan_tixian_alipay"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">微信提现方式</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_tixian_wx" default="<?php echo $conf["fenzhan_tixian_wx"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">QQ提现方式</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_tixian_qq" default="<?php echo $conf["fenzhan_tixian_qq"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">是否启用收款图</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_skimg" default="<?php echo $conf["fenzhan_skimg"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">是否启用代付接口提现</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_daifu" default="<?php echo $conf["fenzhan_daifu"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">提现余额比例</label>
								<div class="col-sm-9"><input type="text" name="tixian_rate" value="<?php echo $conf["tixian_rate"];?>" class="form-control" placeholder="填写百分数" /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">提现最低余额</label>
								<div class="col-sm-9"><input type="text" name="tixian_min" value="<?php echo $conf["tixian_min"];?>" class="form-control" /></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">仅限提成部分可提现</label>
								<div class="col-sm-9"><select class="form-control" name="tixian_limit" default="<?php echo $conf["tixian_limit"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select>
									<font color="green">开启后只有提成才可以提现，充值以及赠送的余额无法提现</font>
								</div>
							</div>
							<div id="frame_set2" style="<?php echo $conf["tixian_limit"] == 1 ? "display:none;" : NULL;?>">
								<div class="form-group">
									<label class="col-sm-3 control-label">提成收入多少天后可提现</label>
									<div class="col-sm-9"><input type="text" name="tixian_days" value="<?php echo $conf["tixian_days"];?>" class="form-control" placeholder="留空或0为可立即提现" />
										<font color="green">0为可立即提现，大于0则需要额外监控提成统计来实现提成入账</font>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
								</div>
							</div>
						</form>
					</div>
					<div class="tab-pane fade in" id="fenzhan_recharge">
						<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
							<div class="form-group">
								<label class="col-sm-3 control-label">开启加款卡充值</label>
								<div class="col-sm-9"><select class="form-control" name="fenzhan_jiakuanka" default="<?php echo $conf["fenzhan_jiakuanka"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select><a href="./kmlist.php">加款卡密列表</a></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">充值赠送规则</label>
								<div class="col-sm-9"><input type="text" name="fenzhan_gift" value="<?php echo $conf["fenzhan_gift"];?>" class="form-control" placeholder="" />
									<pre>例如：100:3|200:5|500:10 就是一次性充值100元以上，赠送3%，200元以上赠送5%，500元以上赠送10%（所有符号都是英文状态输入的，不会的请勿乱填！）</pre>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">最低充值金额</label>
								<div class="col-sm-9"><input type="text" name="recharge_min" value="<?php echo $conf["recharge_min"];?>" class="form-control" placeholder="不填写则不限制" /></div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
								</div>
							</div>
						</form>
					</div>

					<div class="tab-pane fade in" id="fenzhan_workorder">
						<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
							<div class="form-group">
								<label class="col-sm-3 control-label">开启工单功能</label>
								<div class="col-sm-9"><select class="form-control" name="workorder_open" default="<?php echo $conf["workorder_open"];?>">
										<option value="1">开启</option>
										<option value="0">关闭</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">提交工单可上传图片</label>
								<div class="col-sm-9"><select class="form-control" name="workorder_pic" default="<?php echo $conf["workorder_pic"];?>">
										<option value="0">关闭</option>
										<option value="1">开启</option>
									</select></div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">工单可选问题类型</label>
								<div class="col-sm-9"><input type="text" name="workorder_type" value="<?php echo $conf["workorder_type"];?>" class="form-control" placeholder="多个类型用|隔开" /></div>
							</div>
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
								</div>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>

		<script>
			$("select[name='fenzhan_buy']").change(function() {
				if ($(this).val() == 1) {
					$("#frame_set1").css("display", "inherit");
				} else {
					$("#frame_set1").css("display", "none");
				}
			});
			$("select[name='tixian_limit']").change(function() {
				if ($(this).val() == 1) {
					$("#frame_set2").css("display", "inherit");
				} else {
					$("#frame_set2").css("display", "none");
				}
			});
			$("select[name='fenzhan_page']").change(function() {
				if ($(this).val() == 1) {
					alert('开启后必须保证主站预留域名已正确填写，否则主站将出现404无法访问。注意！！www.qq.com与qq.com是两个域名！！');
				}
			});
		</script>
	<?php 
} elseif ($mod == "gonggao") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">网站公告配置</h3>
			</div>
			<div class="panel-body">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
					<div class="form-group">
						<label>首页公告</label><br />
						<textarea class="form-control" name="anounce" rows="8" style="width:100%;"><?php echo htmlspecialchars($conf["anounce"]);?></textarea>
					</div>
					<div class="form-group">
						<label>首页弹出公告</label>
						<select class="form-control" name="modal_show_type" style="width:auto;display:inline;margin-left:10px;" default="<?php echo isset($conf['modal_show_type']) ? $conf['modal_show_type'] : '0';?>">
							<option value="0">每次进网站都弹</option>
							<option value="1">只弹一次</option>
						</select><br />
						<textarea class="form-control" name="modal" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["modal"]);?></textarea>
					</div>
					<div class="form-group">
						<label>站点工具/友情链接（部分模板显示）</label><br />
						<textarea class="form-control" name="bottom" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["bottom"]);?></textarea>
					</div>
					<div class="form-group">
						<label>在线下单提示（部分模板显示）</label><br />
						<textarea class="form-control" name="alert" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["alert"]);?></textarea>
					</div>
					<div class="form-group">
						<label>订单查询页面公告</label><br />
						<textarea class="form-control" name="gg_search" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["gg_search"]);?></textarea>
					</div>
					<div class="form-group">
						<label>分站后台公告</label><br />
						<textarea class="form-control" name="gg_panel" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["gg_panel"]);?></textarea>
					</div>
					<div class="form-group">
						<label>所有分站显示首页公告</label><br />
						<textarea class="form-control" name="gg_announce" rows="5" placeholder="此处公告内容将在所有分站首页公告显示。顺序是先显示此公告再显示分站自定义公告" style="width:100%;"><?php echo htmlspecialchars($conf["gg_announce"]);?></textarea>
					</div>
					<div class="form-group">
						<label>首页底部排版</label><br />
						<textarea class="form-control" name="footer" rows="3" style="width:100%;" placeholder="可用于统计代码或备案号等"><?php echo htmlspecialchars($conf["footer"]);?></textarea>
					</div>
					<div class="form-group">
						<label>支付方式选择页面提示</label><br />
						<textarea class="form-control" name="paymsg" rows="3" style="width:100%;" placeholder=""><?php echo htmlspecialchars($conf["paymsg"]);?></textarea>
					</div>
					<div class="form-group">
						<label>APP下载地址</label><br />
						<input type="text" name="appurl" value="<?php echo $conf["appurl"];?>" class="form-control" placeholder="没有请留空" />
					</div>
					<div class="form-group">
						<label>APP启动弹出内容</label><br />
						<textarea class="form-control" name="appalert" rows="3"><?php echo htmlspecialchars($conf["appalert"]);?></textarea>
					</div>
					<div class="form-group">
						<label>首页背景音乐</label><br />
						<input type="text" name="musicurl" value="<?php echo $conf["musicurl"];?>" class="form-control" placeholder="填写音乐的URL" />
					</div>
					<div class="form-group">
						<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>
				实用工具：<a href="set.php?mod=copygg">一键复制其他站点排版</a>｜<a href="http://www.runoob.com/runcode" target="_blank" rel="noreferrer">HTML在线测试</a>｜<a href="https://sm.ms/" target="_blank" rel="noreferrer">SM.MS图床</a>｜<a href="https://imgbb.com/" target="_blank" rel="noreferrer">ImgBB图床</a>｜<a href="https://link.hhtjim.com/" target="_blank" rel="noreferrer">音乐外链1</a>｜<a href="http://www.musictool.top/" target="_blank" rel="noreferrer">音乐外链2</a>
			</div>
		</div>
	<?php 
} elseif ($mod == "copygg_n") {
	$url = $_POST["url"];
	$content = $_POST["content"];
	$url_arr = parse_url($url);
	if ($url_arr["host"] == $_SERVER["HTTP_HOST"]) {
		showmsg("无法自己复制自己", 3);
	}
	$data = get_curl($url . "api.php?act=siteinfo");
	$arr = json_decode($data, true);
	if (array_key_exists("sitename", $arr)) {
		if (in_array("anounce", $content)) {
			saveSetting("anounce", str_replace($arr["kfqq"], $conf["kfqq"], $arr["anounce"]));
		}
		if (in_array("modal", $content)) {
			saveSetting("modal", str_replace($arr["kfqq"], $conf["kfqq"], $arr["modal"]));
		}
		if (in_array("bottom", $content)) {
			saveSetting("bottom", str_replace($arr["kfqq"], $conf["kfqq"], $arr["bottom"]));
		}
		if (in_array("alert", $content)) {
			saveSetting("alert", str_replace($arr["kfqq"], $conf["kfqq"], $arr["alert"]));
		}
		if (in_array("gg_search", $content)) {
			saveSetting("gg_search", str_replace($arr["kfqq"], $conf["kfqq"], $arr["gg_search"]));
		}
		if (in_array("gg_panel", $content)) {
			saveSetting("gg_panel", str_replace($arr["kfqq"], $conf["kfqq"], $arr["gg_panel"]));
		}
		$ad = $CACHE->clear();
		if ($ad) {
			showmsg("修改成功！", 1);
		} else {
			showmsg("修改失败！<br/>" . $DB->error(), 4);
		}
	} else {
		showmsg("获取数据失败，对方网站无法连接或存在金盾或云锁等防火墙。", 4);
	}
} elseif ($mod == "copygg") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">一键复制其他站点排版</h3>
			</div>
			<div class="">
				<form action="./set.php?mod=copygg_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-sm-2 control-label">站点URL</label>
						<div class="col-sm-10"><input type="text" name="url" value="" class="form-control" placeholder="http://www.qq.com/" required /></div>
					</div><br />
					<div class="form-group">
						<label class="col-sm-2 control-label">复制内容</label>
						<div class="col-sm-10"><label><input name="content[]" type="checkbox" value="anounce" checked /> 首页公告</label><br /><label><input name="content[]" type="checkbox" value="modal" checked /> 弹出公告</label><br /><label><input name="content[]" type="checkbox" value="bottom" checked /> 底部排版</label><br /><label><input name="content[]" type="checkbox" value="alert" checked /> 下单提示</label><br /><label><input name="content[]" type="checkbox" value="gg_search" checked /> 订单查询公告</label><br /><label><input name="content[]" type="checkbox" value="gg_panel" checked /> 分站后台公告</label></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php 
} elseif ($mod == "mail") {
	adminpermission("set", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">消息提醒设置</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">默认提醒方式</label>
						<div class="col-sm-10"><select class="form-control" name="message_type" default="<?php echo $conf["message_type"];?>">
								<option value="0">邮件</option>
								<option value="1">微信</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">对接下单失败提醒</label>
						<div class="col-sm-10"><select class="form-control" name="message_duijie" default="<?php echo $conf["message_duijie"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">全部商品下单提醒</label>
						<div class="col-sm-10"><select class="form-control" name="message_buy" default="<?php echo $conf["message_buy"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
								<option value="2">开启（不包括免费商品）</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">新工单提醒</label>
						<div class="col-sm-10"><select class="form-control" name="message_workorder" default="<?php echo $conf["message_workorder"];?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">发信邮箱设置</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">发信模式</label>
						<div class="col-sm-10"><select class="form-control" name="mail_cloud" default="<?php echo $conf["mail_cloud"];?>">
								<option value="0">SMTP发信</option>
								<option value="1">搜狐Sendcloud</option>
								<option value="2">阿里云邮件推送</option>
							</select></div>
					</div>
					<div id="frame_set1" style="<?php echo $conf["mail_cloud"] == 0 ? NULL : "display:none;";?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP服务器</label>
							<div class="col-sm-10"><input type="text" name="mail_smtp" value="<?php echo $conf["mail_smtp"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">SMTP端口</label>
							<div class="col-sm-10"><input type="text" name="mail_port" value="<?php echo $conf["mail_port"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">邮箱账号</label>
							<div class="col-sm-10"><input type="text" name="mail_name" value="<?php echo $conf["mail_name"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">邮箱密码</label>
							<div class="col-sm-10"><input type="text" name="mail_pwd" value="<?php echo $conf["mail_pwd"];?>" class="form-control" /></div>
						</div>
					</div>
					<div id="frame_set2" style="<?php echo $conf["mail_cloud"] >= 1 ? NULL : "display:none;";?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">API_USER</label>
							<div class="col-sm-10"><input type="text" name="mail_apiuser" value="<?php echo $conf["mail_apiuser"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">API_KEY</label>
							<div class="col-sm-10"><input type="text" name="mail_apikey" value="<?php echo $conf["mail_apikey"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">发信邮箱</label>
							<div class="col-sm-10"><input type="text" name="mail_name2" value="<?php echo $conf["mail_name2"];?>" class="form-control" /></div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">收信邮箱</label>
						<div class="col-sm-10"><input type="text" name="mail_recv" value="<?php echo $conf["mail_recv"];?>" class="form-control" placeholder="不填默认为发信邮箱" /></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br /> </div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>
				此功能为用户下单时给自己发邮件提醒以及发卡商品发送给用户的邮件。<br />使用普通模式发信时，建议使用QQ邮箱，SMTP服务器smtp.qq.com，端口465或587，密码不是QQ密码也不是邮箱独立密码，是QQ邮箱设置界面生成的<a href="https://service.mail.qq.com/cgi-bin/help?subtype=1&&no=1001256&&id=28" target="_blank" rel="noreferrer">授权码</a>。<br />阿里云邮件推送：<a href="https://www.aliyun.com/product/directmail" target="_blank" rel="noreferrer">点此进入</a>｜<a href="https://usercenter.console.aliyun.com/#/manage/ak" target="_blank" rel="noreferrer">获取AK/SK</a>
			</div>
		</div>
		<script>
			$("select[name='mail_cloud']").change(function() {
				if ($(this).val() == 0) {
					$("#frame_set1").css("display", "inherit");
					$("#frame_set2").css("display", "none");
				} else {
					$("#frame_set1").css("display", "none");
					$("#frame_set2").css("display", "inherit");
				}
			});
		</script>
		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">微信消息设置</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">微信消息接口</label>
						<div class="col-sm-10"><select class="form-control" name="wechat_api" default="<?php echo $conf["wechat_api"];?>">
								<option value="0">ServerChan(ftqq)</option>
								<option value="1">WxPusher</option>
							</select></div>
					</div>
					<div id="frame_set3" style="<?php echo $conf["wechat_api"] == 0 ? "display:none;" : NULL;?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">SCKEY</label>
							<div class="col-sm-10"><input type="text" name="wechat_sckey" value="<?php echo $conf["wechat_sckey"];?>" class="form-control" /></div>
						</div>
					</div>
					<div id="frame_set4" style="<?php echo $conf["wechat_api"] == 1 ? "display:none;" : NULL;?>">
						<div class="form-group">
							<label class="col-sm-2 control-label">appToken</label>
							<div class="col-sm-10"><input type="text" name="wechat_apptoken" value="<?php echo $conf["wechat_apptoken"];?>" class="form-control" /></div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">用户UID</label>
							<div class="col-sm-10"><input type="text" name="wechat_appuid" value="<?php echo $conf["wechat_appuid"];?>" class="form-control" /></div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span><br />
				<b>ServerChan(ftqq)：</b>进入 <a href="http://sc.ftqq.com/" target="_blank" rel="noopener noreferrer">http://sc.ftqq.com/</a> ，登录账号 -> 绑定自己的微信号 -> 获取到SCKEY填写到上方输入框！<br />
				<b>WxPusher：</b><a href="https://wxpusher.zjiecode.com/admin/" target="_blank" rel="noopener noreferrer">点此进入</a> ，注册并且创建应用 -> 将appToken填写到上方输入框 -> 扫码关注应用 -> 在用户列表查看自己的UID填写到上方输入框<br />
			</div>
		</div>
		<script>
			$("select[name='wechat_api']").change(function() {
				if ($(this).val() == 0) {
					$("#frame_set3").css("display", "inherit");
					$("#frame_set4").css("display", "none");
				} else {
					$("#frame_set3").css("display", "none");
					$("#frame_set4").css("display", "inherit");
				}
			});
		</script>
	<?php 
} elseif ($mod == "alipay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$alipay_appid = $_POST["alipay_appid"];
	$alipay_publickey = $_POST["alipay_publickey"];
	$alipay_privatekey = $_POST["alipay_privatekey"];
	saveSetting("alipay_appid", $alipay_appid);
	saveSetting("alipay_publickey", $alipay_publickey);
	saveSetting("alipay_privatekey", $alipay_privatekey);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "alipay2_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$alipay_getway = $_POST["alipay_getway"];
	$alipay_pid = $_POST["alipay_pid"];
	$alipay_key = $_POST["alipay_key"];
	saveSetting("alipay_getway", $alipay_getway);
	saveSetting("alipay_pid", $alipay_pid);
	saveSetting("alipay_key", $alipay_key);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "qqpay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$qqpay_mchid = $_POST["qqpay_mchid"];
	$qqpay_key = $_POST["qqpay_key"];
	saveSetting("qqpay_mchid", $qqpay_mchid);
	saveSetting("qqpay_key", $qqpay_key);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "wxpay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$wxpay_appid = $_POST["wxpay_appid"];
	$wxpay_mchid = $_POST["wxpay_mchid"];
	$wxpay_key = $_POST["wxpay_key"];
	$wxpay_appsecret = $_POST["wxpay_appsecret"];
	$wxpay_domain = $_POST["wxpay_domain"];
	saveSetting("wxpay_appid", $wxpay_appid);
	saveSetting("wxpay_mchid", $wxpay_mchid);
	saveSetting("wxpay_key", $wxpay_key);
	saveSetting("wxpay_appsecret", $wxpay_appsecret);
	saveSetting("wxpay_domain", $wxpay_domain);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "codepay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$codepay_id = $_POST["codepay_id"];
	$codepay_key = $_POST["codepay_key"];
	saveSetting("codepay_id", $codepay_id);
	saveSetting("codepay_key", $codepay_key);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "micropay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$micropayapi = $_POST["micropayapi"];
	$micropay_pid = $_POST["micropay_pid"];
	$micropay_key = $_POST["micropay_key"];
	$micropay_mchid = $_POST["micropay_mchid"];
	saveSetting("micropayapi", $micropayapi);
	saveSetting("micropay_pid", $micropay_pid);
	saveSetting("micropay_key", $micropay_key);
	saveSetting("micropay_mchid", $micropay_mchid);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "epay_n" && $_POST["do"] == "submit") {
	adminpermission("super", 1);
	$payapi = $_POST["payapi"];
	$epay_url = $_POST["epay_url"];
	//if (!check_pay_api($epay_url) && !empty($epay_url)) {
	//	showmsg("未经过官方认证的支付接口存在重大安全隐患,请谨慎使用！", 3);
	//}
	$epay_pid = $_POST["epay_pid"];
	$epay_key = $_POST["epay_key"];
	if (($conf["alipay_api"] == 2 || $conf["qqpay_api"] == 2 || $conf["wxpay_api"] == 2) && $payapi == -1) {
		if (empty($epay_url)) {
			showmsg("请填写接口网址", 3);
		}
		$conf["payapi"] = -1;
		$conf["epay_url"] = $epay_url;
	}
	$payapi2 = $_POST["payapi2"];
	$epay_url2 = $_POST["epay_url2"];
	//if (!check_pay_api($epay_url2) && !empty($epay_url2)) {
	//	showmsg("未经过官方认证的支付接口存在重大安全隐患,请谨慎使用！", 3);
	//}
	$epay_pid2 = $_POST["epay_pid2"];
	$epay_key2 = $_POST["epay_key2"];
	if (($conf["qqpay_api"] == 8 || $conf["wxpay_api"] == 8) && $payapi2 == -1) {
		if (empty($epay_url2)) {
			showmsg("请填写接口网址", 3);
		}
		$conf["payapi2"] = -1;
		$conf["epay_url2"] = $epay_url2;
	}
	$payapi3 = $_POST["payapi3"];
	$epay_url3 = $_POST["epay_url3"];
	//if (!check_pay_api($epay_url3) && !empty($epay_url3)) {
	//	showmsg("未经过官方认证的支付接口存在重大安全隐患,请谨慎使用！", 3);
	//}
	$epay_pid3 = $_POST["epay_pid3"];
	$epay_key3 = $_POST["epay_key3"];
	if ($conf["wxpay_api"] == 9 && $payapi3 == -1) {
		if (empty($epay_url3)) {
			showmsg("请填写接口网址", 3);
		}
		$conf["payapi3"] = -1;
		$conf["epay_url3"] = $epay_url3;
	}
	$payapi4 = $_POST["payapi4"];
	$epay_url4 = $_POST["epay_url4"];
	//if (!check_pay_api($epay_url4) && !empty($epay_url4)) {
	//	showmsg("未经过官方认证的支付接口存在重大安全隐患,请谨慎使用！", 3);
	//}
	$epay_pid4 = $_POST["epay_pid4"];
	$epay_key4 = $_POST["epay_key4"];
	if (($conf["alipay_api"] == 10 || $conf["qqpay_api"] == 10 || $conf["wxpay_api"] == 10) && $payapi4 == -1) {
		if (empty($epay_url4)) {
			showmsg("请填写接口网址", 3);
		}
		$conf["payapi4"] = -1;
		$conf["epay_url4"] = $epay_url4;
	}
	if($payapi){
		saveSetting("payapi", $payapi);
	}
	if($epay_url){
		saveSetting("epay_url", $epay_url);
	}
	if($epay_pid){
		saveSetting("epay_pid", $epay_pid);
	}
	if($epay_key){
		saveSetting("epay_key", $epay_key);
	}
	if($payapi2){
		saveSetting("payapi2", $payapi2);
	}
	if($epay_url2){
		saveSetting("epay_url2", $epay_url2);
	}
	if($epay_pid2){
		saveSetting("epay_pid2", $epay_pid2);
	}
	if($epay_key2){
		saveSetting("epay_key2", $epay_key2);
	}
	if($payapi3){
	saveSetting("payapi3", $payapi3);
}
if($epay_url3){
	saveSetting("epay_url3", $epay_url3);
}
if($epay_pid3){
	saveSetting("epay_pid3", $epay_pid3);
}
if($epay_key3){
	saveSetting("epay_key3", $epay_key3);
}
if($payapi4){
	saveSetting("payapi4", $payapi4);
}
if($epay_url4){
	saveSetting("epay_url4", $epay_url4);
}
if($epay_pid4){
	saveSetting("epay_pid4", $epay_pid4);
}
if($epay_key4){
	saveSetting("epay_key4", $epay_key4);
}

	
	$ad = $CACHE->clear();
	
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "pay") {
	adminpermission("super", 1);
	//$pay_api_list = $authorization->getPayList();
	?>		<style>
			.block-options .btn {
				opacity: 0.8;
				font-weight: 600;
				color: #f36b0a
			}
		</style>
		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">支付接口配置</h3>
				<div class="block-options pull-right"><a href="./payorder.php" class="btn btn-default">支付记录</a></div>
			</div>
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-lg-3 control-label">支付宝支付接口</label>
					<div class="col-lg-8">
						<select class="form-control" name="alipay_api" default="<?php echo $conf["alipay_api"];?>">
							<option value="0">关闭</option>
							<option value="1">支付宝电脑+手机网站支付</option>
							<option value="3">支付宝当面付扫码支付</option>
							<option value="2">易支付接口</option>
							<option value="10">易支付接口（新）</option>
							<option value="6">支付宝小微支付接口</option>
							<option value="7">卡易信笔笔清支付宝接口</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">QQ钱包支付接口</label>
					<div class="col-lg-8">
						<select class="form-control" name="qqpay_api" default="<?php echo $conf["qqpay_api"];?>">
							<option value="0">关闭</option>
							<option value="1">QQ钱包官方支付接口</option>
							<option value="2">易支付接口</option>
							<option value="8">易支付接口(备用1)</option>
							<option value="10">易支付接口（新）</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">微信支付接口</label>
					<div class="col-lg-8">
						<select class="form-control" name="wxpay_api" default="<?php echo $conf["wxpay_api"];?>">
							<option value="0">关闭</option>
							<option value="1">微信官方扫码+公众号支付接口</option>
							<option value="3">微信官方扫码+H5支付接口</option>
							<option value="2">易支付接口</option>
							<option value="8">易支付接口(备用1)</option>
							<option value="9">易支付接口(备用2)</option>
							<option value="10">易支付接口（新）</option>
							<option value="6">微信小微支付接口</option>
						</select>
					</div>
				</div>
				<div class="form-group">
				<div class="col-sm-offset-3 col-sm-8">
					<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
				</div>
			</div>
		</form>
	</div>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">用户后台充值专属支付接口</h3>
		</div>
		<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-lg-3 control-label">充值专属-支付宝支付接口</label>
				<div class="col-lg-8">
					<select class="form-control" name="recharge_alipay_api" default="<?php echo isset($conf['recharge_alipay_api']) ? $conf['recharge_alipay_api'] : 0;?>">
						<option value="0">默认（使用上方支付宝设置）</option>
						<option value="1">支付宝电脑+手机网站支付</option>
						<option value="3">支付宝当面付扫码支付</option>
						<option value="2">易支付接口</option>
						<option value="10">易支付接口（新）</option>
						<option value="6">支付宝小微支付接口</option>
						<option value="7">卡易信笔笔清支付宝接口</option>
					</select>
					<font color="green">设置为"默认"时使用上方支付宝支付接口设置，否则使用此处指定的支付接口</font>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">充值专属-QQ钱包支付接口</label>
				<div class="col-lg-8">
					<select class="form-control" name="recharge_qqpay_api" default="<?php echo isset($conf['recharge_qqpay_api']) ? $conf['recharge_qqpay_api'] : 0;?>">
						<option value="0">默认（使用上方QQ钱包设置）</option>
						<option value="1">QQ钱包官方支付接口</option>
						<option value="2">易支付接口</option>
						<option value="8">易支付接口(备用1)</option>
						<option value="10">易支付接口（新）</option>
					</select>
					<font color="green">设置为"默认"时使用上方QQ钱包支付接口设置，否则使用此处指定的支付接口</font>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">充值专属-微信支付接口</label>
				<div class="col-lg-8">
					<select class="form-control" name="recharge_wxpay_api" default="<?php echo isset($conf['recharge_wxpay_api']) ? $conf['recharge_wxpay_api'] : 0;?>">
						<option value="0">默认（使用上方微信支付设置）</option>
						<option value="1">微信官方扫码+公众号支付接口</option>
						<option value="3">微信官方扫码+H5支付接口</option>
						<option value="2">易支付接口</option>
						<option value="8">易支付接口(备用1)</option>
						<option value="9">易支付接口(备用2)</option>
						<option value="10">易支付接口（新）</option>
						<option value="6">微信小微支付接口</option>
					</select>
					<font color="green">设置为"默认"时使用上方微信支付接口设置，否则使用此处指定的支付接口</font>
				</div>
			</div>
			<div id="recharge_epay_config" style="display:none;">
			<hr>
			<div class="epay-group" data-api="2">
			<h4>充值专属 - 易支付密钥配置</h4>
			<font color="green">仅在充值专属接口选择"易支付"时生效，留空则使用上方全局易支付配置</font>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付接口网址</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_url" class="form-control" value="<?php echo isset($conf['recharge_epay_url']) ? $conf['recharge_epay_url'] : '';?>" placeholder="留空使用全局易支付网址">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户ID</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_pid" class="form-control" value="<?php echo isset($conf['recharge_epay_pid']) ? $conf['recharge_epay_pid'] : '';?>" placeholder="留空使用全局易支付商户ID">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户密钥</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_key" class="form-control" value="<?php echo isset($conf['recharge_epay_key']) ? $conf['recharge_epay_key'] : '';?>" placeholder="留空使用全局易支付密钥">
				</div>
			</div>
			</div>
			<div class="epay-group" data-api="8">
			<h4>充值专属 - 易支付(备用1)密钥配置</h4>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付接口网址</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_url2" class="form-control" value="<?php echo isset($conf['recharge_epay_url2']) ? $conf['recharge_epay_url2'] : '';?>" placeholder="留空使用全局备用1网址">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户ID</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_pid2" class="form-control" value="<?php echo isset($conf['recharge_epay_pid2']) ? $conf['recharge_epay_pid2'] : '';?>" placeholder="留空使用全局备用1商户ID">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户密钥</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_key2" class="form-control" value="<?php echo isset($conf['recharge_epay_key2']) ? $conf['recharge_epay_key2'] : '';?>" placeholder="留空使用全局备用1密钥">
				</div>
			</div>
			</div>
			<div class="epay-group" data-api="9">
			<h4>充值专属 - 易支付(备用2)密钥配置</h4>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付接口网址</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_url3" class="form-control" value="<?php echo isset($conf['recharge_epay_url3']) ? $conf['recharge_epay_url3'] : '';?>" placeholder="留空使用全局备用2网址">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户ID</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_pid3" class="form-control" value="<?php echo isset($conf['recharge_epay_pid3']) ? $conf['recharge_epay_pid3'] : '';?>" placeholder="留空使用全局备用2商户ID">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户密钥</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_key3" class="form-control" value="<?php echo isset($conf['recharge_epay_key3']) ? $conf['recharge_epay_key3'] : '';?>" placeholder="留空使用全局备用2密钥">
				</div>
			</div>
			</div>
			<div class="epay-group" data-api="10">
			<h4>充值专属 - 易支付(新)密钥配置</h4>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付接口网址</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_url4" class="form-control" value="<?php echo isset($conf['recharge_epay_url4']) ? $conf['recharge_epay_url4'] : '';?>" placeholder="留空使用全局新易支付网址">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户ID</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_pid4" class="form-control" value="<?php echo isset($conf['recharge_epay_pid4']) ? $conf['recharge_epay_pid4'] : '';?>" placeholder="留空使用全局新易支付商户ID">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label">易支付商户密钥</label>
				<div class="col-lg-8">
					<input type="text" name="recharge_epay_key4" class="form-control" value="<?php echo isset($conf['recharge_epay_key4']) ? $conf['recharge_epay_key4'] : '';?>" placeholder="留空使用全局新易支付密钥">
				</div>
			</div>
			</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-8">
					<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
				</div>
			</div>
		</form>
	</div>
	<script>
	(function(){
		var epayConfig = document.getElementById('recharge_epay_config');
		var selects = document.querySelectorAll('select[name="recharge_alipay_api"], select[name="recharge_qqpay_api"], select[name="recharge_wxpay_api"]');
		var groups = document.querySelectorAll('.epay-group');
		function toggleEpayConfig() {
			var selectedApis = {};
			selects.forEach(function(s) {
				var v = parseInt(s.value);
				if (v > 0) selectedApis[v] = true;
			});
			var anyVisible = false;
			groups.forEach(function(g) {
				var api = parseInt(g.getAttribute('data-api'));
				if (selectedApis[api]) {
					g.style.display = '';
					anyVisible = true;
				} else {
					g.style.display = 'none';
				}
			});
			epayConfig.style.display = anyVisible ? '' : 'none';
		}
		selects.forEach(function(s) {
			s.addEventListener('change', toggleEpayConfig);
		});
		toggleEpayConfig();
	})();
	</script>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">商品名称自定义</h3>
		</div>
		<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<label class="col-lg-3 control-label">商品名称自定义</label>
				<div class="col-lg-8">
					<input type="text" name="ordername" class="form-control" value="<?php echo $conf["ordername"];?>" placeholder="留空使用原商品名称">
					<font color="green">此选项可以替换支付接口的商品名称，留空使用原商品名称。支持变量值：[time]当前时间戳，[order]支付订单号，[name]原商品名称</font>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
				</div>
			</div>
		</form>
	</div>
					<?php 
	// USDT-TRC20支付配置入口
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">USDT-TRC20支付设置</h3>
		</div>
		<div class="panel-body">
			<p>USDT-TRC20支付需要单独配置，<a href="./set.php?mod=codepay">点击这里进入USDT-TRC20支付配置页面</a></p>
		</div>
	</div>
		<?php 
	if ($conf["alipay_api"] == 1 || $conf["alipay_api"] == 3) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">支付宝官方支付接口配置</h3>
					<div class="block-options pull-right"><a href="https://b.alipay.com/signing/productSetV2.htm" rel="noreferrer" target="_blank" class="btn btn-default">申请地址</a><a href="https://openhome.alipay.com/dev/workspace/key-manage" rel="noreferrer" target="_blank" class="btn btn-default">密钥查看</a></div>
				</div>
				<form action="./set.php?mod=alipay_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">应用APPID</label>
						<div class="col-lg-8">
							<input type="text" name="alipay_appid" class="form-control" value="<?php echo $conf["alipay_appid"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">支付宝公钥(RSA2)</label>
						<div class="col-lg-8">
							<textarea id="appkey" name="alipay_publickey" rows="2" class="form-control" required="" placeholder="是支付宝公钥不是商户公钥！填错会无法回调"><?php echo $conf["alipay_publickey"];?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">应用私钥(RSA2)</label>
						<div class="col-lg-8">
							<textarea id="appkey" name="alipay_privatekey" rows="2" class="form-control" required="" placeholder=""><?php echo $conf["alipay_privatekey"];?></textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["alipay_api"] == 7) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">卡易信支付宝接口配置</h3>
					<div class="block-options pull-right"><a href="http://qdd.kayixin.com/reg1.htm" rel="noreferrer" target="_blank" class="btn btn-default">申请地址</a></div>
				</div>
				<form action="./set.php?mod=alipay2_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">接口域名</label>
						<div class="col-lg-8">
							<input type="text" name="alipay_getway" class="form-control" value="<?php echo $conf["alipay_getway"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">商户号</label>
						<div class="col-lg-8">
							<input type="text" name="alipay_pid" class="form-control" value="<?php echo $conf["alipay_pid"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">商户密钥</label>
						<div class="col-lg-8">
							<input type="text" name="alipay_key" class="form-control" value="<?php echo $conf["alipay_key"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["qqpay_api"] == 1) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">QQ钱包官方接口配置</h3>
					<div class="block-options pull-right"><a href="https://qpay.qq.com/" rel="noreferrer" target="_blank" class="btn btn-default">申请地址</a></div>
				</div>
				<form action="./set.php?mod=qqpay_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">QQ钱包商户号</label>
						<div class="col-lg-8">
							<input type="text" name="qqpay_mchid" class="form-control" value="<?php echo $conf["qqpay_mchid"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">QQ钱包API密钥</label>
						<div class="col-lg-8">
							<input type="text" name="qqpay_key" class="form-control" value="<?php echo $conf["qqpay_key"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["wxpay_api"] == 1 || $conf["wxpay_api"] == 3) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">微信支付官方接口配置</h3>
					<div class="block-options pull-right"><a href="https://pay.weixin.qq.com/" rel="noreferrer" target="_blank" class="btn btn-default">申请地址</a></div>
				</div>
				<form action="./set.php?mod=wxpay_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">微信公众号APPID</label>
						<div class="col-lg-8">
							<input type="text" name="wxpay_appid" class="form-control" value="<?php echo $conf["wxpay_appid"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">微信支付商户号</label>
						<div class="col-lg-8">
							<input type="text" name="wxpay_mchid" class="form-control" value="<?php echo $conf["wxpay_mchid"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">微信支付商户密钥</label>
						<div class="col-lg-8">
							<input type="text" name="wxpay_key" class="form-control" value="<?php echo $conf["wxpay_key"];?>" required="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">微信公众号APPSECRET</label>
						<div class="col-lg-8">
							<input type="text" name="wxpay_appsecret" class="form-control" value="<?php echo $conf["wxpay_appsecret"];?>" placeholder="仅公众号支付需要填写">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">微信支付指定域名</label>
						<div class="col-lg-8">
							<input type="text" name="wxpay_domain" class="form-control" value="<?php echo $conf["wxpay_domain"];?>" placeholder="用于微信公众号支付与H5支付接口，限制域名的情况下">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["alipay_api"] == 2 || $conf["qqpay_api"] == 2 || $conf["wxpay_api"] == 2) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">易支付配置</h3>
					<div class="block-options pull-right"></div>
					<div class="block-options pull-right"><a href="#" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl" style="display:none;">进入易支付网站</a>  <a href="https://404.6v6.ren/zhi-fu/1.html" target="_blank" class="btn btn-default" style="color:#0000FF">推荐的支付</a></div>
				</div>
				<form action="./set.php?mod=epay_n" method="post" class="form-horizontal" role="form" onsubmit="return checkepayurl('payapi','epay_url')"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付接入商</label>
						<div class="col-lg-8">
							<select class="form-control" name="payapi" default="<?php echo $conf["payapi"];?>">
							    <?php echo $pay_api_list;?>								<option value="-1">其它（手动输入）</option>
							</select>
						</div>
					</div>
					<div class="form-group" id="payapi_07" style="<?php echo $conf["payapi"] == -1 ? "display:none;" : NULL;?>">
						<label class="col-lg-3 control-label">易支付接口网址</label>
						<div class="col-lg-8">
							<input type="text" name="epay_url" class="form-control" value="<?php echo $conf["epay_url"];?>" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户ID</label>
						<div class="col-lg-8">
							<input type="text" name="epay_pid" class="form-control" value="<?php echo $conf["epay_pid"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户密钥</label>
						<div class="col-lg-8">
							<input type="text" name="epay_key" class="form-control" value="<?php echo $conf["epay_key"];?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br /><br />
							<a href="set.php?mod=epay">进入易支付结算设置及订单查询页面</a>
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["alipay_api"] == 6 || $conf["wxpay_api"] == 6) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">支付宝/微信小微支付配置</h3>
					<div class="block-options pull-right"><a href="#" rel="noreferrer" target="_blank" class="btn btn-default" id="micropayurl" style="display:none;">申请地址</a></div>
				</div>
				<form action="./set.php?mod=micropay_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">小微支付接入商</label>
						<div class="col-lg-8">
							<select class="form-control" name="micropayapi" default="<?php echo $conf["micropayapi"];?>">
								<option value="0">风吹雨</option>
								<option value="1">微通付</option>
								<option value="2">微易付</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">小微支付APPID</label>
						<div class="col-lg-8">
							<input type="text" name="micropay_pid" class="form-control" value="<?php echo $conf["micropay_pid"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">小微支付密钥</label>
						<div class="col-lg-8">
							<input type="text" name="micropay_key" class="form-control" value="<?php echo $conf["micropay_key"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">小微支付MCHID</label>
						<div class="col-lg-8">
							<input type="text" name="micropay_mchid" class="form-control" value="<?php echo $conf["micropay_mchid"];?>">
							<font color="green">此处可同时填写支付宝和微信的，用英文逗号,隔开</font>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["qqpay_api"] == 8 || $conf["wxpay_api"] == 8) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">易支付（备用1）配置</h3>
					<div class="block-options pull-right"><a href="#" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl2" style="display:none;">进入易支付网站</a></div>
				</div>
				<form action="./set.php?mod=epay_n" method="post" class="form-horizontal" role="form" onsubmit="return checkepayurl('payapi2','epay_url2')"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付接入商</label>
						<div class="col-lg-8">
							<select class="form-control" name="payapi2" default="<?php echo $conf["payapi2"];?>">
								<?php echo $pay_api_list;?>								<option value="-1">其它（手动输入）</option>
							</select>
						</div>
					</div>
					<div class="form-group" id="payapi_08" style="<?php echo $conf["payapi2"] == -1 ? "display:none;" : NULL;?>">
						<label class="col-lg-3 control-label">易支付接口网址</label>
						<div class="col-lg-8">
							<input type="text" name="epay_url2" class="form-control" value="<?php echo $conf["epay_url2"];?>" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户ID</label>
						<div class="col-lg-8">
							<input type="text" name="epay_pid2" class="form-control" value="<?php echo $conf["epay_pid2"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户密钥</label>
						<div class="col-lg-8">
							<input type="text" name="epay_key2" class="form-control" value="<?php echo $conf["epay_key2"];?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["wxpay_api"] == 9) {
		?>			<div class="block">
				<div class="block-title">
					<h3 class="panel-title">易支付（备用2）配置</h3>
					<div class="block-options pull-right"><a href="#" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl3" style="display:none;">进入易支付网站</a></div>
				</div>
				<form action="./set.php?mod=epay_n" method="post" class="form-horizontal" role="form" onsubmit="return checkepayurl('payapi3','epay_url3')"><input type="hidden" name="do" value="submit" />
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付接入商</label>
						<div class="col-lg-8">
							<select class="form-control" name="payapi3" default="<?php echo $conf["payapi3"];?>">
								<?php echo $pay_api_list;?>								<option value="-1">其它（手动输入）</option>
							</select>
						</div>
					</div>
					<div class="form-group" id="payapi_09" style="<?php echo $conf["payapi3"] == -1 ? "display:none;" : NULL;?>">
						<label class="col-lg-3 control-label">易支付接口网址</label>
						<div class="col-lg-8">
							<input type="text" name="epay_url3" class="form-control" value="<?php echo $conf["epay_url3"];?>" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户ID</label>
						<div class="col-lg-8">
							<input type="text" name="epay_pid3" class="form-control" value="<?php echo $conf["epay_pid3"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-3 control-label">易支付商户密钥</label>
						<div class="col-lg-8">
							<input type="text" name="epay_key3" class="form-control" value="<?php echo $conf["epay_key3"];?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
		<?php 
	}
	?>		<?php 
	if ($conf["alipay_api"] == 10 || $conf["qqpay_api"] == 10 || $conf["wxpay_api"] == 10) {
			?>				<div class="block">
					<div class="block-title">
						<h3 class="panel-title">易支付（新）配置</h3>
						<div class="block-options pull-right"><a href="#" rel="noreferrer" target="_blank" class="btn btn-default" id="epayurl4" style="display:none;">进入易支付网站</a></div>
					</div>
					<form action="./set.php?mod=epay_n" method="post" class="form-horizontal" role="form" onsubmit="return checkepayurl('payapi4','epay_url4')"><input type="hidden" name="do" value="submit" />
						<div class="form-group">
							<label class="col-lg-3 control-label">易支付接入商</label>
							<div class="col-lg-8">
								<select class="form-control" name="payapi4" default="<?php echo $conf["payapi4"];?>">
									<?php echo $pay_api_list;?>									<option value="-1">其它（手动输入）</option>
								</select>
							</div>
						</div>
						<div class="form-group" id="payapi_10" style="<?php echo $conf["payapi4"] == -1 ? "display:none;" : NULL;?>">
							<label class="col-lg-3 control-label">易支付接口网址</label>
							<div class="col-lg-8">
								<input type="text" name="epay_url4" class="form-control" value="<?php echo $conf["epay_url4"];?>" placeholder="">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">易支付商户ID</label>
							<div class="col-lg-8">
								<input type="text" name="epay_pid4" class="form-control" value="<?php echo $conf["epay_pid4"];?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 control-label">易支付商户密钥</label>
							<div class="col-lg-8">
								<input type="text" name="epay_key4" class="form-control" value="<?php echo $conf["epay_key4"];?>">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-8"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
							</div>
						</div>
					</form>
				</div>
			<?php 
			}
			?>
	</div>
<script>
	var items = $("select[default]");
	for (i = 0; i < items.length; i++) {
		$(items[i]).val($(items[i]).attr("default") || 0);
	}

	function checkURL(obj) {
		var url = $(obj).val();

		if (url.indexOf(" ") >= 0) {
			url = url.replace(/ /g, "");
		}
		if (url.toLowerCase().indexOf("http://") < 0 && url.toLowerCase().indexOf("https://") < 0) {
			url = "http://" + url;
		}
		if (url.slice(url.length - 1) != "/") {
			url = url + "/";
		}
		$(obj).val(url);
	}

	function checkepayurl(var1, var2) {
		if ($("select[name='" + var1 + "']").val() == -1) {
			checkURL("input[name='" + var2 + "']");
		}
		return true;
	}
	$("select[name='payapi']").change(function() {
       // console.log($("input[name='epay_url']").val());
        if ($(this).val() == null){
            $("#epayurl").hide();
            $("#payapi_07").css("display", "none");
        }else if ($(this).val() == -1) {
			$("#payapi_07").css("display", "inherit");
			$("#epayurl").hide();
		} else {
			$("#payapi_07").css("display", "none");
			$("input[name='epay_url']").val($("select[name='payapi']").val());
            $("#epayurl").attr("href", $("input[name='epay_url']").val());
			$("#epayurl").html('点此进入' + $("select[name='payapi'] option:selected").html() + '');
			$("#epayurl").show();
		}
	
	});
	$("select[name='payapi2']").change(function() {
	    if ($(this).val() == null){
            $("#epayurl2").hide();
            $("#payapi_08").css("display", "none");
		}else if ($(this).val() == -1) {
			$("#payapi_08").css("display", "inherit");
			$("#epayurl2").hide();
		} else {
			$("#payapi_08").css("display", "none");
			$("input[name='epay_url2']").val($("select[name='payapi2']").val());
			$("#epayurl2").attr("href", $("select[name='payapi2']").val());
			$("#epayurl2").html('点此进入' + $("select[name='payapi2'] option:selected").html() + '');
			$("#epayurl2").show();
		}
	});
	$("select[name='payapi3']").change(function() {
	    if ($(this).val() == null){
            $("#epayurl3").hide();
            $("#payapi_09").css("display", "none");
		}else if ($(this).val() == -1) {
			$("#payapi_09").css("display", "inherit");
			$("#epayurl3").hide();
		} else {
			$("#payapi_09").css("display", "none");
			$("input[name='epay_url3']").val($("select[name='payapi3']").val());
			$("#epayurl3").attr("href", $("select[name='payapi3']").val());
			$("#epayurl3").html('点此进入' + $("select[name='payapi3'] option:selected").html() + '');
			$("#epayurl3").show();
		}
	});
	if ($("#epayurl").length > 0 && $("#epayurl").is(':hidden')) {
		$("select[name='payapi']").change();
	}
	if ($("#epayurl2").length > 0 && $("#epayurl2").is(':hidden')) {
		$("select[name='payapi2']").change();
	}
	$('select[name="payapi4"]').change(function() {
		if ($(this).val() == null){
			$('#epayurl4').hide();
			$('#payapi_10').css('display', 'none');
		}else if ($(this).val() == -1) {
			$('#payapi_10').css('display', 'inherit');
			$('#epayurl4').hide();
		} else {
			$('#payapi_10').css('display', 'none');
			$('input[name="epay_url4"]').val($('select[name="payapi4"]').val());
			$('#epayurl4').attr('href', $('select[name="payapi4"]').val());
			$('#epayurl4').html('点此进入' + $('select[name="payapi4"] option:selected').html() + '');
			$('#epayurl4').show();
		}
	});
	if ($("#epayurl3").length > 0 && $("#epayurl3").is(':hidden')) {
		$('select[name="payapi3"]').change();
	}
	if ($("#epayurl4").length > 0 && $("#epayurl4").is(':hidden')) {
		$('select[name="payapi4"]').change();
	}
	$("select[name='micropayapi']").change(function() {
		$.ajax({
			type: "GET",
			url: "ajax.php?act=micropayurl&id=" + $(this).val(),
			dataType: 'json',
			success: function(data) {
				if (data.code == 0) {
					$("#micropayurl").attr("href", data.url);
					$("#micropayurl").html('进入' + $("select[name='micropayapi'] option:selected").html() + '商户申请页面');
					$("#micropayurl").show();
				} else {
					$("#micropayurl").hide();
				}
			}
		});
	});
	if ($("#micropayurl").length > 0 && $("#micropayurl").is(':hidden')) {
		$("select[name='micropayapi']").change();
	}
</script>
<?php 
} elseif ($mod == "epay_nn") {
	adminpermission("super", 1);
	$payapi = pay_api(true);
	$account = $_POST["account"];
	$username = $_POST["username"];
	if ($account == NULL || $username == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$data = get_curl($payapi . "api.php?act=change&pid=" . $conf["epay_pid"] . "&key=" . $conf["epay_key"] . "&account=" . $account . "&username=" . $username . "&url=" . $_SERVER["HTTP_HOST"]);
		$arr = json_decode($data, true);
		if ($arr["code"] == 1) {
			showmsg("修改成功!", 1);
		} else {
			showmsg($arr["msg"]);
		}
	}
} elseif ($mod == "codepay") {
	adminpermission("super", 1);
	?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">USDT-TRC20支付配置</h3>
			</div>
			<form action="./set.php?mod=codepay_n" method="post" class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
				<div class="form-group">
					<label class="col-lg-3 control-label">USDT钱包地址</label>
					<div class="col-lg-8">
						<input type="text" name="codepay_key" class="form-control" value="<?php echo $conf["codepay_key"];?>" required>
						<font color="green">请填写您的TRC20格式USDT钱包地址</font>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">支付设置</label>
					<div class="col-lg-8">
						<input type="text" name="codepay_id" class="form-control" value="<?php echo $conf["codepay_id"];?>" placeholder="格式：有效期|汇率" required>
						<font color="green">有效期单位为分钟，汇率为USDT对人民币的汇率，例如：30|7.2</font>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-8">
						<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
					</div>
				</div>
			</form>
		</div>
		<?php 
} elseif ($mod == "epay") {
	adminpermission("super", 1);
	if (!empty($conf["epay_pid"]) && !empty($conf["epay_key"])) {
		$payapi = pay_api(true);
		$data = get_curl($payapi . "api.php?act=query&pid=" . $conf["epay_pid"] . "&key=" . $conf["epay_key"] . "&url=" . $_SERVER["HTTP_HOST"]);
		$arr = json_decode($data, true);
		if ($arr["code"] == -2) {
			showmsg("易支付KEY校验失败！");
		} elseif (!array_key_exists("code", $arr)) {
			showmsg("获取失败，请刷新重试！");
		}
		$stype = $arr["stype"] ?: "支付宝";
	} else {
		showmsg("你还未填写易支付商户ID和密钥，请返回填写！");
	}
	if (array_key_exists("active", $arr) && $arr["active"] == 0) {
		showmsg("该商户已被封禁");
	}
	$key = substr($arr["key"], 0, 8) . "****************" . substr($arr["key"], 24, 32);
	if (!file_exists("pay.lock")) {
		file_put_contents("pay.lock", "pay.lock");
	}
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">易支付设置</h3>
		</div>
		<div class="">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#">易支付设置</a></li>
				<li><a href="./set.php?mod=epay_order">订单记录</a></li>
				<li><a href="./set.php?mod=epay_settle">结算记录</a></li>
			</ul>
			<form action="./set.php?mod=epay_nn" method="post" class="form-horizontal" role="form">
				<h4>商户信息查看：</h4>
				<div class="form-group">
					<label class="col-sm-2 control-label">商户ID</label>
					<div class="col-sm-10"><input type="text" name="pid" value="<?php echo $arr["pid"];?>" class="form-control" disabled /></div>
				</div><br />
				<div class="form-group">
					<label class="col-sm-2 control-label">商户KEY</label>
					<div class="col-sm-10"><input type="text" name="key" value="<?php echo $key;?>" class="form-control" disabled /></div>
				</div><br />
				<div class="form-group">
					<label class="col-sm-2 control-label">商户余额</label>
					<div class="col-sm-10"><input type="text" name="money" value="<?php echo $arr["money"];?>" class="form-control" disabled /></div>
				</div><br />
				<h4>收款账号设置：</h4>
				<div class="form-group">
					<label class="col-sm-2 control-label">结算方式</label>
					<div class="col-sm-10"><input type="text" name="type" value="<?php echo $stype;?>" class="form-control" disabled /></div>
				</div><br />
				<div class="form-group">
					<label class="col-sm-2 control-label">结算账号</label>
					<div class="col-sm-10"><input type="text" name="account" value="<?php echo $arr["account"];?>" class="form-control" /></div>
				</div><br />
				<div class="form-group">
					<label class="col-sm-2 control-label">真实姓名</label>
					<div class="col-sm-10"><input type="text" name="username" value="<?php echo $arr["username"];?>" class="form-control" /></div>
				</div><br />
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="确定修改" class="btn btn-primary form-control" /><br />
					</div>
				</div>
				<h4><span class="glyphicon glyphicon-info-sign"></span> 注意事项</h4>
				1.结算账号和真实姓名请仔细核对，一旦错误将无法结算到账！<br />2.每笔交易会有<?php echo 100 - $arr["money_rate"];?>%的手续费，这个手续费是支付宝、微信和财付通收取的，非本接口收取。<br />3.结算为T+1规则，当天满<?php echo $arr["settle_money"];?>元在第二天会自动结算
			</form>
		</div>
	</div>
<?php 
} elseif ($mod == "epay_settle") {
	adminpermission("set", 1);
	$payapi = pay_api(true);
	$data = get_curl($payapi . "api.php?act=settle&pid=" . $conf["epay_pid"] . "&key=" . $conf["epay_key"] . "&limit=20&url=" . $_SERVER["HTTP_HOST"]);
	$arr = json_decode($data, true);
	if ($arr["code"] == -2) {
		showmsg("易支付KEY校验失败！");
	}
	?>	<div class="block">
		<div class="block-title w h">
			<h3 class="panel-title">易支付结算记录</h3>
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>ID</th>
						<th>结算账号</th>
						<th>结算金额</th>
						<th>手续费</th>
						<th>结算时间</th>
					</tr>
				</thead>
				<tbody>
					<?php 
	foreach ($arr["data"] as $res) {
		echo "<tr><td><b>" . $res["id"] . "</b></td><td>" . $res["account"] . "</td><td><b>" . $res["money"] . "</b></td><td><b>" . $res["fee"] . "</b></td><td>" . $res["time"] . "</td></tr>";
	}
	?>				</tbody>
			</table>
		</div>
	</div>
<?php 
} elseif ($mod == "epay_order") {
	adminpermission("set", 1);
	$payapi = pay_api(true);
	$data = get_curl($payapi . "api.php?act=orders&pid=" . $conf["epay_pid"] . "&key=" . $conf["epay_key"] . "&limit=30&url=" . $_SERVER["HTTP_HOST"]);
	$arr = json_decode($data, true);
	if ($arr["code"] == -2) {
		showmsg("易支付KEY校验失败！");
	}
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">易支付订单记录</h3>
		</div>订单只展示前30条[<a href="set.php?mod=epay">返回</a>]
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>交易号/商户订单号</th>
						<th>付款方式</th>
						<th>商品名称/金额</th>
						<th>创建时间/完成时间</th>
						<th>状态</th>
					</tr>
				</thead>
				<tbody>
					<?php 
	foreach ($arr["data"] as $res) {
		echo "<tr><td>" . $res["trade_no"] . "<br/>" . $res["out_trade_no"] . "</td><td>" . $res["type"] . "</td><td>" . $res["name"] . "<br/>￥ <b>" . $res["money"] . "</b></td><td>" . $res["addtime"] . "<br/>" . $res["endtime"] . "</td><td>" . ($res["status"] == 1 ? "<font color=green>已完成</font>" : "<font color=red>未完成</font>") . "</td></tr>";
	}
	?>				</tbody>
			</table>
		</div>
	</div>
<?php 
} elseif ($mod == "template") {
	adminpermission("set", 1);
	$mblist = \lib\Template::getList();
	$tpl_names = array('XHY-01'=>'官方默认模板');
	function tpl_display_name($name){ global $tpl_names; return isset($tpl_names[$name]) ? $tpl_names[$name] : $name; }
	?>	<style>
		.mblist {
			margin-bottom: 20px;
		}

		.mblist img {
			height: 110px;
			margin: 0 auto;
		}
	</style>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">首页模板设置</h3>
		</div>
		<div class="">
			<ul class="nav nav-tabs">
				<li style="width: 33.33%;" align="center" class="active">
					<a href="./set.php?mod=template">切换模板</a>
				</li>
				<li style="width: 33.33%;" align="center" class="">
					<a href="./set.php?mod=template2">模板设置</a>
				</li>
				<li style="width: 33.33%;" align="center" class="">
					<a href="./set.php?mod=template_editor">自定义布局编辑</a>
				</li>
			</ul>
			<h4>当前使用模板：</h4>
			<div class="row text-center">
				<div class="col-xs-6 col-sm-4">
					<img class="img-responsive img-thumbnail img-rounded" src="../template/<?php echo !empty($conf['template'])?$conf['template']:'default';?>/preview.png" loading="lazy" onerror="this.src='../assets/img/NoImg.png'" style="min-height: 150px; background: #f5f5f5; display: block;">
				</div>
				<div class="col-xs-6 col-sm-4">
					<p>模板名称：<?php echo tpl_display_name(!empty($conf['template'])?$conf['template']:'default');?></p>
					<p><a href="./set.php?mod=template2">进入模板设置</a></p>
				</div>
			</div>
			<hr />
			<h4>更换模板：</h4>
			<div class="row text-center">
				<?php 
	foreach ($mblist as $row) {
		?>					<div class="col-xs-6 col-sm-4 mblist">
						<a href="javascript:void(0)" onclick="return changeTemplate('<?php echo $row;?>')"><img class="img-responsive img-thumbnail img-rounded" src="../template/<?php echo $row;?>/preview.png" loading="lazy" onerror="this.src='../assets/img/NoImg.png'" title="点击更换到该模板" style="min-height: 150px; background: #f5f5f5; display: block;"><br /><strong><?php echo tpl_display_name($row);?></strong></a>
					</div>
				<?php 
	}
	?>			</div>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			网站模板对应template目录里面的名称，会自动获取
		</div>
	</div>
<?php 
} elseif ($mod == "template_editor") {
	adminpermission("set", 1);
	$template_name = $conf['template'] ? $conf['template'] : 'TingdongCat';
	$config_file = TEMPLATE_ROOT . $template_name . '/layout_config.json';
	$layout_config = [];
	if (file_exists($config_file)) {
		$layout_config = json_decode(file_get_contents($config_file), true);
	}
	if (!$layout_config) {
		$layout_config = [];
	}
	?>
<style>
.template-editor-container {
	display: flex;
	flex-direction: column;
	height: calc(100vh - 120px);
}
.template-editor-toolbar {
	background: #fff;
	padding: 10px 15px;
	border-radius: 6px;
	margin-bottom: 10px;
	box-shadow: 0 1px 3px rgba(0,0,0,0.1);
	display: flex;
	align-items: center;
	gap: 10px;
	flex-wrap: wrap;
}
.template-editor-toolbar .btn {
	border-radius: 4px;
}
.template-editor-toolbar .editor-status {
	margin-left: auto;
	font-size: 13px;
	color: #999;
}
.template-editor-iframe-wrap {
	flex: 1;
	position: relative;
	border: 2px solid #e0e0e0;
	border-radius: 6px;
	overflow: hidden;
	background: #f5f5f5;
}
.template-editor-iframe-wrap iframe {
	width: 100%;
	height: 100%;
	border: none;
}
.editor-hint-overlay {
	position: absolute;
	bottom: 10px;
	left: 50%;
	transform: translateX(-50%);
	background: rgba(0,0,0,0.75);
	color: #fff;
	padding: 6px 16px;
	border-radius: 20px;
	font-size: 12px;
	pointer-events: none;
	z-index: 10;
	white-space: nowrap;
}
</style>
<div class="block">
	<div class="block-title">
		<h3 class="panel-title">首页模板设置</h3>
	</div>
	<div class="">
		<ul class="nav nav-tabs">
			<li style="width: 33.33%;" align="center" class="">
				<a href="./set.php?mod=template">切换模板</a>
			</li>
			<li style="width: 33.33%;" align="center" class="">
				<a href="./set.php?mod=template2">模板设置</a>
			</li>
			<li style="width: 33.33%;" align="center" class="active">
				<a href="./set.php?mod=template_editor">自定义布局编辑</a>
			</li>
		</ul>
		<div class="template-editor-container">
			<div class="template-editor-toolbar">
				<strong>当前模板：<?php echo $template_name;?></strong>
				<button class="btn btn-sm btn-primary" onclick="templateEditor.save()"><i class="fa fa-save"></i> 保存修改</button>
				<button class="btn btn-sm btn-warning" onclick="templateEditor.reset()"><i class="fa fa-refresh"></i> 重置内容</button>
				<button class="btn btn-sm btn-info" onclick="templateEditor.toggleEditMode()" id="btn-toggle-edit"><i class="fa fa-edit"></i> 退出编辑模式</button>
				<span class="editor-status" id="editor-status">编辑模式已启用</span>
			</div>
			<div class="template-editor-iframe-wrap" id="iframe-wrap">
				<iframe id="template-preview-iframe" src="../?template_preview=<?php echo $template_name;?>"></iframe>
				<div class="editor-hint-overlay" id="editor-hint">右键点击文字可编辑内容</div>
			</div>
		</div>
	</div>
</div>
<script>
var templateEditor = {
	editMode: true,
	layoutConfig: <?php echo json_encode($layout_config);?>,
	templateName: '<?php echo $template_name;?>',

	init: function() {
		var self = this;
		var iframe = document.getElementById('template-preview-iframe');
		iframe.onload = function() {
			self.injectEditor();
		};
		if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
			self.injectEditor();
		}
	},

	injectEditor: function() {
		var self = this;
		var iframe = document.getElementById('template-preview-iframe');
		var doc = iframe.contentDocument || iframe.contentWindow.document;
		if (!doc || !doc.body) return;

		var style = doc.createElement('style');
		style.id = 'template-editor-style';
		style.textContent = '\
			.tpl-editable-text { cursor: pointer; position: relative; transition: background 0.2s; }\
			.tpl-editable-text:hover { background: rgba(255, 235, 59, 0.3) !important; outline: 2px dashed #ff9800; }\
			.tpl-editable-text.editing { background: rgba(255, 235, 59, 0.5) !important; outline: 2px solid #ff5722; }\
			.tpl-editor-input { border: 2px solid #ff5722 !important; border-radius: 4px; padding: 4px 8px; font-size: inherit; font-family: inherit; background: #fff; width: auto; min-width: 60px; z-index: 9999; }\
		';
		doc.head.appendChild(style);

		var textSelectors = 'h1, h2, h3, h4, h5, span, p, a, b, strong, small, label, button, td, th, li, .btn, .list-group-item, .widget-heading, .panel-title, .block-title h4';
		var textElements = doc.querySelectorAll(textSelectors);

		var tplIdx = 0;
		textElements.forEach(function(el) {
			if (el.closest('script') || el.closest('style') || el.closest('link') || el.closest('meta')) return;
			if (el.querySelector('input, textarea, select, button, img, iframe, canvas, svg')) return;
			var text = (el.innerText || el.textContent || '').trim();
			if (text.length > 0 && text.length < 200 && el.children.length === 0) {
				el.classList.add('tpl-editable-text');
				el.setAttribute('data-original-text', text);
				el.setAttribute('data-tpl-id', tplIdx);
				tplIdx++;
			}
		});

		doc.addEventListener('contextmenu', function(e) {
			if (!self.editMode) return;
			var target = e.target.closest('.tpl-editable-text');
			if (target) {
				e.preventDefault();
				self.startTextEdit(target);
			}
		});

		self.applyLayoutConfig();
		self.iframeDoc = doc;
		document.getElementById('editor-status').textContent = '编辑模式已启用';
	},

	startTextEdit: function(el) {
		var self = this;
		var original = el.getAttribute('data-original-text') || el.innerText || '';
		var input = document.createElement('input');
		input.type = 'text';
		input.value = original;
		input.className = 'tpl-editor-input';
		input.style.width = Math.max(el.offsetWidth, 80) + 'px';

		el.innerHTML = '';
		el.appendChild(input);
		el.classList.add('editing');
		input.focus();
		input.select();

		input.addEventListener('blur', function() {
			self.finishTextEdit(el, input.value);
		});
		input.addEventListener('keydown', function(e) {
			if (e.key === 'Enter') {
				input.blur();
			}
			if (e.key === 'Escape') {
				input.value = original;
				input.blur();
			}
		});
	},

	finishTextEdit: function(el, newText) {
		el.innerText = newText || el.getAttribute('data-original-text') || '';
		el.classList.remove('editing');
		el.setAttribute('data-original-text', el.innerText);
		document.getElementById('editor-status').textContent = '文字已修改 - 记得保存';
	},

	save: function() {
		var self = this;
		var doc = this.iframeDoc;
		if (!doc) { alert('编辑器未就绪，请等待页面加载完成'); return; }

		var textEdits = {};
		var textElements = doc.querySelectorAll('.tpl-editable-text');
		textElements.forEach(function(el) {
			var key = el.getAttribute('data-tpl-id');
			var text = (el.innerText || el.textContent || '').trim();
			if (text && key) {
				textEdits[key] = text;
			}
		});

		var config = {
			template: self.templateName,
			textEdits: textEdits,
			updatedAt: new Date().toISOString()
		};

		var xhr = new XMLHttpRequest();
		xhr.open('POST', './ajax.php?act=save_template_layout', true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			if (xhr.status === 200) {
				var resp = JSON.parse(xhr.responseText);
				if (resp.code === 0) {
					document.getElementById('editor-status').textContent = '保存成功';
				} else {
					alert('保存失败：' + resp.msg);
				}
			} else {
				alert('保存失败，请稍后重试');
			}
		};
		xhr.onerror = function() {
			alert('网络错误，保存失败');
		};
		xhr.send('template=' + encodeURIComponent(self.templateName) + '&config=' + encodeURIComponent(JSON.stringify(config)));
	},

	applyLayoutConfig: function() {
		if (!this.layoutConfig || !this.layoutConfig.textEdits) return;
		var doc = this.iframeDoc;
		if (!doc) return;
		var self = this;
		var textElements = doc.querySelectorAll('.tpl-editable-text');
		textElements.forEach(function(el) {
			var id = el.getAttribute('data-tpl-id');
			if (id && self.layoutConfig.textEdits[id]) {
				el.innerText = self.layoutConfig.textEdits[id];
				el.setAttribute('data-original-text', self.layoutConfig.textEdits[id]);
			}
		});
	},

	reset: function() {
		var self = this;
		var xhr = new XMLHttpRequest();
		xhr.open('POST', './ajax.php?act=reset_template_layout', true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.onload = function() {
			if (xhr.status === 200) {
				var resp = JSON.parse(xhr.responseText);
				if (resp.code === 0) {
					document.getElementById('template-preview-iframe').contentWindow.location.reload();
					document.getElementById('editor-status').textContent = '已重置 - 编辑模式已启用';
				}
			}
		};
		xhr.send('template=' + encodeURIComponent(self.templateName));
	},

	toggleEditMode: function() {
		this.editMode = !this.editMode;
		var btn = document.getElementById('btn-toggle-edit');
		var status = document.getElementById('editor-status');
		var hint = document.getElementById('editor-hint');
		var style = this.iframeDoc ? this.iframeDoc.getElementById('template-editor-style') : null;

		if (this.editMode) {
			btn.innerHTML = '<i class="fa fa-edit"></i> 退出编辑模式';
			status.textContent = '编辑模式已启用';
			if (hint) hint.style.display = '';
			if (style) style.textContent = style.textContent.replace(/display:\s*none[^}]*}/g, '');
		} else {
			btn.innerHTML = '<i class="fa fa-eye"></i> 进入编辑模式';
			status.textContent = '预览模式 - 不可编辑';
			if (hint) hint.style.display = 'none';
			if (style) style.textContent += '.tpl-editable-text:hover { background: transparent !important; outline: none !important; }';
		}
	}
};

document.addEventListener('DOMContentLoaded', function() {
	templateEditor.init();
});

window.addEventListener('message', function(e) {
	if (e.data === 'iframe-loaded') {
		templateEditor.injectEditor();
	}
});
</script>
<?php 
} elseif ($mod == "template2") {
	adminpermission("set", 1);
	$mblist = \lib\Template::getList();
	$template_settings = \lib\Template::loadSetting();
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">首页模板设置</h3>
		</div>
		<div class="">
			<ul class="nav nav-tabs">
				<li style="width: 33.33%;" align="center" class="">
					<a href="./set.php?mod=template">切换模板</a>
				</li>
				<li style="width: 33.33%;" align="center" class="active">
					<a href="./set.php?mod=template2">模板设置</a>
				</li>
				<li style="width: 33.33%;" align="center" class="">
					<a href="./set.php?mod=template_editor">自定义布局编辑</a>
				</li>
			</ul>
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">公共静态资源CDN</label>
					<div class="col-sm-10"><select class="form-control" name="cdnpublic" default="<?php echo $conf["cdnpublic"];?>">
							<option value="0">七牛云CDN</option>
							<option value="1">360CDN</option>
							<option value="2">BootCDN</option>
							<option value="4">今日头条CDN</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">专有静态资源CDN域名</label>
					<div class="col-lg-8">
						<input type="text" name="staticurl" class="form-control" value="<?php echo $conf["staticurl"];?>" placeholder="直接填写域名即可，不使用请留空" />
						<pre>不懂请勿乱填，否则会导致页面显示不正常</pre>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">手机使用模板</label>
					<div class="col-sm-10"><select class="form-control" name="template_m" default="<?php echo $conf["template_m"];?>">
							<option value="0">与电脑版相同（默认）</option>
							<?php 
	foreach ($mblist as $row) {
		?>								<option value="<?php echo $row;?>"><?php echo $row;?></option>
							<?php 
	}
	?>						</select></div>
				</div>
				<?php 
	if ($template_settings) {
		foreach ($template_settings as $k => $v) {
			if ($v["type"]) {
				?>							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $v["name"];?></label>
								<div class="col-sm-10">
									<?php 
				if ($v["type"] == "input") {
					?>										<input type="text" name="<?php echo $k;?>" value="﻿<?php echo $conf[$k];?>" class="form-control" placeholder="<?php echo $v["note"];?>" />
									<?php 
				} elseif ($v["type"] == "textarea") {
					?>										<textarea class="form-control" name="<?php echo $k;?>" rows="5" style="width:100%;" placeholder="<?php echo $v["note"];?>"><?php echo htmlspecialchars($conf[$k]);?></textarea>
									<?php 
				} elseif ($v["type"] == "select") {
					?>										<select class="form-control" name="<?php echo $k;?>" default="<?php echo isset($conf[$k]) && $conf[$k] !== '' ? $conf[$k] : $v["default"];?>">
											<?php 
					foreach ($v["options"] as $optionk => $optionv) {
						?>												<option value="<?php echo $optionk;?>"><?php echo $optionv;?></option>
											<?php 
					}
					?>										</select>
									<?php 
				}
				?>								</div>
							</div>
				<?php 
			}
		}
	}
	?>				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
	</div>
<?php 
} elseif ($mod == "oauth") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">快捷登录配置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-3 control-label">QQ快捷登录方式</label>
					<div class="col-sm-9"><select class="form-control" name="login_qq" default="<?php echo $conf["login_qq"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
							<option value="2">手机QQ扫码登录</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">微信快捷登录方式</label>
					<div class="col-sm-9"><select class="form-control" name="login_wx" default="<?php echo $conf["login_wx"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">支付宝快捷登录</label>
					<div class="col-sm-9"><select class="form-control" name="login_alipay" default="<?php echo $conf["login_alipay"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">抖音快捷登录</label>
					<div class="col-sm-9"><select class="form-control" name="login_douyin" default="<?php echo $conf["login_douyin"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">百度快捷登录</label>
					<div class="col-sm-9"><select class="form-control" name="login_baidu" default="<?php echo $conf["login_baidu"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">微博快捷登录</label>
					<div class="col-sm-9"><select class="form-control" name="login_weibo" default="<?php echo $conf["login_weibo"];?>">
							<option value="0">关闭</option>
							<option value="1">安享聚合登录</option>
						</select></div>
				</div>
				<div id="frame_set3" style="display:none;">
					<div class="form-group">
						<label class="col-sm-3 control-label">API接口地址</label>
						<div class="col-sm-9"><input type="text" name="login_apiurl" value="<?php echo $conf["login_apiurl"];?>" class="form-control" placeholder="API地址要以http://或https://开头，以/结尾" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">应用APPID</label>
						<div class="col-sm-9"><input type="text" name="login_appid" value="<?php echo $conf["login_appid"];?>" class="form-control" /></div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">应用APPKEY</label>
						<div class="col-sm-9"><input type="text" name="login_appkey" value="<?php echo $conf["login_appkey"];?>" class="form-control" /></div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			QQ快捷登录接口是使用安享聚合登录系统搭建的站点，并非QQ互联官方接口。<br />
			QQ快捷登录开启后请勿随意更换登录API站点，否则会导致之前以QQ快捷登录注册的用户全部无法登录。<br />
			手机QQ扫码登录使用更方便，登录凭证以用户注册时填写的QQ为准
		</div>
	</div>
	<script>
		$("select[name='login_qq']").change(function() {
			if ($("select[name='login_qq']").val() == 1 || $("select[name='login_wx']").val() == 1) {
				$("#frame_set3").css("display", "inherit");
			} else {
				$("#frame_set3").css("display", "none");
			}
		});
		$("select[name='login_wx']").change(function() {
			if ($("select[name='login_qq']").val() == 1 || $("select[name='login_wx']").val() == 1) {
				$("#frame_set3").css("display", "inherit");
			} else {
				$("#frame_set3").css("display", "none");
			}
		});
	</script>
<?php 
} elseif ($mod == "captcha_n" && $_POST["do"] == "submit") {
	adminpermission("set", 1);
	$captcha_open = $_POST["captcha_open"];
	$captcha_id = $_POST["captcha_id"];
	$captcha_key = $_POST["captcha_key"];
	$captcha_open_free = $_POST["captcha_open_free"];
	$captcha_open_reg = $_POST["captcha_open_reg"];
	$captcha_open_login = $_POST["captcha_open_login"];
	$login_limit_enable = $_POST["login_limit_enable"];
	$login_limit_max = $_POST["login_limit_max"];
	$login_limit_time = $_POST["login_limit_time"];
	saveSetting("captcha_open", $captcha_open);
	saveSetting("captcha_id", $captcha_id);
	saveSetting("captcha_key", $captcha_key);
	saveSetting("captcha_open_free", $captcha_open_free);
	saveSetting("captcha_open_reg", $captcha_open_reg);
	saveSetting("captcha_open_login", $captcha_open_login);
	saveSetting("login_limit_enable", $login_limit_enable);
	saveSetting("login_limit_max", $login_limit_max);
	saveSetting("login_limit_time", $login_limit_time);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "captcha") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">滑动验证码设置</h3>
		</div>
		<div class="">
			<form action="./set.php?mod=captcha_n" method="post" class="form-horizontal form-bordered" role="form"><input type="hidden" name="do" value="submit" />
				<div class="form-group">
					<label class="col-sm-2 control-label">验证码选择</label>
					<div class="col-sm-10"><select class="form-control" name="captcha_open" default="<?php echo $conf["captcha_open"];?>">
							<option value="0">关闭</option>
							<option value="1">极限滑动验证码</option>
							<option value="2">顶象滑动验证码</option>
							<option value="3">VAPTCHA手势验证码</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">接口ID</label>
					<div class="col-sm-10"><input type="text" name="captcha_id" value="<?php echo $conf["captcha_id"];?>" class="form-control" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">接口KEY</label>
					<div class="col-sm-10"><input type="text" name="captcha_key" value="<?php echo $conf["captcha_key"];?>" class="form-control" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">开启验证场景</label>
					<div class="col-sm-10">
						<label><input name="captcha_open_free" type="checkbox" value="1" <?php echo $conf["captcha_open_free"] == 1 ? " checked" : null;?> /> 购买免费商品</label><br />
						<label><input name="captcha_open_reg" type="checkbox" value="1" <?php echo $conf["captcha_open_reg"] == 1 ? " checked" : null;?> /> 用户注册</label><br />
						<label><input name="captcha_open_login" type="checkbox" value="1" <?php echo $conf["captcha_open_login"] == 1 ? " checked" : null;?> /> 用户登录</label><br />
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			极限验证码：<a href="https://www.geetest.com/Register" rel="noreferrer" target="_blank">点击进入</a> （免费版每小时限流，需人工审核）<br />
			顶象验证码：<a href="https://www.dingxiang-inc.com/business/captcha" rel="noreferrer" target="_blank">点击进入</a> （收费的，可免费试用）<br />
			VAPTCHA手势验证码：<a href="https://www.vaptcha.com/" rel="noreferrer" target="_blank">点击进入</a> （目前完全免费）<br />
			选择极限验证码，然后ID和KEY留空保存，即可直接免费使用公共接口(测试中)
		</div>
	</div>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">登录失败限制设置</h3>
		</div>
		<div class="">
			<form action="./set.php?mod=captcha_n" method="post" class="form-horizontal form-bordered" role="form"><input type="hidden" name="do" value="submit" />
				<div class="form-group">
					<label class="col-sm-3 control-label">启用登录失败限制</label>
					<div class="col-sm-9"><select class="form-control" name="login_limit_enable" default="<?php echo isset($conf['login_limit_enable'])?$conf['login_limit_enable']:0;?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select>
						<span class="help-block">开启后，当用户登录密码连续错误达到指定次数时，将限制该IP一段时间内不能登录</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">允许密码错误次数</label>
					<div class="col-sm-9"><input type="number" name="login_limit_max" value="<?php echo isset($conf['login_limit_max'])?$conf['login_limit_max']:5;?>" class="form-control" min="1" max="100" />
						<span class="help-block">连续密码错误达到此次数后，将限制登录。建议设置为3-10次</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">限制登录时长（分钟）</label>
					<div class="col-sm-9"><input type="number" name="login_limit_time" value="<?php echo isset($conf['login_limit_time'])?$conf['login_limit_time']:30;?>" class="form-control" min="1" max="1440" />
						<span class="help-block">密码错误次数达到后，限制登录的时长。单位：分钟，建议设置为15-60分钟</span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			登录失败限制功能说明：<br />
			- 根据IP地址进行限制，同一IP在规定时间内密码连续错误达到次数后将无法登录<br />
			- 限制时间到期后会自动解除限制<br />
			- 建议根据网站安全需求合理设置次数和时长
		</div>
	</div>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">用户IP地址获取设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">用户IP地址获取方式</label>
					<div class="col-sm-10"><select class="form-control" name="ip_type" default="<?php echo $conf["ip_type"];?>"></select></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			此功能设置用于防止用户伪造IP请求造成免费商品无限制下单。<br />
			X_FORWARDED_FOR：之前的获取真实IP方式，极易被伪造IP<br />
			X_REAL_IP：在网站使用CDN的情况下选择此项，在不使用CDN的情况下也会被伪造<br />
			REMOTE_ADDR：直接获取真实请求IP，无法被伪造，但可能获取到的是CDN节点IP<br />
			<b>你可以从中选择一个能显示你真实地址的IP，优先选下方的选项。</b>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$.ajax({
				type: "GET",
				url: "ajax.php?act=iptype",
				dataType: 'json',
				async: true,
				success: function(data) {
					$("select[name='ip_type']").empty();
					var defaultv = $("select[name='ip_type']").attr('default');
					$.each(data, function(k, item) {
						$("select[name='ip_type']").append('<option value="' + k + '" ' + (defaultv == k ? 'selected' : '') + '>' + item.name + ' - ' + item.ip + ' ' + item.city + '</option>');
					})
				}
			});
		})
	</script>
<?php 
} elseif ($mod == "delwxqrcode") {
	adminpermission("set", 1);
	if (file_exists(ROOT . "assets/img/wxqrcode.png")) {
		unlink(ROOT . "assets/img/wxqrcode.png");
	}
	exit("<script language='javascript'>alert('删除成功');window.location.href='./uset.php?mod=upwxqrcode';</script>");
} elseif ($mod == "upwxqrcode") {
	adminpermission("set", 1);
	if ($_POST["s"] == 1) {
		// 检查文件类型
		$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
		$file_type = $_FILES["file"]["type"];
		$file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
		$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
		
		if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
			showmsg("只允许上传JPG、PNG、GIF、WEBP格式的图片文件！", 4);
		}
		
		// 检查文件大小（限制为2MB）
		$max_size = 2 * 1024 * 1024;
		if ($_FILES["file"]["size"] > $max_size) {
			showmsg("文件大小不能超过2MB！", 4);
		}
		
		// 检查文件是否为真实图片
		$image_info = getimagesize($_FILES["file"]["tmp_name"]);
		if (!$image_info) {
			showmsg("请上传真实的图片文件！", 4);
		}
		
		// 保存文件，使用固定文件名
		if (move_uploaded_file($_FILES["file"]["tmp_name"], ROOT . "assets/img/wxqrcode.png")) {
			compress_image(ROOT . "assets/img/wxqrcode.png"); // 自动压缩，加快前端加载
			showmsg("上传成功！", 1);
		} else {
			showmsg("上传失败！", 4);
		}
	}
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">上传客服微信二维码</h3>
		</div>
		<div class="">
		
			<form action="set.php?mod=upwxqrcode" method="POST" enctype="multipart/form-data">
				<label for="file"></label>
				<input type="file" name="file" id="file" accept="image/*" />
				<input type="hidden" name="s" value="1" /><br>
				<input type="submit" class="btn btn-primary btn-block" value="确认上传" /><br />
				<a href="./set.php?mod=delwxqrcode" class="btn btn-danger btn-block btn-sm">删除图片</a>
			</form><br>
			现在的客服微信二维码：<br><img src="../assets/img/wxqrcode.png?r=<?php echo rand(10000, 99999);?>" style="max-width:30%">
		</div>
	</div>
<?php 
} elseif ($mod == "upimg") {
	adminpermission("set", 1);
	if ($_POST["s"] == 1) {
		// 检查文件类型
		$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
		$file_type = $_FILES["file"]["type"];
		$file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
		$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
		
		if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
			showmsg("只允许上传JPG、PNG、GIF、WEBP格式的图片文件！", 4);
		}
		
		// 检查文件大小（限制为2MB）
		$max_size = 2 * 1024 * 1024;
		if ($_FILES["file"]["size"] > $max_size) {
			showmsg("文件大小不能超过2MB！", 4);
		}
		
		// 检查文件是否为真实图片
		$image_info = getimagesize($_FILES["file"]["tmp_name"]);
		if (!$image_info) {
			showmsg("请上传真实的图片文件！", 4);
		}
		
		// 保存文件，使用固定文件名
		if (move_uploaded_file($_FILES["file"]["tmp_name"], ROOT . "assets/img/logo.png")) {
			compress_image(ROOT . "assets/img/logo.png"); // 自动压缩，加快前端加载
			showmsg("上传成功！", 1);
		} else {
			showmsg("上传失败！", 4);
		}
	}
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">更改首页LOGO</h3>
			<div class="block-options pull-right"><a class="btn btn-default" href="set.php?mod=upbgimg">更改背景图</a></div>
		</div>
		<div class="">
			<form action="set.php?mod=upimg" method="POST" enctype="multipart/form-data">
				<label for="file"></label>
				<input type="file" name="file" id="file" accept="image/*" />
				<input type="hidden" name="s" value="1" /><br>
				<input type="submit" class="btn btn-primary btn-block" value="确认上传" />
			</form>
			<br>现在的图片：<br>
			<img src="../assets/img/logo.png?r=<?php echo rand(10000, 99999);?>" style="max-width:100%">
		</div>
	</div>
<?php 
} elseif ($mod == "upbgimg") {
	adminpermission("set", 1);
	if ($_POST["s"] == 1) {
		// 检查文件类型
		$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
		$file_type = $_FILES["file"]["type"];
		$file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
		$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
		
		if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
			showmsg("只允许上传JPG、PNG、GIF、WEBP格式的图片文件！", 4);
		}
		
		// 检查文件大小（限制为5MB）
		$max_size = 5 * 1024 * 1024;
		if ($_FILES["file"]["size"] > $max_size) {
			showmsg("文件大小不能超过5MB！", 4);
		}
		
		// 检查文件是否为真实图片
		$image_info = getimagesize($_FILES["file"]["tmp_name"]);
		if (!$image_info) {
			showmsg("请上传真实的图片文件！", 4);
		}
		
		// 保存文件，使用固定文件名
		if (move_uploaded_file($_FILES["file"]["tmp_name"], ROOT . "assets/img/bj.png")) {
			compress_image(ROOT . "assets/img/bj.png"); // 自动压缩，加快前端加载
			showmsg("上传成功！", 1);
		} else {
			showmsg("上传失败！", 4);
		}
	}
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">更改首页背景图</h3>
			<div class="block-options pull-right"><a class="btn btn-default" href="set.php?mod=upimg">更改LOGO</a></div>
		</div>
		<div class="">
		
			<form action="set.php?mod=upbgimg" method="POST" enctype="multipart/form-data">
				<label for="file"></label>
				<input type="file" name="file" id="file" accept="image/*" />
				<input type="hidden" name="s" value="1" /><br>
				<input type="submit" class="btn btn-primary btn-block" value="确认上传" />
			</form><br>现在的图片：<br><img src="../assets/img/bj.png?r=<?php echo rand(10000, 99999);?>" style="max-width:100%">
		</div>
	</div>
<?php 
} elseif ($mod == "cron") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">计划任务配置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-sm-3 control-label">监控密钥</label>
					<div class="col-sm-9"><input type="text" name="cronkey" value="<?php echo $conf["cronkey"];?>" class="form-control" placeholder="" /></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
					</div><br />
				</div>
			</form>
		</div>
	</div>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">计划任务列表</h3>
		</div>
		<div class="">
			<p>请按自己的需要监控以下网址。只能在一个地方监控，千万不要多节点监控或在多处监控，否则会导致数据错乱！</p>
			<p>每日数据库维护+排行榜奖励发放+提成余额延迟到账（每天0点后执行2次）：</p>
			<li class="list-group-item"><?php echo $siteurl;?>cron.php?do=daily&amp;key=<?php echo $conf["cronkey"];?></li>
			</br />
			<p>社区价格监控（10到60分钟一次）：</p>
			<li class="list-group-item"><?php echo $siteurl;?>cron.php?do=pricejk&amp;key=<?php echo $conf["cronkey"];?></li>
			</br />
			<p>易支付订单补单监控（如果易支付订单已完成，但本站无订单可以监控以下url补单，大约5分钟一次。如果易支付订单也显示未完成，或者其他情况使用该监控均无法补单）：</p>
			<li class="list-group-item"><?php echo $siteurl;?>cron.php?key=<?php echo $conf["cronkey"];?>&id=1</li>
			<li class="list-group-item"><?php echo $siteurl;?>cron.php?key=<?php echo $conf["cronkey"];?>&id=2</li>
			</br />
			<p>订单状态检测：</p>
			<li class="list-group-item"><?php echo $siteurl;?>cron.php?do=updatestatus&amp;key=<?php echo $conf["cronkey"];?></li>
			</br />
		</div>
	</div>
<?php 
} elseif ($mod == "qiandao") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">签到模块设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">奖励余额初始值</label>
					<div class="col-sm-10"><input type="text" name="qiandao_reward" value="<?php echo $conf["qiandao_reward"];?>" class="form-control" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">每日递增倍数</label>
					<div class="col-sm-10"><input type="text" name="qiandao_mult" value="<?php echo $conf["qiandao_mult"];?>" class="form-control" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">最多递增天数</label>
					<div class="col-sm-10"><input type="text" name="qiandao_day" value="<?php echo $conf["qiandao_day"];?>" class="form-control" /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">是否限制每个IP签到一次</label>
					<div class="col-sm-10"><select class="form-control" name="qiandao_limitip" default="<?php echo $conf["qiandao_limitip"];?>">
							<option value="0">否</option>
							<option value="1">是</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">是否开启虚拟签到消息</label>
					<div class="col-sm-10"><select class="form-control" name="qiandao_virtual" default="<?php echo $conf["qiandao_virtual"];?>">
							<option value="0">否</option>
							<option value="1">是</option>
						</select></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>奖励余额初始值填写一个值代表所有类型分站都一样，填写3个值并用|隔开代表不同类型分站不一样，例如0.01|0.02|0.03 分别是普通用户、普及版分站、专业版分站的奖励余额初始值。
		</div>
	</div>
	<div class="block">
		<div class="block-title">
			<h3 class="panel-title" id="title">签到统计</h3>
		</div>
		<div class="">
			<table class="table table-bordered">
				<tbody>
					<tr>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-user-circle-o"></i> 今日签到<br><span id="count1"></span>人
						</th>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-user-circle"></i> 昨日签到<br><span id="count2"></span>人
						</th>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-pie-chart"></i> 累计签到<br><span id="count3"></span>人
						</th>
					</tr>
					<tr>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-money"></i> 今日送出余额<br><span id="count4"></span>元
						</th>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-money"></i> 昨日送出余额<br><span id="count5"></span>元
						</th>
						<th style="font-size: 13px;" class="text-center">
							<i class="fa fa-bar-chart"></i> 累计送出余额<br><span id="count6"></span>元
						</th>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$('#title').html('正在加载数据中...');
			$.ajax({
				type: "GET",
				url: "ajax.php?act=qdcount",
				dataType: 'json',
				async: true,
				success: function(data) {
					$('#count1').html(data.count1);
					$('#count2').html(data.count2);
					$('#count3').html(data.count3);
					$('#count4').html(data.count4);
					$('#count5').html(data.count5);
					$('#count6').html(data.count6);
					$('#title').html('签到统计');
				}
			});
		})
	</script>
<?php 
} elseif ($mod == "invitegift") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">邀请返利设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form" role="form">
				<div class="form-group">
					<label>开启邀请返利功能</label>
					<select class="form-control" name="invitegift_open" default="<?php echo $conf["invitegift_open"];?>">
						<option value="0">关闭</option>
						<option value="1">开启</option>
					</select>
				</div>
				<div class="form-group">
					<label>广告语自定义</label>
					<textarea class="form-control" name="invitegift_content" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["invitegift_content"]);?></textarea>
					<pre>其中，商品名称用[name]，商品价格用[price]，返利链接用[url]，会自动替换</pre>
				</div>
				<div class="form-group">
					<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
					<a href="./invitegift.php" class="btn btn-info btn-block">进入返利商品列表</a>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			开启后，前台分享商品海报，分享商品链接，只要是返利商品便自动切换成返利链接
		</div>
	</div>
<?php 
} elseif ($mod == "invite") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">推广链接设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form" role="form">
				<div class="form-group">
					<label>开启推广链接功能</label>
					<select class="form-control" name="invite_tid" default="<?php echo $conf["invite_tid"];?>">
						<option value="0">关闭</option>
						<option value="1">开启</option>
					</select>
				</div>
				<div class="form-group">
					<label>广告语自定义</label>
					<textarea class="form-control" name="invite_content" rows="5" style="width:100%;"><?php echo htmlspecialchars($conf["invite_content"]);?></textarea>
					<pre>其中，推广链接用[url]，会自动替换</pre>
				</div>
				<div class="form-group">
					<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
					<a href="./invite.php" class="btn btn-info btn-block">进入推广商品列表</a><br />
					<a href="./invitelog.php" class="btn btn-default btn-block">查看推广记录</a>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			推广链接生成地址：/?mod=invite<br />
			推广页面模板文件：/template/default/invite.php<br />
			如果使用累计次数推广模式，建议先设置好<a href="./set.php?mod=captcha">用户IP地址获取设置</a>，相同IP地址算一次访问。
		</div>
	</div>
<?php 
} elseif ($mod == "dwz") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">防红链接生成接口设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form" role="form">
			    <div class="form-group">
            	    <label>防红接口选择：<a href="https://404.6v6.ren/fang-hon/1.html" target="_blank">防红推荐</a></label>
            		<select class="form-control" name="fanghong_api" default="<?php echo $conf["fanghong_api"];?>">
            		  <option value="0">不使用防红接口</option>
            		  <option value="1">安享官方防红</option>
            		  <option value="9">自定义防红接口</option>
            		</select>
            	</div>
            	<div class="form-group" id="fanghong_gf" style="<?php echo $conf["fanghong_api"] == 9 || $conf["fanghong_api"] == 0 ? "display:none;" : NULL;?>">
            	    <div class="form-group">
                        <div class="input-group">
                            
                            <div class="input-group-addon">短网址类型</div>
                            <input type="text" name="fanghong_gftype" value="<?php echo $conf["fanghong_gftype"];?>" class="form-control" placeholder="例如：留空默认为网易163接口">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">短网址模式</div>
                            <input type="text" name="fanghong_gfpattern" value="<?php echo $conf["fanghong_gfpattern"];?>" class="form-control" placeholder="默认为3">
                        </div>
                        <div class="alert alert-info">短网址模式（普通：1，防红：2，直链：3）</div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">token</div>
                            <input type="text" name="fanghong_gftoken" value="<?php echo $conf["fanghong_gftoken"];?>" class="form-control" placeholder="token" required="required">
                        </div>
                    </div>
            	    
        	    </div>
                <div class="form-group" id="fanghong_diy" style="<?php echo $conf["fanghong_api"] != 9 ? "display:none;" : NULL;?>">
    				<div class="form-group">
    					<div class="input-group">
    						<div class="input-group-addon">防红API</div>
    						<input type="text" name="fanghong_url" value="<?php echo $conf["fanghong_url"];?>" class="form-control" placeholder="不填写则关闭防红链接生成" />
    						<div class="input-group-addon" onclick="checkurl()"><small>检测地址</small></div>
    					</div>
    				</div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">返回值</div>
                            <input type="text" name="fanghong_key" value="<?php echo $conf["fanghong_key"];?>" class="form-control" placeholder="返回生成URL的键名，一般为url">
                        </div>
                    </div>
                 </div>
				<div class="form-group">
					<input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br />
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			一般防红接口地址为 http://防红网站域名/dwz.php?longurl= 具体可以咨询相应站长
		</div>
	</div>
	<?php 
	if ($conf["fanghong_api"] != 0) {
		?>		<div class="block">
			<div class="block-title">
				<h3 class="panel-title">获取防红链接</h3>
			</div>
			<div class="">
				<div class="input-group">
					<span class="input-group-addon">防红网址</span>
					<input class="form-control" id="longurl" value="<?php echo (is_https() ? "https://" : "http://") . $_SERVER["HTTP_HOST"];?>">
				</div>
				<div class="well well-sm">如果您的网址在QQ内报毒或者打不开，您可以使用此功能生成防毒链接！</div>
				<a class="btn btn-block btn-success" id="create_url">生成我的防红链接</a>
			</div>
		</div>
	<?php 
	}
	?>	<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
	<script src="<?php echo $cdnpublic;?>clipboard.js/1.7.1/clipboard.min.js"></script>
	<div class="modal fade in" id="fanghongurl" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
					<h4 class="modal-title">防红链接生成</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">防红链接</div><input type="text" id="target_url" value="" class="form-control" readOnly="readOnly">
						</div>
					</div>
					<center><button class="btn btn-info btn-sm" id="recreate_url">重新生成</button>&nbsp;<button class="btn btn-warning btn-sm copy-btn" id="copyurl" data-clipboard-text="">一键复制链接</button></center>
				</div>
				<div class="modal-footer"><button type="button" class="btn btn-white" data-dismiss="modal">关闭</button></div>
			</div>
		</div>
	</div>
	<script>
		function checkurl() {
			var url = $("input[name='fanghong_url']").val();
			if (url.indexOf('http') >= 0 && url.indexOf('=') >= 0) {
				var ii = layer.load(2, {
					shade: [0.1, '#fff']
				});
				$.ajax({
					type: "POST",
					url: "ajax.php?act=checkdwz",
					data: {
						url: url
					},
					dataType: 'json',
					success: function(data) {
						layer.close(ii);
						if (data.code == 1) {
							layer.msg('检测正常');
						} else if (data.code == 2) {
							layer.alert('链接无法访问或返回内容不是json格式');
						} else {
							layer.alert('该链接无法访问');
						}
					},
					error: function(data) {
						layer.close(ii);
						layer.msg('目标URL连接超时');
						return false;
					}
				});
			} else {
				layer.alert('链接地址错误');
			}
		}
		$(document).ready(function() {

$("select[name='fanghong_api']").change(function(){
	if($(this).val() == 9){
		$("#fanghong_diy").css("display","inherit");
		$("#fanghong_gf").css("display","none");
		
	}else if($(this).val() == 0){
		$("#fanghong_diy").css("display","none");
		$("#fanghong_gf").css("display","none");
		
	}else{
	    $("#fanghong_diy").css("display","none");
	    $("#fanghong_gf").css("display","inherit");
	}
	
});

			var clipboard = new Clipboard('.copy-btn', {
				container: document.getElementById('fanghongurl')
			});
			clipboard.on('success', function(e) {
				layer.msg('复制成功！');
			});
			clipboard.on('error', function(e) {
				layer.msg('复制失败，请长按链接后手动复制');
			});
			var clipboard = new Clipboard('.copy-btn', {
				container: document.getElementById('fanghongvip')
			});
			clipboard.on('success', function(e) {
				layer.msg('复制成功！');
			});
			clipboard.on('error', function(e) {
				layer.msg('复制失败，请长按链接后手动复制');
			});
			$("#create_url").click(function() {
				var self = $(this);
				if (self.attr("data-lock") === "true") return;
				else self.attr("data-lock", "true");
				var url = $("input[id='longurl']").val();
				if (url == '') {
					layer.alert('网址不能为空');
					return false;
				}
				var ii = layer.load(1, {
					shade: [0.1, '#fff']
				});
				$.get("ajax.php?act=create_url&force=1&longurl=" + encodeURIComponent(url), function(data) {
					layer.close(ii);
					if (data.code == 0) {
						$("#target_url").val(data.url);
						$("#copyurl").attr('data-clipboard-text', data.url);
						$('#fanghongurl').modal('show');
					} else {
						layer.alert(data.msg);
					}
					self.attr("data-lock", "false");
				}, 'json');
			});
			$("#recreate_url").click(function() {
				var self = $(this);
				if (self.attr("data-lock") === "true") return;
				else self.attr("data-lock", "true");
				var url = $("input[id='longurl']").val();
				if (url == '') {
					layer.alert('网址不能为空');
					return false;
				}
				var ii = layer.load(1, {
					shade: [0.1, '#fff']
				});
				$.get("ajax.php?act=create_url&force=1&longurl=" + encodeURIComponent(url), function(data) {
					layer.close(ii);
					if (data.code == 0) {
						layer.msg('生成链接成功');
						$("#target_url").val(data.url);
						$("#copyurl").attr('data-clipboard-text', data.url);
					} else {
						layer.alert(data.msg);
					}
					self.attr("data-lock", "false");
				}, 'json');
			});
		});
	</script>
<?php 
} elseif ($mod == "mailcon_reset") {
	adminpermission("set", 1);
	$faka_mail = "<b>商品名称：</b> [name]<br/><b>购买时间：</b>[date]<br/><b>以下是你的卡密信息：</b><br/>[kmdata]<br/>----------<br/><b>使用说明：</b><br/>[alert]<br/>----------<br/>安享云商城自助下单平台<br/>[domain]";
	saveSetting("faka_mail", $faka_mail);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "mailcon") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">发信邮件模板设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">发卡邮件模板</label>
					<div class="col-sm-10"><textarea class="form-control" name="faka_mail" id="faka_mail" rows="6"><?php echo htmlspecialchars($conf["faka_mail"]);?></textarea></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" /><br /><br />
						<a href="./set.php?mod=mailcon_reset" class="btn btn-warning btn-block" onclick="return confirm('确定要重置吗？');">重置模板设置</a><br />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<font color="green">变量代码：<br />
				<a href="#" onclick="Addstr('faka_mail','[kmdata]');return false">[kmdata]</a>&nbsp;卡密内容<br />
				<a href="#" onclick="Addstr('faka_mail','[name]');return false">[name]</a>&nbsp;商品名称<br />
				<a href="#" onclick="Addstr('faka_mail','[alert]');return false">[alert]</a>&nbsp;商品简介<br />
				<a href="#" onclick="Addstr('faka_mail','[date]');return false">[date]</a>&nbsp;购买时间<br />
				<a href="#" onclick="Addstr('faka_mail','[email]');return false">[email]</a>&nbsp;收信人邮箱<br />
			</font>
		</div>
	</div>
	<script>
		function Addstr(id, str) {
			$("#" + id).val($("#" + id).val() + str);
		}
	</script>
<?php 
} elseif ($mod == "cloneset") {
	adminpermission("set", 1);
	$key = md5($password_hash . md5(SYS_KEY) . $conf["apikey"]);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">克隆站点配置</h3>
		</div>
		<div class="">
			<form class="form-horizontal" role="form"><input type="hidden" name="do" value="submit" />
				<div class="form-group">
					<label class="col-sm-2 control-label">克隆密钥</label>
					<div class="col-sm-10"><input type="text" name="key" value="<?php echo $key;?>" class="form-control" disabled /></div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			此密钥是用于其他站点克隆本站商品<br />
			提示：修改API对接密钥可同时重置克隆密钥。<br />
		</div>
	</div>
<?php 
} elseif ($mod == "rewrite") {
	adminpermission("set", 1);
	$SERVER_SOFTWARE = explode("/", $_SERVER["SERVER_SOFTWARE"]);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">伪静态设置</h3>
		</div>
		<div class="">
			<form onsubmit="return saveRewrite(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">伪静态开关</label>
					<div class="col-sm-10"><select class="form-control" name="article_rewrite" default="<?php echo $conf["article_rewrite"];?>">
							<option value="0">关闭</option>
							<option value="1">开启</option>
						</select></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
		<div class="panel-footer">
			<span class="glyphicon glyphicon-info-sign"></span>
			当前服务器Web软件为：<?php echo ucwords($SERVER_SOFTWARE[0]);?><br />
			请将以下内容添加到伪静态规则
			<pre>location / {
 if (!-e $request_filename) {
   rewrite ^/article-(.[0-9]*).html$ /index.php?mod=article&id=$1 last;
   rewrite ^/(.[a-zA-Z0-9\-\_]+).html$ /index.php?mod=$1 last;
 }
}</pre>
		</div>
	</div>

	<script>
		function saveRewrite(obj) {
			var ii = layer.load(2, {
				shade: [0.1, '#fff']
			});
			$.ajax({
				type: 'POST',
				url: 'ajax.php?act=rewrite',
				data: $(obj).serialize(),
				dataType: 'json',
				success: function(data) {
					layer.close(ii);
					if (data.code == 0) {
						layer.alert('设置伪静态成功！', {
							icon: 1,
							closeBtn: false
						}, function() {
							window.location.reload()
						});
					} else {
						layer.alert(data.msg, {
							icon: 2
						})
					}
				},
				error: function(data) {
					layer.msg('服务器错误');
					return false;
				}
			});
			return false;
		}
	</script>

<?php 
} elseif ($mod == "map") {
	adminpermission("set", 1);
	?>	<div class="block">
		<div class="block-title">
			<h3 class="panel-title">网站地图生成工具</h3>
		</div>
		<div class="">
			<div class="alert alert-info">使用此功能可一键生成网站XML地图[map.xml]，方便站长自动推送网站页面，提升SEO效益</div>
			<form onsubmit="return saveMap(this)" method="post" class="form-horizontal form-bordered" role="form">
				<div class="form-group">
					<label class="col-sm-2 control-label">生成类型</label>
					<div class="col-sm-10"><select class="form-control" name="map_type">
							<option value="1">商品分类(仅上架中的)</option>
							<option value="2">商品列表(仅上架中的)</option>
							<option value="3">文章列表(仅已显示的)</option>
						</select></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">URL规则</label>
					<div class="col-sm-10"><input type="text" name="map_urlpattern" value="http://[siteurl]/class/[cid].html" class="form-control" placeholder="http://[siteurl]/class/[cid].html" required /></div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">优先权值</label>
					<div class="col-sm-10"><input type="text" name="map_priority" value="" class="form-control" placeholder="优先权标签，优先权值0.0-1.0" required /></div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="提交操作" class="btn btn-primary btn-block" />
					</div>
				</div>
			</form>
		</div>
	</div>
	</div>
	<script>
		function saveMap(obj) {
			var map_urlpattern = $("input[name='map_urlpattern']").val();
			if (map_urlpattern.indexOf('http') >= 0) {
				var ii = layer.load(2, {
					shade: [0.1, '#fff']
				});
				$.ajax({
					type: 'POST',
					url: 'ajax.php?act=map',
					data: $(obj).serialize(),
					dataType: 'json',
					success: function(data) {
						layer.close(ii);
						if (data.code == 0) {
							layer.msg(data.msg);
						} else {
							layer.alert(data.msg);
						}
					},
					error: function(data) {
						layer.close(ii);
						layer.msg('目标URL连接超时');
						return false;
					}
				});
			} else {
				layer.alert('URL规则必须以 http:// 开头，以 / 结尾');
			}
			return false;
		}
	</script>
<?php 
} elseif ($mod == "chat") {
    adminpermission("set", 1);
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['do']) && $_POST['do'] == 'save') {
        // 保存客服系统基本设置
        $chat_enable = intval($_POST['chat_enable']);
        $chat_title = trim($_POST['chat_title']);
        $chat_welcome = trim($_POST['chat_welcome']);
        $chat_polling = intval($_POST['chat_polling']);
        $chat_autoclose = intval($_POST['chat_autoclose']);
        $chat_auto_delete = intval($_POST['chat_auto_delete']);
        $chat_delete_days = intval($_POST['chat_delete_days']);
        $chat_btn_color = trim($_POST['chat_btn_color']);
        $chat_window_color = trim($_POST['chat_window_color']);
        $chat_sound = intval($_POST['chat_sound']);
        $chat_style = intval($_POST['chat_style']);
        $chat_base_path = trim($_POST['chat_base_path']);
        saveSetting('chat_enable', $chat_enable);
        saveSetting('chat_title', $chat_title);
        saveSetting('chat_welcome', $chat_welcome);
        saveSetting('chat_polling', $chat_polling);
        saveSetting('chat_autoclose', $chat_autoclose);
        saveSetting('chat_auto_delete', $chat_auto_delete);
        saveSetting('chat_delete_days', $chat_delete_days);
        saveSetting('chat_btn_color', $chat_btn_color);
        saveSetting('chat_window_color', $chat_window_color);
        saveSetting('chat_sound', $chat_sound);
        saveSetting('chat_style', $chat_style);
        saveSetting('chat_base_path', $chat_base_path);
        $chat_image_limit_enable = intval($_POST['chat_image_limit_enable']);
        $chat_image_max_size = intval($_POST['chat_image_max_size']);
        $chat_image_formats = trim($_POST['chat_image_formats']);
        saveSetting('chat_image_limit_enable', $chat_image_limit_enable);
        saveSetting('chat_image_max_size', $chat_image_max_size);
        saveSetting('chat_image_formats', $chat_image_formats);
        $chat_video_enable = intval($_POST['chat_video_enable']);
        $chat_video_max_size = intval($_POST['chat_video_max_size']);
        saveSetting('chat_video_enable', $chat_video_enable);
        saveSetting('chat_video_max_size', $chat_video_max_size);
        $chat_anti_spam_enable = intval($_POST['chat_anti_spam_enable']);
        $chat_max_messages = intval($_POST['chat_max_messages']);
        $chat_first_ban_time = intval($_POST['chat_first_ban_time']);
        $chat_second_ban_time = intval($_POST['chat_second_ban_time']);
        saveSetting('chat_anti_spam_enable', $chat_anti_spam_enable);
        saveSetting('chat_max_messages', $chat_max_messages);
        saveSetting('chat_first_ban_time', $chat_first_ban_time);
        saveSetting('chat_second_ban_time', $chat_second_ban_time);
        $ad = $CACHE->clear();
        if ($ad) {
            showmsg('客服系统设置保存成功！', 1);
        } else {
            showmsg('保存失败！<br/>' . $DB->error(), 4);
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['do']) && $_POST['do'] == 'save_keyword') {
        // 保存关键词自动回复设置
        $chat_keyword_enable = intval($_POST['chat_keyword_enable']);
        $chat_keywords = trim($_POST['chat_keywords']);
        saveSetting('chat_keyword_enable', $chat_keyword_enable);
        saveSetting('chat_keywords', $chat_keywords);
        $ad = $CACHE->clear();
        if ($ad) {
            showmsg('关键词自动回复设置保存成功！', 1);
        } else {
            showmsg('保存失败！<br/>' . $DB->error(), 4);
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['do']) && $_POST['do'] == 'save_prohibited') {
        // 保存违禁词过滤设置
        $chat_prohibited_enable = intval($_POST['chat_prohibited_enable']);
        $chat_prohibited_words = trim($_POST['chat_prohibited_words']);
        $chat_prohibited_msg = trim($_POST['chat_prohibited_msg']);
        saveSetting('chat_prohibited_enable', $chat_prohibited_enable);
        saveSetting('chat_prohibited_words', $chat_prohibited_words);
        saveSetting('chat_prohibited_msg', $chat_prohibited_msg);
        $ad = $CACHE->clear();
        if ($ad) {
            showmsg('违禁词过滤设置保存成功！', 1);
        } else {
            showmsg('保存失败！<br/>' . $DB->error(), 4);
        }
    }
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">客服系统设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>如何在网站中添加客服系统？</b><br>
                1. 在需要显示客服系统的PHP页面中，找到已有的<?php ?>标签，在其中添加如下代码：<br>
                <code>include ROOT."template/default/chat/widget.php";</code><br>
                2. 添加后将在页面右下角显示客服图标，用户点击即可打开对话窗口<br>
                3. 管理员可在 客服工作台 查看和回复用户消息<br>
                4. 请先在下方开启客服系统并完成相关配置
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form">
            <input type="hidden" name="do" value="save">
            <div class="form-group">
                <label class="col-sm-2 control-label">客服系统开关</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_enable" default="<?php echo $conf['chat_enable'] ?? 0;?>">
                        <option value="1" <?php if($conf['chat_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客服系统基础路径</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="chat_base_path" value="<?php echo htmlspecialchars($conf['chat_base_path'] ?? '');?>" placeholder="留空则自动检测，例如：/shop">
                    <span class="help-block">设置客服系统的基础路径，适用于网站放在子目录的情况，留空则自动检测</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客服窗口标题</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="chat_title" value="<?php echo htmlspecialchars($conf['chat_title'] ?? '在线客服');?>" placeholder="在线客服">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客服欢迎语</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="chat_welcome" value="<?php echo htmlspecialchars($conf['chat_welcome'] ?? '请问有什么可以帮您？');?>" placeholder="请问有什么可以帮您？">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">轮询时间(秒)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_polling" value="<?php echo intval($conf['chat_polling'] ?? 3);?>" min="1" max="60">
                    <span class="help-block">建议设置3-5秒，太短会增加服务器压力，太长会影响实时性</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">自动关闭时间(分钟)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_autoclose" value="<?php echo intval($conf['chat_autoclose'] ?? 30);?>" min="1" max="1440">
                    <span class="help-block">超过设定时间未收到新消息的会话自动关闭，设为0则不自动关闭</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">自动删除聊天记录</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_auto_delete" default="<?php echo $conf['chat_auto_delete'] ?? 0;?>">
                        <option value="1" <?php if($conf['chat_auto_delete']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_auto_delete']!=1)echo 'selected';?>>关闭</option>
                    </select>
                    <span class="help-block">开启后将自动删除超过设定天数的聊天记录</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">删除天数(天)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_delete_days" value="<?php echo intval($conf['chat_delete_days'] ?? 30);?>" min="1" max="365">
                    <span class="help-block">超过设定天数的聊天记录将被自动删除，建议设置30-90天</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">客服按钮颜色</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control colorpicker" name="chat_btn_color" value="<?php echo htmlspecialchars($conf['chat_btn_color'] ?? '#2196F3');?>" placeholder="#2196F3">
                    <span class="help-block">设置客服悬浮按钮的背景颜色，默认为蓝色(#2196F3)</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">聊天窗口头部颜色</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control colorpicker" name="chat_window_color" value="<?php echo htmlspecialchars($conf['chat_window_color'] ?? '#FFFFFF');?>" placeholder="#FFFFFF">
                    <span class="help-block">设置前台客服聊天窗口顶部区域的背景颜色，默认为白色(#FFFFFF)</span>
                </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">提示音开关</label>
            <div class="col-sm-10">
                <select class="form-control" name="chat_sound" default="<?php echo $conf['chat_sound'] ?? 1;?>">
                    <option value="1" <?php if($conf['chat_sound']==1)echo 'selected';?>>开启</option>
                    <option value="0" <?php if($conf['chat_sound']!=1)echo 'selected';?>>关闭</option>
                </select>
                <span class="help-block">开启后，客服工作台收到新消息时会播放提示音</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">工作台美化样式</label>
            <div class="col-sm-10">
                <select class="form-control" name="chat_style" default="<?php echo $conf['chat_style'] ?? 1;?>">
                    <option value="1" <?php if(($conf['chat_style'] ?? 1)==1)echo 'selected';?>>样式一 · 清新蓝</option>
                    <option value="2" <?php if(($conf['chat_style'] ?? 1)==2)echo 'selected';?>>样式二 · 暗夜黑</option>
                    <option value="3" <?php if(($conf['chat_style'] ?? 1)==3)echo 'selected';?>>样式三 · 渐变科技</option>
                </select>
                <span class="help-block">选择客服工作台（后台）的整体美化样式</span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">图片上传限制开关</label>
            <div class="col-sm-10">
                <select class="form-control" name="chat_image_limit_enable" default="<?php echo $conf['chat_image_limit_enable'] ?? 1;?>">
                    <option value="1" <?php if($conf['chat_image_limit_enable']==1)echo 'selected';?>>开启</option>
                    <option value="0" <?php if($conf['chat_image_limit_enable']!=1)echo 'selected';?>>关闭</option>
                </select>
                <span class="help-block">开启后将限制用户上传的图片大小和格式</span>
            </div>
        </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">最大图片大小(KB)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_image_max_size" value="<?php echo intval($conf['chat_image_max_size'] ?? 2048);?>" min="100" max="10240">
                    <span class="help-block">限制用户上传的图片大小，单位为KB，默认2048KB(2MB)</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">允许的图片格式</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="chat_image_formats" value="<?php echo htmlspecialchars($conf['chat_image_formats'] ?? 'jpg,jpeg,png,gif');?>" placeholder="jpg,jpeg,png,gif">
                    <span class="help-block">允许用户上传的图片格式，多个格式用逗号分隔，如：jpg,jpeg,png,gif</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">视频上传开关</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_video_enable" default="<?php echo $conf['chat_video_enable'] ?? 1;?>">
                        <option value="1" <?php if($conf['chat_video_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_video_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                    <span class="help-block">开启后用户可以在客服聊天中上传MP4视频</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">最大视频大小(MB)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_video_max_size" value="<?php echo intval($conf['chat_video_max_size'] ?? 10);?>" min="1" max="100">
                    <span class="help-block">限制用户上传的视频大小，单位为MB，默认10MB</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">防刷屏功能开关</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_anti_spam_enable" default="<?php echo $conf['chat_anti_spam_enable'] ?? 1;?>">
                        <option value="1" <?php if($conf['chat_anti_spam_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_anti_spam_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                    <span class="help-block">开启后将限制用户在短时间内发送过多消息</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">分钟内最大消息数</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_max_messages" value="<?php echo intval($conf['chat_max_messages'] ?? 10);?>" min="5" max="100">
                    <span class="help-block">用户在一分钟内允许发送的最大消息数，默认10条</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">首次封禁时长(分钟)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_first_ban_time" value="<?php echo intval($conf['chat_first_ban_time'] ?? 3);?>" min="1" max="60">
                    <span class="help-block">首次违规时的封禁时长，默认3分钟</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">二次封禁时长(小时)</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="chat_second_ban_time" value="<?php echo intval($conf['chat_second_ban_time'] ?? 24);?>" min="1" max="168">
                    <span class="help-block">二次违规时的封禁时长，默认24小时(1天)</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>

    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">关键词自动回复设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>关键词自动回复功能说明：</b><br>
                1. 开启后，当用户发送包含设置的关键词的消息时，系统将自动回复对应的内容<br>
                2. 可设置多个关键词-回复对，每行一个<br>
                3. 格式：关键词|回复内容（例如：你好|您好，请问有什么可以帮您？）<br>
                4. 支持模糊匹配，只要用户消息中包含设置的关键词，就会触发自动回复
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form">
            <input type="hidden" name="do" value="save_keyword">
            <div class="form-group">
                <label class="col-sm-2 control-label">关键词自动回复开关</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_keyword_enable" default="<?php echo $conf['chat_keyword_enable'] ?? 0;?>">
                        <option value="1" <?php if($conf['chat_keyword_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_keyword_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">关键词-回复设置</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="chat_keywords" rows="10" placeholder="每行一个关键词-回复对，格式：关键词|回复内容"><?php echo htmlspecialchars($conf['chat_keywords'] ?? '');?></textarea>
                    <span class="help-block">示例：
                    <br>你好|您好，欢迎咨询，请问有什么可以帮您？
                    <br>价格|我们的产品价格合理，请访问商城查看详情
                    <br>帮助|请查看我们的帮助中心：<a href="/user/faq.php" target="_blank">帮助中心</a></span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>

    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">违禁词过滤设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>违禁词过滤功能说明：</b><br>
                1. 开启后，系统将自动检测用户发送的消息中是否包含违禁词<br>
                2. 如果检测到违禁词，系统将拒绝发送该消息并给出提示<br>
                3. 可设置多个违禁词，每行一个<br>
                4. 支持精确匹配，只要用户消息中包含设置的违禁词，就会被拦截
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form">
            <input type="hidden" name="do" value="save_prohibited">
            <div class="form-group">
                <label class="col-sm-2 control-label">违禁词过滤开关</label>
                <div class="col-sm-10">
                    <select class="form-control" name="chat_prohibited_enable" default="<?php echo $conf['chat_prohibited_enable'] ?? 0;?>">
                        <option value="1" <?php if($conf['chat_prohibited_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['chat_prohibited_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">违禁词列表</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="chat_prohibited_words" rows="10" placeholder="每行一个违禁词"><?php echo htmlspecialchars($conf['chat_prohibited_words'] ?? '');?></textarea>
                    <span class="help-block">示例：
                    <br>违禁词1
                    <br>违禁词2
                    <br>违禁词3</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">违禁提示语</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="chat_prohibited_msg" value="<?php echo htmlspecialchars($conf['chat_prohibited_msg'] ?? '您的消息包含违禁内容，无法发送！');?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>
<?php
} elseif ($mod == "beautify_sidebar") {
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">后台侧边栏框架美化</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>后台侧边栏框架美化功能说明：</b><br>
                1. 框架变色：可以选择不同的后台框架主题颜色<br>
                2. 点击对应的主题即可立即切换<br>
                3. 切换后会自动保存到浏览器缓存中
            </div>
        </div>
        <div class="block-content">
            <div class="sidebar-section">
            <style>
            h4{font-family:"微软雅黑",Georgia,Serif;}
            /* 新拟态主题样式类 */
            .themed-background-neumorphic { background-color: #e0e5ec !important; }
            .themed-background-dark-neumorphic { background-color: #374249 !important; }
            /* 仅影响当前页面的主题列表，不影响其他页面 */
            .block-content .sidebar-section .sidebar-themes {
                opacity: 1 !important;
            }
            /* 仅影响当前页面的主题项悬停效果 */
            .block-content .sidebar-section .sidebar-themes li:hover,
            .block-content .sidebar-section .sidebar-themes li.active {
                opacity: 1 !important;
                transform: scale(1.05) !important;
                transition: all 0.3s ease !important;
            }
            /* 仅影响当前页面的主题项默认样式 */
            .block-content .sidebar-section .sidebar-themes li {
                opacity: 1;
                transition: all 0.3s ease;
            }
            </style>

            <ul class="sidebar-themes clearfix">
            <li class="">
            <a href="javascript:void(0)" class="themed-background-default" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/themes-2.2.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-default"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-classy"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-social"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-flat"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-amethyst"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-creme"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-passion"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="" data-original-title="">
            <span class="section-side themed-background-dark-neumorphic"></span>
            <span class="section-content"></span>
            </a>
            <br>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-inverse" data-theme-sidebar="sidebar-light" data-original-title="">
            <span class="section-side"></span>
            <span class="section-content"></span>
            </a>
            </li>

            <li class="">
            <a href="javascript:void(0)" class="themed-background-classy" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/classy-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-classy"></span>
            <span class="section-content"></span>
            </a>
            <br>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-social" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/social-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-social"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li>
            <a href="javascript:void(0)" class="themed-background-flat" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/flat-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-flat"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-amethyst" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/amethyst-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-amethyst"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-creme" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/creme-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-creme"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-passion" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/passion-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-passion"></span>
            <span class="section-content"></span>
            </a>
            </li>
            <li class="">
            <a href="javascript:void(0)" class="themed-background-neumorphic" data-toggle="tooltip" title="" data-theme="../assets/appui/css/themes/neumorphic-2.4.css" data-theme-navbar="navbar-default" data-theme-sidebar="" data-original-title="">
            <span class="section-header"></span>
            <span class="section-side themed-background-dark-neumorphic"></span>
            <span class="section-content"></span>
            </a>
            </li>
            </ul>
            </div>
        </div>
    </div>
<?php
} elseif ($mod == "beautify_admin") {
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">鼠标美化设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>鼠标美化功能说明：</b><br>
                1. 开启鼠标美化：可以启用或关闭鼠标美化功能<br>
                2. 选择鼠标样式：可以选择不同的鼠标指针样式<br>
                3. 保存设置后将立即生效到所有页面
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form" enctype="multipart/form-data" onsubmit="return saveSetting(this)">
            <input type="hidden" name="do" value="submit">
            <input type="hidden" name="mod" value="beautify_admin_n">
            <div class="form-group">
                <label class="col-sm-2 control-label">开启鼠标美化</label>
                <div class="col-sm-10">
                    <select class="form-control" name="mouse_beautify" default="<?php echo $conf['mouse_beautify'] ?? 0;?>">
                        <option value="0" <?php if(($conf['mouse_beautify']??0)==0)echo 'selected';?>>关闭</option>
                        <option value="1" <?php if(($conf['mouse_beautify']??0)==1)echo 'selected';?>>开启</option>
                    </select>
                    <span class="help-block">开启后将应用自定义鼠标样式</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">鼠标样式</label>
                <div class="col-sm-10">
                    <select class="form-control" name="mouse_style" default="<?php echo $conf['mouse_style'] ?? 'default';?>">
                        <option value="default" <?php if(($conf['mouse_style']??'default')=='default')echo 'selected';?>>默认样式</option>
                        <option value="cute" <?php if(($conf['mouse_style']??'default')=='cute')echo 'selected';?>>可爱风格</option>
                        <option value="modern" <?php if(($conf['mouse_style']??'default')=='modern')echo 'selected';?>>现代风格</option>
                        <option value="game" <?php if(($conf['mouse_style']??'default')=='game')echo 'selected';?>>游戏风格</option>
                        <option value="star" <?php if(($conf['mouse_style']??'default')=='star')echo 'selected';?>>星形</option>
                        <option value="heart" <?php if(($conf['mouse_style']??'default')=='heart')echo 'selected';?>>心形</option>
                        <option value="arrow" <?php if(($conf['mouse_style']??'default')=='arrow')echo 'selected';?>>箭头形</option>
                        <option value="cross" <?php if(($conf['mouse_style']??'default')=='cross')echo 'selected';?>>十字形</option>
                        <option value="drop" <?php if(($conf['mouse_style']??'default')=='drop')echo 'selected';?>>水滴形</option>
                        <option value="triangle" <?php if(($conf['mouse_style']??'default')=='triangle')echo 'selected';?>>三角形</option>
                    </select>
                    <span class="help-block">选择您喜欢的鼠标指针样式</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>
<?php
} elseif ($mod == "admin_style") {
$admin_ui_style = $conf['admin_ui_style'] ?? 'none';
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">后台整站UI风格</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#eef4fb;">
                <b>后台UI风格功能说明：</b><br>
                1. 选择整体界面风格后，管理后台所有页面（含侧边栏、顶栏、表格、按钮等）将统一应用该风格<br>
                2. "默认"表示使用系统自带的主题样式<br>
                3. 保存后立即生效
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form" onsubmit="return saveSetting(this)">
            <input type="hidden" name="do" value="submit">
            <input type="hidden" name="mod" value="admin_style_n">
            <div class="form-group">
                <label class="col-sm-2 control-label">选择UI风格</label>
                <div class="col-sm-10">
                    <select class="form-control" name="admin_ui_style" default="<?php echo htmlspecialchars($admin_ui_style);?>" style="max-width:420px;">
                        <option value="none" <?php if($admin_ui_style=='none')echo 'selected';?>>默认（系统原版）</option>
                        <option value="simple" <?php if($admin_ui_style=='simple')echo 'selected';?>>简易风</option>
                        <option value="tech" <?php if($admin_ui_style=='tech')echo 'selected';?>>科技风</option>
                        <option value="enterprise" <?php if($admin_ui_style=='enterprise')echo 'selected';?>>企业风</option>
                        <option value="dark" <?php if($admin_ui_style=='dark')echo 'selected';?>>深色风</option>
                        <option value="green" <?php if($admin_ui_style=='green')echo 'selected';?>>清新绿</option>
                        <option value="orange" <?php if($admin_ui_style=='orange')echo 'selected';?>>活力橙</option>
                        <option value="purple" <?php if($admin_ui_style=='purple')echo 'selected';?>>高级紫</option>
                        <option value="red" <?php if($admin_ui_style=='red')echo 'selected';?>>中国红</option>
                        <option value="blue" <?php if($admin_ui_style=='blue')echo 'selected';?>>经典蓝</option>
                        <option value="cyan" <?php if($admin_ui_style=='cyan')echo 'selected';?>>青蓝</option>
                        <option value="pink" <?php if($admin_ui_style=='pink')echo 'selected';?>>少女粉</option>
                        <option value="gold" <?php if($admin_ui_style=='gold')echo 'selected';?>>土豪金</option>
                        <option value="coffee" <?php if($admin_ui_style=='coffee')echo 'selected';?>>咖啡棕</option>
                        <option value="teal" <?php if($admin_ui_style=='teal')echo 'selected';?>>青碧</option>
                        <option value="navy" <?php if($admin_ui_style=='navy')echo 'selected';?>>藏蓝</option>
                        <option value="gray" <?php if($admin_ui_style=='gray')echo 'selected';?>>商务灰</option>
                        <option value="mint" <?php if($admin_ui_style=='mint')echo 'selected';?>>薄荷</option>
                        <option value="lavender" <?php if($admin_ui_style=='lavender')echo 'selected';?>>薰衣草</option>
                        <option value="rose" <?php if($admin_ui_style=='rose')echo 'selected';?>>玫红</option>
                        <option value="sky" <?php if($admin_ui_style=='sky')echo 'selected';?>>天蓝</option>
                        <option value="ocean" <?php if($admin_ui_style=='ocean')echo 'selected';?>>海洋</option>
                        <option value="lime" <?php if($admin_ui_style=='lime')echo 'selected';?>>青柠</option>
                        <option value="glass" <?php if($admin_ui_style=='glass')echo 'selected';?>>玻璃拟态</option>
                        <option value="neon" <?php if($admin_ui_style=='neon')echo 'selected';?>>霓虹流光</option>
                        <option value="gradient" <?php if($admin_ui_style=='gradient')echo 'selected';?>>渐变炫彩</option>
                        <option value="cyber" <?php if($admin_ui_style=='cyber')echo 'selected';?>>赛博朋克</option>
                        <option value="retro" <?php if($admin_ui_style=='retro')echo 'selected';?>>复古怀旧</option>
                        <option value="material" <?php if($admin_ui_style=='material')echo 'selected';?>>材料设计</option>
                        <option value="flat" <?php if($admin_ui_style=='flat')echo 'selected';?>>扁平色块</option>
                        <option value="minimal" <?php if($admin_ui_style=='minimal')echo 'selected';?>>极简留白</option>
                        <option value="luxury" <?php if($admin_ui_style=='luxury')echo 'selected';?>>黑金奢华</option>
                        <option value="forest" <?php if($admin_ui_style=='forest')echo 'selected';?>>森林墨绿</option>
                        <option value="sunset" <?php if($admin_ui_style=='sunset')echo 'selected';?>>日落暖橙</option>
                        <option value="starry" <?php if($admin_ui_style=='starry')echo 'selected';?>>星空梦幻</option>
                        <option value="slate" <?php if($admin_ui_style=='slate')echo 'selected';?>>石板灰蓝</option>
                        <option value="indigo" <?php if($admin_ui_style=='indigo')echo 'selected';?>>靛蓝紫</option>
                        <option value="violet" <?php if($admin_ui_style=='violet')echo 'selected';?>>紫罗兰</option>
                        <option value="coral" <?php if($admin_ui_style=='coral')echo 'selected';?>>珊瑚橙</option>
                        <option value="peach" <?php if($admin_ui_style=='peach')echo 'selected';?>>蜜桃粉橙</option>
                        <option value="cream" <?php if($admin_ui_style=='cream')echo 'selected';?>>奶油米白</option>
                        <option value="honey" <?php if($admin_ui_style=='honey')echo 'selected';?>>蜂蜜暖黄</option>
                        <option value="charcoal" <?php if($admin_ui_style=='charcoal')echo 'selected';?>>炭黑酷感</option>
                        <option value="wood" <?php if($admin_ui_style=='wood')echo 'selected';?>>原木自然</option>
                        <option value="bamboo" <?php if($admin_ui_style=='bamboo')echo 'selected';?>>翠竹青绿</option>
                        <option value="sand" <?php if($admin_ui_style=='sand')echo 'selected';?>>沙漠沙金</option>
                        <option value="candy" <?php if($admin_ui_style=='candy')echo 'selected';?>>糖果甜心</option>
                        <option value="pastel" <?php if($admin_ui_style=='pastel')echo 'selected';?>>马卡龙</option>
                        <option value="spring" <?php if($admin_ui_style=='spring')echo 'selected';?>>春芽新绿</option>
                        <option value="summer" <?php if($admin_ui_style=='summer')echo 'selected';?>>夏日湛蓝</option>
                        <option value="autumn" <?php if($admin_ui_style=='autumn')echo 'selected';?>>秋枫绯红</option>
                        <option value="winter" <?php if($admin_ui_style=='winter')echo 'selected';?>>冰雪银蓝</option>
                        <option value="aurora" <?php if($admin_ui_style=='aurora')echo 'selected';?>>极光幻彩</option>
                        <option value="steampunk" <?php if($admin_ui_style=='steampunk')echo 'selected';?>>蒸汽朋克</option>
                        <option value="ink" <?php if($admin_ui_style=='ink')echo 'selected';?>>水墨国风</option>
                        <option value="berry" <?php if($admin_ui_style=='berry')echo 'selected';?>>莓果紫红</option>
                        <option value="olive" <?php if($admin_ui_style=='olive')echo 'selected';?>>橄榄军绿</option>
                        <option value="pumpkin" <?php if($admin_ui_style=='pumpkin')echo 'selected';?>>南瓜暖橙</option>
                        <option value="grape" <?php if($admin_ui_style=='grape')echo 'selected';?>>葡萄深紫</option>
                    </select>
                    <span class="help-block">选择后管理后台整体界面将切换为对应风格</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">风格预览</label>
                <div class="col-sm-10">
                    <table class="table table-bordered" style="max-width:420px;margin-bottom:0;">
                        <tr><th style="width:120px;">风格</th><th>配色示意</th></tr>
                        <tr>
                            <td>简易风</td>
                            <td><span class="label" style="background:#337ab7;color:#fff;">按钮蓝</span> <span class="label" style="background:#5cb85c;color:#fff;">成功绿</span> <span class="label" style="background:#fafafa;color:#333;border:1px solid #ddd;">表头灰</span> 白底/直角/无阴影</td>
                        </tr>
                        <tr>
                            <td>科技风</td>
                            <td><span class="label" style="background:#00b4d8;color:#fff;">科技青</span> <span class="label" style="background:#0d1b2a;color:#00d4ff;">深蓝侧栏</span> <span class="label" style="background:#0077b6;color:#fff;">按钮蓝</span> 深色侧栏/发光高亮</td>
                        </tr>
                        <tr>
                            <td>企业风</td>
                            <td><span class="label" style="background:#1f3864;color:#fff;">商务藏青</span> <span class="label" style="background:#c9a063;color:#fff;">金色点缀</span> <span class="label" style="background:#2c4a7c;color:#fff;">按钮蓝</span> 稳重典雅/商务感</td>
                        </tr>
                        <tr>
                            <td>深色风</td>
                            <td><span class="label" style="background:#23272e;color:#00c8ff;border:1px solid #3a404b;">暗色面板</span> <span class="label" style="background:#00c8ff;color:#111318;">霓虹青</span> <span class="label" style="background:#2b7de9;color:#fff;">按钮蓝</span> 全深色/护眼</td>
                        </tr>
                        <tr>
                            <td>清新绿</td>
                            <td><span class="label" style="background:#2e9b63;color:#fff;">薄荷绿</span> <span class="label" style="background:#e8f5ee;color:#1e7a4d;border:1px solid #cde8d6;">浅绿表头</span> <span class="label" style="background:#34a853;color:#fff;">按钮绿</span> 自然清新</td>
                        </tr>
                        <tr>
                            <td>活力橙</td>
                            <td><span class="label" style="background:#f97316;color:#fff;">活力橙</span> <span class="label" style="background:#ffefe2;color:#c2410c;border:1px solid #f7d9c2;">浅橙表头</span> <span class="label" style="background:#ea580c;color:#fff;">按钮橙</span> 热情醒目</td>
                        </tr>
                        <tr>
                            <td>高级紫</td>
                            <td><span class="label" style="background:#6d28d9;color:#fff;">典雅紫</span> <span class="label" style="background:#f1e9fe;color:#5b21b6;border:1px solid #ddd0f5;">浅紫表头</span> <span class="label" style="background:#8b5cf6;color:#fff;">按钮紫</span> 轻奢高级感</td>
                        </tr>
                        <tr>
                            <td>中国红</td>
                            <td><span class="label" style="background:#b71c1c;color:#fff;">中国红</span> <span class="label" style="background:#ffe9e9;color:#a01717;border:1px solid #f5cccc;">浅红表头</span> <span class="label" style="background:#e53935;color:#fff;">按钮红</span> 喜庆醒目</td>
                        </tr>
                        <tr>
                            <td>经典蓝</td>
                            <td><span class="label" style="background:#3b82f6;color:#fff;">经典蓝</span> <span class="label" style="background:#e0ecfb;color:#1e40af;border:1px solid #c3d9f5;">浅蓝表头</span> <span class="label" style="background:#2563eb;color:#fff;">按钮蓝</span> 清爽可靠</td>
                        </tr>
                        <tr>
                            <td>青蓝</td>
                            <td><span class="label" style="background:#06b6d4;color:#fff;">青蓝</span> <span class="label" style="background:#d9f4fa;color:#0e7490;border:1px solid #b0e6f2;">浅青表头</span> <span class="label" style="background:#22d3ee;color:#fff;">按钮青</span> 活泼清爽</td>
                        </tr>
                        <tr>
                            <td>少女粉</td>
                            <td><span class="label" style="background:#ec4899;color:#fff;">少女粉</span> <span class="label" style="background:#fde3f0;color:#be185d;border:1px solid #f5c4dd;">浅粉表头</span> <span class="label" style="background:#f472b6;color:#fff;">按钮粉</span> 甜美可爱</td>
                        </tr>
                        <tr>
                            <td>土豪金</td>
                            <td><span class="label" style="background:#c9a23f;color:#fff;">土豪金</span> <span class="label" style="background:#f6edd7;color:#9a7a2c;border:1px solid #ead8a9;">浅金表头</span> <span class="label" style="background:#d4a94e;color:#fff;">按钮金</span> 大气奢华</td>
                        </tr>
                        <tr>
                            <td>咖啡棕</td>
                            <td><span class="label" style="background:#8c5a39;color:#fff;">咖啡棕</span> <span class="label" style="background:#f0e4d8;color:#6b4327;border:1px solid #dfc9b0;">浅棕表头</span> <span class="label" style="background:#a16a44;color:#fff;">按钮棕</span> 沉稳文艺</td>
                        </tr>
                        <tr>
                            <td>青碧</td>
                            <td><span class="label" style="background:#0d9488;color:#fff;">青碧</span> <span class="label" style="background:#d3f2ec;color:#0f766e;border:1px solid #a9e3d8;">浅碧表头</span> <span class="label" style="background:#14b8a6;color:#fff;">按钮碧</span> 宁静雅致</td>
                        </tr>
                        <tr>
                            <td>藏蓝</td>
                            <td><span class="label" style="background:#26385e;color:#fff;">藏蓝侧栏</span> <span class="label" style="background:#dde4f0;color:#1c2b4a;border:1px solid #c3cde0;">浅蓝表头</span> <span class="label" style="background:#334a78;color:#fff;">按钮蓝</span> 稳重商务</td>
                        </tr>
                        <tr>
                            <td>商务灰</td>
                            <td><span class="label" style="background:#4a5a6b;color:#fff;">商务灰</span> <span class="label" style="background:#e5e9ed;color:#38454f;border:1px solid #ccd3da;">浅灰表头</span> <span class="label" style="background:#5a6b7d;color:#fff;">按钮灰</span> 简约商务</td>
                        </tr>
                        <tr>
                            <td>薄荷</td>
                            <td><span class="label" style="background:#1fae8d;color:#fff;">薄荷绿</span> <span class="label" style="background:#d8f3e8;color:#17806a;border:1px solid #b3e6d3;">浅绿表头</span> <span class="label" style="background:#2ec4a1;color:#fff;">按钮绿</span> 清凉自然</td>
                        </tr>
                        <tr>
                            <td>薰衣草</td>
                            <td><span class="label" style="background:#8b5cf6;color:#fff;">薰衣草紫</span> <span class="label" style="background:#ece4fb;color:#6d28d9;border:1px solid #d8c9f5;">浅紫表头</span> <span class="label" style="background:#9d7be8;color:#fff;">按钮紫</span> 温柔浪漫</td>
                        </tr>
                        <tr>
                            <td>玫红</td>
                            <td><span class="label" style="background:#d63a54;color:#fff;">玫红</span> <span class="label" style="background:#fce0e5;color:#a11f3a;border:1px solid #f5c2cc;">浅玫表头</span> <span class="label" style="background:#e7556b;color:#fff;">按钮玫红</span> 热情雅致</td>
                        </tr>
                        <tr>
                            <td>天蓝</td>
                            <td><span class="label" style="background:#2f9fe0;color:#fff;">天蓝</span> <span class="label" style="background:#dceefb;color:#1f74ad;border:1px solid #bcdcf2;">浅蓝表头</span> <span class="label" style="background:#4db8f0;color:#fff;">按钮蓝</span> 清澈通透</td>
                        </tr>
                        <tr>
                            <td>海洋</td>
                            <td><span class="label" style="background:#1565c0;color:#fff;">海洋蓝侧栏</span> <span class="label" style="background:#dce8fb;color:#0d47a1;border:1px solid #bcd4f2;">浅蓝表头</span> <span class="label" style="background:#1e88e5;color:#fff;">按钮蓝</span> 深邃大气</td>
                        </tr>
                        <tr>
                            <td>青柠</td>
                            <td><span class="label" style="background:#65a30d;color:#fff;">青柠绿</span> <span class="label" style="background:#e6f2cf;color:#4d7c0f;border:1px solid #cfe4a5;">浅绿表头</span> <span class="label" style="background:#84cc16;color:#fff;">按钮绿</span> 清新活力</td>
                        </tr>
                        <tr>
                            <td>玻璃拟态</td>
                            <td><span class="label" style="background:rgba(99,102,241,0.85);color:#fff;">蓝紫主色</span> <span class="label" style="background:rgba(255,255,255,0.7);color:#334155;border:1px solid rgba(148,163,184,0.4);">半透明卡片</span> <span class="label" style="background:#6366f1;color:#fff;">按钮</span> 毛玻璃/大圆角/柔和阴影</td>
                        </tr>
                        <tr>
                            <td>霓虹流光</td>
                            <td><span class="label" style="background:#0a0a12;color:#00ffe7;border:1px solid #00ffe7;">深黑+霓虹青</span> <span class="label" style="background:#00e5ff;color:#0a0a12;">霓虹青</span> <span class="label" style="background:#ff3df2;color:#0a0a12;">霓虹品红</span> 发光文字/霓虹渐变</td>
                        </tr>
                        <tr>
                            <td>渐变炫彩</td>
                            <td><span class="label" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;">紫蓝渐变</span> <span class="label" style="background:#f6f4fe;color:#4c1d95;border:1px solid #ddd6fe;">浅紫表头</span> <span class="label" style="background:#764ba2;color:#fff;">按钮</span> 渐变背景/炫彩</td>
                        </tr>
                        <tr>
                            <td>赛博朋克</td>
                            <td><span class="label" style="background:#0f0b1e;color:#00ffc4;border:1px solid #00ffc4;">深紫+霓虹绿</span> <span class="label" style="background:#00ffc4;color:#0f0b1e;">赛博青</span> <span class="label" style="background:#ff0a54;color:#fff;">霓虹红</span> 霓虹边框/发光</td>
                        </tr>
                        <tr>
                            <td>复古怀旧</td>
                            <td><span class="label" style="background:#8a5a2b;color:#fff;">怀旧棕</span> <span class="label" style="background:#fbf3e3;color:#6f4518;border:1px solid #d3b98a;">米黄面板</span> <span class="label" style="background:#5b8a2b;color:#fff;">复古绿</span> 纸感/衬线标题</td>
                        </tr>
                        <tr>
                            <td>材料设计</td>
                            <td><span class="label" style="background:#3949ab;color:#fff;">靛蓝</span> <span class="label" style="background:#f5f5f5;color:#3949ab;border:1px solid #e0e0e0;">浅灰表头</span> <span class="label" style="background:#43a047;color:#fff;">材料绿</span> 层次阴影/大圆角</td>
                        </tr>
                        <tr>
                            <td>扁平色块</td>
                            <td><span class="label" style="background:#2563eb;color:#fff;">扁平蓝</span> <span class="label" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">浅灰表头</span> <span class="label" style="background:#16a34a;color:#fff;">扁平绿</span> 无阴影/高饱和</td>
                        </tr>
                        <tr>
                            <td>极简留白</td>
                            <td><span class="label" style="background:#555;color:#fff;">低调灰</span> <span class="label" style="background:#fafafa;color:#999;border:1px solid #eee;">极简表头</span> <span class="label" style="background:#f0f0f0;color:#444;border:1px solid #ddd;">细线按钮</span> 大量留白/无装饰</td>
                        </tr>
                        <tr>
                            <td>黑金奢华</td>
                            <td><span class="label" style="background:#1f1f1f;color:#e6c46a;border:1px solid #c9a23f;">暗色面板+金</span> <span class="label" style="background:#c9a23f;color:#fff;">金色</span> <span class="label" style="background:#2b2b2b;color:#d4d4d4;border:1px solid #4a4a4a;">深色按钮</span> 黑金/金属质感</td>
                        </tr>
                        <tr>
                            <td>森林墨绿</td>
                            <td><span class="label" style="background:#1b4332;color:#d8f3dc;">墨绿侧栏</span> <span class="label" style="background:#e7f2e4;color:#24543f;border:1px solid #cfe4c8;">浅绿表头</span> <span class="label" style="background:#2d6a4f;color:#fff;">按钮绿</span> 自然生态</td>
                        </tr>
                        <tr>
                            <td>日落暖橙</td>
                            <td><span class="label" style="background:linear-gradient(90deg,#f97316,#ec4899);color:#fff;">橙粉渐变</span> <span class="label" style="background:#fdeedf;color:#c2410c;border:1px solid #f8d8c0;">暖橙表头</span> <span class="label" style="background:#ea580c;color:#fff;">按钮橙</span> 落日余晖</td>
                        </tr>
                        <tr>
                            <td>星空梦幻</td>
                            <td><span class="label" style="background:#0f172a;color:#a5b4fc;border:1px solid #818cf8;">深蓝星空</span> <span class="label" style="background:#2a3a58;color:#a5b4fc;">星空表头</span> <span class="label" style="background:#818cf8;color:#fff;">按钮紫</span> 星光点缀/梦幻</td>
                        </tr>
                        <tr>
                            <td>石板灰蓝</td>
                            <td><span class="label" style="background:#64748b;color:#fff;">石板灰蓝</span> <span class="label" style="background:#e2e8f0;color:#334155;border:1px solid #cbd5e1;">浅灰表头</span> <span class="label" style="background:#64748b;color:#fff;">按钮灰蓝</span> 沉稳理性</td>
                        </tr>
                        <tr>
                            <td>靛蓝紫</td>
                            <td><span class="label" style="background:#6366f1;color:#fff;">靛蓝</span> <span class="label" style="background:#e0e7ff;color:#4338ca;border:1px solid #c7d2fe;">浅靛表头</span> <span class="label" style="background:#6366f1;color:#fff;">按钮靛蓝</span> 知性大气</td>
                        </tr>
                        <tr>
                            <td>紫罗兰</td>
                            <td><span class="label" style="background:#8b5cf6;color:#fff;">紫罗兰</span> <span class="label" style="background:#ede9fe;color:#6d28d9;border:1px solid #ddd6fe;">浅紫表头</span> <span class="label" style="background:#8b5cf6;color:#fff;">按钮紫</span> 浪漫优雅</td>
                        </tr>
                        <tr>
                            <td>珊瑚橙</td>
                            <td><span class="label" style="background:#ff6f61;color:#fff;">珊瑚橙</span> <span class="label" style="background:#ffe3da;color:#9c3b2e;border:1px solid #ffc9bd;">浅橙表头</span> <span class="label" style="background:#ff6f61;color:#fff;">按钮橙</span> 活力暖意</td>
                        </tr>
                        <tr>
                            <td>蜜桃粉橙</td>
                            <td><span class="label" style="background:#ff8a65;color:#fff;">蜜桃橙</span> <span class="label" style="background:#ffe8dd;color:#a04a24;border:1px solid #ffd4c2;">浅桃表头</span> <span class="label" style="background:#ff8a65;color:#fff;">按钮蜜桃</span> 温柔甜美</td>
                        </tr>
                        <tr>
                            <td>奶油米白</td>
                            <td><span class="label" style="background:#b08968;color:#fff;">奶油棕</span> <span class="label" style="background:#f0e6da;color:#6b5138;border:1px solid #e5d8c6;">米白面板</span> <span class="label" style="background:#b08968;color:#fff;">按钮奶油</span> 柔和素雅</td>
                        </tr>
                        <tr>
                            <td>蜂蜜暖黄</td>
                            <td><span class="label" style="background:#f59e0b;color:#fff;">蜂蜜黄</span> <span class="label" style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;">浅黄表头</span> <span class="label" style="background:#f59e0b;color:#fff;">按钮黄</span> 温暖明亮</td>
                        </tr>
                        <tr>
                            <td>炭黑酷感</td>
                            <td><span class="label" style="background:#374151;color:#fff;">炭黑</span> <span class="label" style="background:#e5e7eb;color:#1f2937;border:1px solid #d1d5db;">浅灰表头</span> <span class="label" style="background:#374151;color:#fff;">按钮炭黑</span> 冷酷极简</td>
                        </tr>
                        <tr>
                            <td>原木自然</td>
                            <td><span class="label" style="background:#8d6e63;color:#fff;">原木棕</span> <span class="label" style="background:#eadfd5;color:#4e342e;border:1px solid #ddd0c3;">浅木表头</span> <span class="label" style="background:#8d6e63;color:#fff;">按钮木色</span> 自然质朴</td>
                        </tr>
                        <tr>
                            <td>翠竹青绿</td>
                            <td><span class="label" style="background:#43a047;color:#fff;">翠竹绿</span> <span class="label" style="background:#e8f5e9;color:#1b5e20;border:1px solid #c8e6c9;">浅绿表头</span> <span class="label" style="background:#43a047;color:#fff;">按钮绿</span> 清雅盎然</td>
                        </tr>
                        <tr>
                            <td>沙漠沙金</td>
                            <td><span class="label" style="background:#c9a227;color:#fff;">沙金黄</span> <span class="label" style="background:#f2e8cd;color:#7a5f12;border:1px solid #e8d9ac;">浅沙表头</span> <span class="label" style="background:#c9a227;color:#fff;">按钮金</span> 西域风情</td>
                        </tr>
                        <tr>
                            <td>糖果甜心</td>
                            <td><span class="label" style="background:#ec4899;color:#fff;">糖果粉</span> <span class="label" style="background:#fce7f3;color:#9d174d;border:1px solid #fbcfe8;">浅粉表头</span> <span class="label" style="background:#ec4899;color:#fff;">按钮粉</span> 甜蜜俏皮</td>
                        </tr>
                        <tr>
                            <td>马卡龙</td>
                            <td><span class="label" style="background:#a78bfa;color:#fff;">马卡龙紫</span> <span class="label" style="background:#efeafb;color:#5b3f9e;border:1px solid #e4ddf7;">浅紫表头</span> <span class="label" style="background:#a78bfa;color:#fff;">按钮紫</span> 柔和清新</td>
                        </tr>
                        <tr>
                            <td>春芽新绿</td>
                            <td><span class="label" style="background:#22c55e;color:#fff;">春芽绿</span> <span class="label" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">浅绿表头</span> <span class="label" style="background:#22c55e;color:#fff;">按钮绿</span> 生机勃勃</td>
                        </tr>
                        <tr>
                            <td>夏日湛蓝</td>
                            <td><span class="label" style="background:#0ea5e9;color:#fff;">夏日蓝</span> <span class="label" style="background:#e0f2fe;color:#075985;border:1px solid #bae6fd;">浅蓝表头</span> <span class="label" style="background:#0ea5e9;color:#fff;">按钮蓝</span> 清爽通透</td>
                        </tr>
                        <tr>
                            <td>秋枫绯红</td>
                            <td><span class="label" style="background:#e05238;color:#fff;">秋枫红</span> <span class="label" style="background:#fde3d8;color:#7c2d12;border:1px solid #f6c7b8;">浅红表头</span> <span class="label" style="background:#e05238;color:#fff;">按钮红</span> 秋意浓郁</td>
                        </tr>
                        <tr>
                            <td>冰雪银蓝</td>
                            <td><span class="label" style="background:#5da9d8;color:#fff;">冰雪蓝</span> <span class="label" style="background:#e7f0f8;color:#1f5e86;border:1px solid #cfe3f2;">浅银表头</span> <span class="label" style="background:#5da9d8;color:#fff;">按钮冰蓝</span> 清冷纯净</td>
                        </tr>
                        <tr>
                            <td>极光幻彩</td>
                            <td><span class="label" style="background:#14b8a6;color:#fff;">极光青</span> <span class="label" style="background:#d9f2ec;color:#115e59;border:1px solid #b7e4dd;">浅青表头</span> <span class="label" style="background:#14b8a6;color:#fff;">按钮青</span> 灵动梦幻</td>
                        </tr>
                        <tr>
                            <td>蒸汽朋克</td>
                            <td><span class="label" style="background:#9c6b30;color:#fff;">黄铜棕</span> <span class="label" style="background:#e8dcc6;color:#4f3a1e;border:1px solid #ddd0b8;">复古米表头</span> <span class="label" style="background:#9c6b30;color:#fff;">按钮黄铜</span> 复古机械</td>
                        </tr>
                        <tr>
                            <td>水墨国风</td>
                            <td><span class="label" style="background:#37474f;color:#fff;">墨色</span> <span class="label" style="background:#e6e2d8;color:#263238;border:1px solid #d5d2c8;">宣纸表头</span> <span class="label" style="background:#37474f;color:#fff;">按钮墨</span> 东方雅韵</td>
                        </tr>
                        <tr>
                            <td>莓果紫红</td>
                            <td><span class="label" style="background:#c2185b;color:#fff;">莓果红</span> <span class="label" style="background:#f9e0eb;color:#7c0f42;border:1px solid #f3c9da;">浅莓表头</span> <span class="label" style="background:#c2185b;color:#fff;">按钮莓红</span> 浓郁甘甜</td>
                        </tr>
                        <tr>
                            <td>橄榄军绿</td>
                            <td><span class="label" style="background:#7c8c2e;color:#fff;">橄榄绿</span> <span class="label" style="background:#eaedcf;color:#414d1e;border:1px solid #d8ddb4;">浅绿表头</span> <span class="label" style="background:#7c8c2e;color:#fff;">按钮橄榄</span> 硬朗利落</td>
                        </tr>
                        <tr>
                            <td>南瓜暖橙</td>
                            <td><span class="label" style="background:#f97316;color:#fff;">南瓜橙</span> <span class="label" style="background:#ffe5cc;color:#9a3412;border:1px solid #ffd0a1;">浅橙表头</span> <span class="label" style="background:#f97316;color:#fff;">按钮橙</span> 丰收暖阳</td>
                        </tr>
                        <tr>
                            <td>葡萄深紫</td>
                            <td><span class="label" style="background:#7b3fa0;color:#fff;">葡萄紫</span> <span class="label" style="background:#eadcf4;color:#4a1d63;border:1px solid #ddc8ea;">浅紫表头</span> <span class="label" style="background:#7b3fa0;color:#fff;">按钮紫</span> 醇厚高贵</td>
                        </tr>
                    </table>
                    <span class="help-block">保存后刷新页面即可看到效果</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block" style="max-width:420px;">
                </div>
            </div>
        </form>
    </div>
<?php
} elseif ($mod == "beautify_font") {
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">字体美化设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>字体美化功能说明：</b><br>
                1. 开启字体美化：可以启用或关闭字体美化功能<br>
                2. 选择字体家族：可以选择不同的字体样式<br>
                3. 调整字体大小：可以调整全局字体大小<br>
                4. 保存设置后将立即生效到所有页面
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form" enctype="multipart/form-data" onsubmit="return saveSetting(this)">
            <input type="hidden" name="do" value="submit">
            <input type="hidden" name="mod" value="beautify_font_n">
            <div class="form-group">
                <label class="col-sm-2 control-label">开启字体美化</label>
                <div class="col-sm-10">
                    <select class="form-control" name="font_beautify" default="<?php echo $conf['font_beautify'] ?? 0;?>">
                        <option value="0" <?php if(($conf['font_beautify']??0)==0)echo 'selected';?>>关闭</option>
                        <option value="1" <?php if(($conf['font_beautify']??0)==1)echo 'selected';?>>开启</option>
                    </select>
                    <span class="help-block">开启后将应用自定义字体样式</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">字体家族</label>
                <div class="col-sm-10">
                    <select class="form-control" name="font_family" default="<?php echo $conf['font_family'] ?? 'default';?>">
                        <option value="default" <?php if(($conf['font_family']??'default')=='default')echo 'selected';?>>默认字体</option>
                        <!-- 常规字体 -->
                        <option value="sans-serif" <?php if(($conf['font_family']??'default')=='sans-serif')echo 'selected';?>>无衬线字体</option>
                        <option value="serif" <?php if(($conf['font_family']??'default')=='serif')echo 'selected';?>>衬线字体</option>
                        <option value="monospace" <?php if(($conf['font_family']??'default')=='monospace')echo 'selected';?>>等宽字体</option>
                        <option value="cursive" <?php if(($conf['font_family']??'default')=='cursive')echo 'selected';?>>手写体</option>
                        <option value="fantasy" <?php if(($conf['font_family']??'default')=='fantasy')echo 'selected';?>>艺术字体</option>
                        <!-- 特色中文字体 -->
                        <option value="lisu" <?php if(($conf['font_family']??'default')=='lisu')echo 'selected';?>>隶书</option>
                        <option value="kaiti" <?php if(($conf['font_family']??'default')=='kaiti')echo 'selected';?>>楷体</option>
                        <option value="fangsong" <?php if(($conf['font_family']??'default')=='fangsong')echo 'selected';?>>仿宋</option>
                        <!-- 特色英文字体 -->
                        <option value="playfair" <?php if(($conf['font_family']??'default')=='playfair')echo 'selected';?>>Playfair Display</option>
                        <option value="montserrat" <?php if(($conf['font_family']??'default')=='montserrat')echo 'selected';?>>Montserrat</option>
                        <option value="pacifico" <?php if(($conf['font_family']??'default')=='pacifico')echo 'selected';?>>Pacifico</option>
                        <option value="dancing" <?php if(($conf['font_family']??'default')=='dancing')echo 'selected';?>>Dancing Script</option>
                        <option value="lobster" <?php if(($conf['font_family']??'default')=='lobster')echo 'selected';?>>Lobster</option>
                        <option value="raleway" <?php if(($conf['font_family']??'default')=='raleway')echo 'selected';?>>Raleway</option>
                        <option value="oswald" <?php if(($conf['font_family']??'default')=='oswald')echo 'selected';?>>Oswald</option>
                    </select>
                    <span class="help-block">选择您喜欢的字体家族</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">字体大小</label>
                <div class="col-sm-10">
                    <input type="number" name="font_size" value="<?php echo $conf['font_size'] ?? 14;?>" min="12" max="20" class="form-control">
                    <span class="help-block">设置全局字体大小，单位为像素，建议范围：12-20px</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">字体颜色</label>
                <div class="col-sm-10">
                    <input type="text" name="font_color" value="<?php echo $conf['font_color'] ?? '#333333';?>" class="form-control" placeholder="#333333">
                    <span class="help-block">设置全局字体颜色，格式为十六进制颜色代码，如：#333333</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>
<?php
} elseif ($mod == "rebaterecharge") {
	adminpermission("set", 1);
	?>      <div class="block">
			<div class="block-title">
				<h3 class="panel-title">充值返利设置</h3>
			</div>
			<div class="">
				<form onsubmit="return saveSetting(this)" method="post" class="form-horizontal form-bordered" role="form">
					<div class="form-group">
						<label class="col-sm-2 control-label">启用充值返利</label>
						<div class="col-sm-10"><select class="form-control" name="recharge_rebate_enabled" default="<?php echo isset($conf["recharge_rebate_enabled"]) ? $conf["recharge_rebate_enabled"] : 1;?>">
								<option value="0">关闭</option>
								<option value="1">开启</option>
							</select></div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">返利比例</label>
						<div class="col-sm-10"><input type="text" name="recharge_rebate_rate" value="<?php echo isset($conf["recharge_rebate_rate"]) ? $conf["recharge_rebate_rate"] : 3;?>" class="form-control" /> <span class="help-block">单位：%，例如：3 表示3%</span></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10"><input type="submit" name="submit" value="修改" class="btn btn-primary btn-block" />
						</div>
					</div>
				</form>
			</div>
			<div class="panel-footer">
				<span class="glyphicon glyphicon-info-sign"></span>
				设置用户充值时的返利比例，所有充值方式（在线充值、加款卡充值）均按此比例计算返利。<br />
				返利金额会在充值成功后自动发放到用户账户。
			</div>
		</div>
	<?php 
} elseif ($mod == "beautify_n" && $_POST["do"] == "submit") {
	$sakura_enable = $_POST["sakura_enable"];
	$sakura_num = $_POST["sakura_num"];
	$sakura_limit_times = $_POST["sakura_limit_times"];
	saveSetting("sakura_enable", $sakura_enable);
	saveSetting("sakura_num", $sakura_num);
	saveSetting("sakura_limit_times", $sakura_limit_times);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("前台美化设置保存成功！", 1);
	} else {
		showmsg("前台美化设置保存失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "beautify_admin_n" && $_POST["do"] == "submit") {
	// 鼠标美化设置处理
	$mouse_beautify = $_POST["mouse_beautify"];
	$mouse_style = $_POST["mouse_style"];
	saveSetting("mouse_beautify", $mouse_beautify);
	saveSetting("mouse_style", $mouse_style);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("鼠标美化设置保存成功！", 1);
	} else {
		showmsg("鼠标美化设置保存失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "admin_style_n" && $_POST["do"] == "submit") {
	// 后台整站UI风格设置处理
	$admin_ui_style = isset($_POST["admin_ui_style"]) ? trim($_POST["admin_ui_style"]) : '';
	$admin_ui_styles = array("", "none", "simple", "tech", "enterprise", "dark", "green", "orange", "purple", "red", "blue", "cyan", "pink", "gold", "coffee", "teal", "navy", "gray", "mint", "lavender", "rose", "sky", "ocean", "lime", "glass", "neon", "gradient", "cyber", "retro", "material", "flat", "minimal", "luxury", "forest", "sunset", "starry", "slate", "indigo", "violet", "coral", "peach", "cream", "honey", "charcoal", "wood", "bamboo", "sand", "candy", "pastel", "spring", "summer", "autumn", "winter", "aurora", "steampunk", "ink", "berry", "olive", "pumpkin", "grape");
	if (!in_array($admin_ui_style, $admin_ui_styles)) {
		$admin_ui_style = "none";
	}
	saveSetting("admin_ui_style", $admin_ui_style);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("后台UI风格设置保存成功！", 1);
	} else {
		showmsg("后台UI风格设置保存失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "beautify_font_n" && $_POST["do"] == "submit") {
	// 字体美化设置处理
	$font_beautify = $_POST["font_beautify"];
	$font_family = $_POST["font_family"];
	$font_size = $_POST["font_size"];
	$font_color = $_POST["font_color"];
	saveSetting("font_beautify", $font_beautify);
	saveSetting("font_family", $font_family);
	saveSetting("font_size", $font_size);
	saveSetting("font_color", $font_color);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("字体美化设置保存成功！", 1);
	} else {
		showmsg("字体美化设置保存失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "beautify_background_n" && $_POST["do"] == "submit") {
	// 背景美化设置处理
	$background_enable = $_POST["background_enable"];
	$background_foreground = $_POST["background_foreground"];
	$background_style = $_POST["background_style"];
	$background_speed = $_POST["background_speed"];
	$background_color = $_POST["background_color"];
	$background_image_enable = $_POST["background_image_enable"];
	$ui_colorto = $_POST["ui_colorto"];
	$ui_color1 = $_POST["ui_color1"];
	$ui_color2 = $_POST["ui_color2"];
	saveSetting("background_enable", $background_enable);
	saveSetting("background_foreground", $background_foreground);
	saveSetting("background_style", $background_style);
	saveSetting("background_speed", $background_speed);
	saveSetting("background_color", $background_color);
	saveSetting("background_image_enable", $background_image_enable);
	saveSetting("ui_colorto", $ui_colorto);
	saveSetting("ui_color1", $ui_color1);
	saveSetting("ui_color2", $ui_color2);
	
	// 处理背景图片上传
	if (isset($_FILES["background_image"]) && $_FILES["background_image"]["error"] == 0) {
		$allowedExts = array("gif", "jpeg", "jpg", "png");
		$temp = explode(".", $_FILES["background_image"]["name"]);
		$extension = end($temp);
		if ((($_FILES["background_image"]["type"] == "image/gif") || ($_FILES["background_image"]["type"] == "image/jpeg") || ($_FILES["background_image"]["type"] == "image/jpg") || ($_FILES["background_image"]["type"] == "image/pjpeg") || ($_FILES["background_image"]["type"] == "image/x-png") || ($_FILES["background_image"]["type"] == "image/png")) && ($_FILES["background_image"]["size"] < 10485760) && in_array($extension, $allowedExts)) {
			// 上传目录
			$uploadDir = ROOT . "assets/img/";
			if (!file_exists($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}
			// 保存文件，覆盖bj.png
			$targetFile = $uploadDir . "bj.png";
			if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $targetFile)) {
				compress_image($targetFile); // 自动压缩，加快前端加载
				// 保存成功
			} else {
				showmsg("背景图片上传失败！", 4);
			}
		} else {
			showmsg("背景图片格式错误或文件过大！", 4);
		}
	}
	
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("背景美化设置保存成功！", 1);
	} else {
		showmsg("背景美化设置保存失败！<br/>" . $DB->error(), 4);
	}
} elseif ($mod == "beautify") {
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">前台美化设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>前台美化功能说明：</b><br>
                1. 樱花落叶效果：在网站页面添加樱花飘落动画效果<br>
                2. 开启后将在所有页面显示樱花飘落效果<br>
                3. 可以调整樱花数量和飘落限制次数<br>
                4. 建议在性能较好的设备上使用，避免影响页面加载速度
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form" onsubmit="return saveSetting(this)">
            <input type="hidden" name="do" value="submit">
            <input type="hidden" name="mod" value="beautify_n">
            <div class="form-group">
                <label class="col-sm-2 control-label">樱花落叶效果</label>
                <div class="col-sm-10">
                    <select class="form-control" name="sakura_enable" default="<?php echo $conf['sakura_enable'] ?? 0;?>">
                        <option value="1" <?php if($conf['sakura_enable']==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if($conf['sakura_enable']!=1)echo 'selected';?>>关闭</option>
                    </select>
                    <span class="help-block">开启后将在网站页面显示樱花飘落效果</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">樱花数量</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="sakura_num" value="<?php echo intval($conf['sakura_num'] ?? 21);?>" min="1" max="100">
                    <span class="help-block">建议设置10-50个，数量过多可能影响页面性能</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">飘落限制次数</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="sakura_limit_times" value="<?php echo intval($conf['sakura_limit_times'] ?? 2);?>" min="-1" max="10">
                    <span class="help-block">设置-1为无限循环，0-10为限制次数</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>
<?php
} elseif ($mod == "beautify_background") {
?>
    <div class="block">
        <div class="block-title">
            <h3 class="panel-title">背景美化设置</h3>
        </div>
        <div class="panel panel-info">
            <div class="panel-heading">使用说明</div>
            <div class="panel-body" style="background:#e6f3fb;">
                <b>背景美化功能说明：</b><br>
                1. 背景动画效果：在网站页面添加动态背景效果<br>
                2. 提供多种背景动画类型选择<br>
                3. 可以调整动画速度和颜色<br>
                4. 建议在性能较好的设备上使用，避免影响页面加载速度
            </div>
        </div>
        <form method="post" class="form-horizontal form-bordered" role="form" enctype="multipart/form-data" onsubmit="return saveSetting(this)">
            <input type="hidden" name="do" value="submit">
            <input type="hidden" name="mod" value="beautify_background_n">
            <div class="form-group">
                <label class="col-sm-2 control-label">开启背景美化</label>
                <div class="col-sm-10">
                    <select class="form-control" name="background_enable" default="<?php echo $conf['background_enable'] ?? 0;?>">
                        <option value="1" <?php if(($conf['background_enable']??0)==1)echo 'selected';?>>开启</option>
                        <option value="0" <?php if(($conf['background_enable']??0)!=1)echo 'selected';?>>关闭</option>
                    </select>
                    <span class="help-block">开启后将在网站页面显示动态背景效果</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">前景动画（叠加层）</label>
                <div class="col-sm-10">
                    <select class="form-control" name="background_foreground" default="<?php echo $conf['background_foreground'] ?? 'none';?>">
                        <option value="none" <?php if(($conf['background_foreground']??'none')=='none')echo 'selected';?>>无</option>
                        <option value="particles" <?php if(($conf['background_foreground']??'none')=='particles')echo 'selected';?>>粒子效果</option>
                        <option value="bubbles" <?php if(($conf['background_foreground']??'none')=='bubbles')echo 'selected';?>>气泡效果</option>
                        <option value="matrix" <?php if(($conf['background_foreground']??'none')=='matrix')echo 'selected';?>>黑客效果</option>
                    </select>
                    <span class="help-block">选择叠加在背景上方的动画效果，可以与背景样式同时使用</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">背景样式（底层）</label>
                <div class="col-sm-10">
                    <select class="form-control" name="background_style" default="<?php echo $conf['background_style'] ?? 'gradient';?>">
                        <option value="gradient" <?php if(($conf['background_style']??'gradient')=='gradient')echo 'selected';?>>渐变背景</option>
                        <option value="grid" <?php if(($conf['background_style']??'gradient')=='grid')echo 'selected';?>>网格背景</option>
                        <option value="image" <?php if(($conf['background_style']??'gradient')=='image')echo 'selected';?>>自定义背景图片</option>
                    </select>
                    <span class="help-block">选择底层背景样式，可以与前景动画叠加使用</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">启用背景图片</label>
                <div class="col-sm-10">
                    <select class="form-control" name="background_image_enable" default="<?php echo $conf['background_image_enable'] ?? 1;?>">
                        <option value="1" <?php if(($conf['background_image_enable']??1)==1)echo 'selected';?>>启用</option>
                        <option value="0" <?php if(($conf['background_image_enable']??1)!=1)echo 'selected';?>>禁用</option>
                    </select>
                    <span class="help-block">控制背景图片的显示/隐藏</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">背景图片上传</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="text" class="form-control" value="当前背景图片：assets/img/bj.png" readonly style="height: 34px;">
                        <span class="input-group-btn">
                            <input type="file" name="background_image" class="btn btn-default" accept="image/*" style="height: 34px;">
                        </span>
                    </div>
                    <span class="help-block">上传背景图片，将会覆盖默认的bj.png文件</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">动画速度</label>
                <div class="col-sm-10">
                    <input type="range" min="1" max="10" value="<?php echo intval($conf['background_speed'] ?? 5);?>" name="background_speed" class="form-control">
                    <span class="help-block">调整背景动画的速度，1为最慢，10为最快</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">主色调</label>
                <div class="col-sm-10">
                    <input type="color" name="background_color" value="<?php echo $conf['background_color'] ?? '#3498db';?>">
                    <span class="help-block">选择背景动画的主色调</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">背景颜色</label>
                <div class="col-sm-10">
                    <div class="row">
                        <div class="col-xs-4">
                            <select class="form-control" name="ui_colorto" default="<?php echo $conf['ui_colorto'] ?? 0;?>">
                                <option value="0" <?php if(($conf['ui_colorto']??0)==0)echo 'selected';?>>纵向渐变</option>
                                <option value="1" <?php if(($conf['ui_colorto']??0)==1)echo 'selected';?>>横向渐变</option>
                            </select>
                        </div>
                        <div class="col-xs-4">
                            <div class="input-group input-colorpicker colorpicker-element">
                                <input type="text" name="ui_color1" class="form-control" placeholder="颜色16进制代码" maxlength="7" value="<?php echo $conf['ui_color1'] ?? '#3498db';?>">
                                <span class="input-group-addon"><i></i></span>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            <div class="input-group input-colorpicker colorpicker-element">
                                <input type="text" name="ui_color2" class="form-control" placeholder="颜色16进制代码" maxlength="7" value="<?php echo $conf['ui_color2'] ?? '#2980b9';?>">
                                <span class="input-group-addon"><i></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input type="submit" value="保存设置" class="btn btn-primary btn-block">
                </div>
            </div>
        </form>
    </div>
<?php
}
?><script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
	if (typeof $ !== 'undefined') {
	var items = $("select[default]");
	for (var i = 0; i < items.length; i++) {
		$(items[i]).val($(items[i]).attr("default") || 0);
	}
	}
	function saveSetting(obj) {
		if ($("input[name='fenzhan_domain']").length > 0) {
			var fenzhan_domain = $("input[name='fenzhan_domain']").val();
			$("input[name='fenzhan_domain']").val(fenzhan_domain.replace("，", ","));
		}
		if ($("input[name='fenzhan_remain']").length > 0) {
			var fenzhan_remain = $("input[name='fenzhan_remain']").val();
			$("input[name='fenzhan_remain']").val(fenzhan_remain.replace("，", ","));
		}
		var ii = layer.load(2, {
			shade: [0.1, '#fff']
		});
		$.ajax({
			type: 'POST',
			url: 'ajax.php?act=set',
			data: $(obj).serialize(),
			dataType: 'json',
			success: function(data) {
				layer.close(ii);
				if (data.code == 0) {
					layer.alert('设置保存成功！', {
						icon: 1,
						closeBtn: false
					}, function() {
						window.location.reload()
					});
				} else {
					layer.alert(data.msg, {
						icon: 2
					})
				}
			},
			error: function(data) {
				layer.msg('服务器错误');
				return false;
			}
		});
		return false;
	}

	function changeTemplate(template) {
		// 模板名校验（允许中文/字母/数字/横线/下划线），防止空值/非法字符提交
		if(!template || !/^[\u4e00-\u9fa5a-zA-Z0-9\-_]+$/.test(template)) {
			layer.alert('模板名称不合法');
			return false;
		}
		// 防重复点击
		if(window._aurTmplLock && (new Date().getTime() - window._aurTmplLock) < 1000) return false;
		window._aurTmplLock = new Date().getTime();
		var ii = layer.load(2, {
			shade: [0.1, '#fff']
		});
		$.ajax({
			type: 'POST',
			url: 'ajax.php?act=set',
			data: {
				template: template
			},
			dataType: 'json',
			success: function(data) {
				layer.close(ii);
				if (data.code == 0) {
					layer.alert('更换模板成功！', {
						icon: 1,
						closeBtn: false
					}, function() {
						window.location.reload()
					});
				} else {
					layer.alert(data.msg || '修改失败', {
						icon: 2
					})
				}
			},
			error: function(data) {
				layer.close(ii);
				layer.msg('服务器错误');
				return false;
			}
		});
		return false;
	}

	function thirdloginbind(type) {
		var typename = type == 'qq' ? 'QQ' : '微信';
		layer.open({
			type: 2,
			title: '绑定' + typename + '登录',
			shadeClose: true,
			closeBtn: 2,
			scrollbar: false,
			area: ['310px', '450px'],
			content: './bind.php?type=' + type
		});
	}

	function thirdloginunbind(type) {
		var typename = type == 'qq' ? 'QQ' : '微信';
		var confirmobj = layer.confirm('确定要解绑' + typename + '吗？', {
			btn: ['确定', '取消']
		}, function() {
			$.post("ajax.php?act=thirdloginunbind", {
				type: type
			}, function(arr) {
				if (arr.code == 0) {
					layer.alert(typename + '解绑成功！', {
						icon: 1,
						closeBtn: false
					}, function() {
						window.location.reload()
					});
				} else {
					layer.alert(data.msg, {
						icon: 2
					})
				}
			}, 'json');
		}, function() {
			layer.close(confirmobj);
		});
	}
</script>
</div>
</div>