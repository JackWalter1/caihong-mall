				<div class="modal fade version-modal-wrap" id="userjs" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-dialog-popin">
						<div class="modal-content">
							<div class="block block-themed block-transparent remove-margin-b">
								<div class="block-header bg-primary-dark">
									<ul class="block-options">
										<li>
											<button data-dismiss="modal" type="button">
												<i class="si si-close">
												</i>
											</button>
										</li>
									</ul>
									<h4 class="block-title">
										版本介绍
									</h4>
								</div>
								<div class="modal-body">
									<div class="table-responsive">
										<table class="table table-borderless table-vcenter">
											<thead>
												<tr>
													<th style="width: 100px;">
														功能
													</th>
													<th class="text-center" style="width: 20px;">
														普及版/专业版
													</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>
														专属代刷平台
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														三种在线支付接口
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														专属网站域名
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														赚取用户提成
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														赚取下级分站提成
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-danger">
															<i class="fa fa-close">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														设置商品价格
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														设置下级分站商品价格
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-danger">
															<i class="fa fa-close">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														搭建下级分站
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-danger">
															<i class="fa fa-close">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
												<tr>
													<td>
														赠送专属精致APP
													</td>
													<td class="text-center">
														<span class="btn btn-effect-ripple btn-xs btn-danger">
															<i class="fa fa-close">
															</i>
														</span>
														<span class="btn btn-effect-ripple btn-xs btn-success">
															<i class="fa fa-check">
															</i>
														</span>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
									<center style="color: #b2b2b2;">
										<small>
											<em>
												* 自己的能力决定着你的收入！
											</em>
										</small>
									</center>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">
									关闭
								</button>
							</div>
						</div>
					</div>
				</div>
				<!--版本介绍-->
				<!--关于我们弹窗-->
				<div class="modal fade" align="left" id="about" tabindex="-1" role="dialog"
				aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">
									<span aria-hidden="true">
										&times;
									</span>
									<span class="sr-only">
										Close
									</span>
								</button>
								<h4 class="modal-title" id="myModalLabel">
									新手下单帮助
								</h4>
							</div>
							<div class="modal-body">
								<a href="javascript:void(0)" class="widget">
									<center>
										<strong>
											<font size="3">
												站长ＱＱ：
												<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq']?>&site=qq&menu=yes"
												target="_blank">
													<?php echo $conf['kfqq']?>
												</a>
											</font>
										</strong>
										<br />
										<strong>
											<font size="2">
												本站域名：<?php echo $_SERVER['HTTP_HOST']; ?>
											</font>
										</strong>
									</center>
									<center>
										<div id="demo-acc-faq" class="panel-group accordion">
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq1" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													下单很久了都没有开始刷呢？
												</a>
												<div id="demo-acc-faq1" class="mar-ver collapse" aria-expanded="false"
												style="height: 0px;">
													由于本站采用全自动订单处理，有几率出现漏单，部分单子处理时间可能会稍长一点，不过都会完成，最终解释权归本站所有。超过24小时没处理请联系客服！
												</div>
											</div>
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq2" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													ＱＱ空间业务类下单方法讲解
												</a>
												<div id="demo-acc-faq2" class="mar-ver collapse" aria-expanded="false">
													1.下单前：空间必须是所有人可访问,必须自带1~4条原创说说!
													<br>
													2.代刷期间，禁止关闭访问权限，或者删除说说，删除说说的一律由自行负责，不给予补偿。
												</div>
											</div>
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq3" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													空间说说赞相关下单方法讲解
												</a>
												<div id="demo-acc-faq3" class="mar-ver collapse" aria-expanded="false">
													1.下单前：空间必须是所有人可访问,必须自带1条原创说说!转发的说说不能刷！
													<br>
													2.在“QQ号码”栏目输入QQ号码，点击下面的获取说说ID并选择你需要刷的说说的ID，下单即可。
													<br>
													3.代刷期间，禁止关闭访问权限，或者删除说说，删除说说的一律由自行负责，不给予补偿。
												</div>
											</div>
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq4" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													全民Ｋ歌业务类下单方法讲解
												</a>
												<div id="demo-acc-faq4" class="mar-ver collapse" aria-expanded="false">
													1.打开你的全名k歌
													<br>
													2.复制你全名k歌里面的需要刷的歌曲链接
													<br>
													3.例如：你歌曲链接是：
													<font color="#ff0000">
														https://kg.qq.com/node/play?s=
														<font color="green">
															881Zbk8aCfIwA8U3
														</font>
														&amp;g_f=personal
													</font>
													<br>
													4.然后把s=后面的
													<font color="green">
														881Zbk8aCfIwA8U3
													</font>
													链接填入到歌曲ID里面，然后提交购买。
												</div>
											</div>
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq5" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													快手业务类代刷下单方法讲解
												</a>
												<div id="demo-acc-faq5" class="mar-ver collapse" aria-expanded="false">
													1.需要填写用户ID和作品ID，比如
													<font color="#ff0000">
														http://www.kuaishou.com/i/photo/lwx?userId=
														<font color="green">
															294200023
														</font>
														&amp;photoId=
														<font color="green">
															1071823418
														</font>
													</font>
													(分享作品就可以看到“复制链接”了)
													<br>
													2.用户ID就是
													<font color="green">
														294200023
													</font>
													作品ID就是
													<font color="green">
														1071823418
													</font>
													，然后在分别把用户ID和作品ID填上，请勿把两个选项填反了，不给予补单！
												</div>
											</div>
											<div class="panel panel-trans pad-top">
												<a href="#demo-acc-faq6" class="text-semibold text-lg text-main collapsed"
												data-toggle="collapse" data-parent="#demo-acc-faq" aria-expanded="false">
													永久ＱＱ会员/钻下单方法讲解
												</a>
												<div id="demo-acc-faq6" class="mar-ver collapse" aria-expanded="false">
													1.下单之前，先确认输的信息是不是正确的!
													<br>
													2.Q会员/钻因为需要人工处理，所以每天不定时开刷，24小时-48小时内到账！
												</div>
											</div>
										</div>
									</center>
								</a>
							</div>
						</div>
					</div>
				</div>