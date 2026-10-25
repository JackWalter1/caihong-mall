	<!--弹出公告-->
	<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">
							×
						</span>
						<span class="sr-only">
							Close
						</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">
						<?php echo $conf['sitename']?>
					</h4>
				</div>
				<div class="modal-body">
					<?php echo $conf['modal']?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						知道啦
					</button>
				</div>
			</div>
		</div>
	</div>
	<!--弹出公告-->
	<!--公告-->
	<div class="modal fade" align="left" id="mustsee" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">
							×
						</span>
						<span class="sr-only">
							Close
						</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">
						公告
					</h4>
				</div>
				<div class="modal-body">
					<?php echo $conf['anounce']?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						关闭
					</button>
				</div>
			</div>
		</div>
	</div>
	<!--公告-->
	<!--平台公告Modal-->
	<div class="modal fade" id="anounce" tabindex="-1" role="dialog" aria-labelledby="anounceLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="anounceLabel">平台公告</h4>
				</div>
				<div class="modal-body">
					<?php echo $conf['anounce']?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				</div>
			</div>
		</div>
	</div>
	<!--平台公告Modal结束-->
	<!--客服Modal-->
	<div id="lxkf" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; background: white; border-radius: 15px; overflow: hidden; border: 2px solid #bcd4f0; box-shadow: 0 5px 20px rgba(46,134,222,0.1); max-width: 90%; width: 90%; max-height: 90vh; overflow-y: auto;">
	<style>
		/* 自适应样式 */
		@media (max-width: 768px) {
			#lxkf {
				width: 95% !important;
				border-radius: 10px !important;
			}
			#lxkf .block-content {
				padding: 15px !important;
			}
			#lxkf .modal-header {
				padding: 15px !important;
			}
			#lxkf .modal-footer {
				padding: 10px 15px !important;
			}
			#lxkf .media {
				flex-direction: column;
				text-align: center;
			}
			#lxkf .media .pull-left {
				margin-right: 0 !important;
				margin-bottom: 10px !important;
			}
			#lxkf .media .pull-left img {
				width: 60px !important;
				height: 60px !important;
			}
		}
	</style>
	<!-- 标题部分 -->
	<div style="background: linear-gradient(135deg, #2e86de 0%, #2e86de 100%); padding: 20px 30px;">
		<button type="button" onclick="closeModal()" style="color: #fff; opacity: 0.9; text-shadow: none; margin-top: -10px; background: none; border: none; font-size: 28px; float: right;">
			<span aria-hidden="true">×</span>
		</button>
		<h4 style="color: #fff; letter-spacing: 2px; font-weight: 600; text-shadow: 0 2px 4px rgba(0,0,0,0.2); margin: 0;">
			<i class="fa fa-headphones fa-fw"></i> 客服与帮助
		</h4>
	</div>

	<!-- 内容区域 -->
	<div style="padding: 25px 30px; background: #eaf3ff; min-height: 200px; font-size: 15px; line-height: 1.8;">
		<!-- 问题1 -->
		<div style="margin-bottom: 15px; border-radius: 10px; border: 1px solid #bcd4f0; box-shadow: 0 2px 6px rgba(46,134,222,0.1);">
			<div style="background: #f0f6ff; border-bottom: 1px solid #bcd4f0; border-radius: 10px 10px 0 0; padding: 15px 20px;">
				<h4 style="margin: 0;">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" style="color: #2e86de; text-decoration: none; display: block; font-weight: 600; font-size: 16px;" aria-expanded="true">
						<i class="fa fa-question-circle" style="margin-right: 10px;"></i>
						购买的辅助导致账号封禁？
					</a>
				</h4>
			</div>
			<div id="collapseOne" class="panel-collapse collapse in" style="" aria-expanded="true">
				<div style="background: #FFF; border-radius: 0 0 10px 10px; padding: 20px;">
					<div style="color: #2e86de; line-height: 1.6;">
						<div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
							<i class="fa fa-info-circle fa-fw" style="color: #2e86de; min-width: 20px; margin-top: 2px;"></i>
							<span>本站所有项目均源自互联网收集</span>
						</div>
						<div style="display: flex; align-items: flex-start;">
							<i class="fa fa-exclamation-triangle fa-fw" style="color: #2e86de; min-width: 20px; margin-top: 2px;"></i>
							<span>下单前已明确标注封号无售后说明</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 问题2 -->
		<div style="margin-bottom: 15px; border-radius: 10px; border: 1px solid #bcd4f0; box-shadow: 0 2px 6px rgba(46,134,222,0.1);">
			<div style="background: #f0f6ff; border-bottom: 1px solid #bcd4f0; border-radius: 10px 10px 0 0; padding: 15px 20px;">
				<h4 style="margin: 0;">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="" style="color: #2e86de; text-decoration: none; display: block; font-weight: 600; font-size: 16px;" aria-expanded="true">
						<i class="fa fa-question-circle" style="margin-right: 10px;"></i>
						买了的项目不会使用？
					</a>
				</h4>
			</div>
			<div id="collapseThree" class="panel-collapse collapse in" style="" aria-expanded="true">
				<div style="background: #FFF; border-radius: 0 0 10px 10px; padding: 20px;">
					<div style="color: #2e86de; line-height: 1.6;">
						<div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
							<i class="fa fa-info-circle fa-fw" style="color: #2e86de; min-width: 20px; margin-top: 2px;"></i>
							<span>确认下载地址可正常访问</span>
						</div>
						<div style="display: flex; align-items: flex-start;">
							<i class="fa fa-exclamation-triangle fa-fw" style="color: #2e86de; min-width: 20px; margin-top: 2px;"></i>
							<span>仔细阅读商品教程文档</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- 问题3 -->
		<div style="margin-bottom: 15px; border-radius: 10px; border: 1px solid #bcd4f0; box-shadow: 0 2px 6px rgba(46,134,222,0.1);">
			<div style="background: #f0f6ff; border-bottom: 1px solid #bcd4f0; border-radius: 10px 10px 0 0; padding: 15px 20px;">
				<h4 style="margin: 0;">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth" class="" style="color: #2e86de; text-decoration: none; display: block; font-weight: 600; font-size: 16px;" aria-expanded="true">
						<i class="fa fa-question-circle" style="margin-right: 10px;"></i>
						人工客服的售后范围？
					</a>
				</h4>
			</div>
			<div id="collapseFourth" class="panel-collapse collapse in" style="" aria-expanded="true">
				<div style="background: #FFF; border-radius: 0 0 10px 10px; padding: 20px;">
					<div style="color: #2e86de; line-height: 1.6;">
						<i class="fa fa-check-circle" style="color: #2e86de; margin-right: 8px;"></i>
						处理项目无效果问题，处理下载地址失效问题，负责树立平台担保责任
					</div>
				</div>
			</div>
		</div>

		<!-- 提示信息 -->
		<div style="background: #f0f6ff; border: 1px solid #bcd4f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #2e86de; font-weight: 500;">
			<i class="fa fa-exclamation-circle" style="margin-right: 8px;"></i>
			站长没有办法处理？点击下方联系平台人工客服解决
		</div>
		
		<!-- 客服联系区域 -->
		<div style="background: #f0f6ff; border: 1px solid #bcd4f0; border-radius: 10px; padding: 20px; box-shadow: 0 2px 6px rgba(46,134,222,0.1);">
			<div style="display: flex; align-items: center;">
				<!-- 客服头像 -->
				<div style="margin-right: 15px;">
					<div style="width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,#2e86de 0%,#54a0ff 100%);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(46,134,222,.3);"><i class="fa fa-headphones" style="color:#fff;font-size:30px;"></i></div>
				</div>
				
				<!-- 客服信息 -->
				<div style="flex: 1;">
					<div style="color: #2e86de; font-size: 18px; font-weight: 600; margin-bottom: 5px;">
						平台无引流人工客服
					</div>
					<div style="color: #3d4654; margin-bottom: 10px; font-size: 15px;">
						<i class="fa fa-clock-o" style="margin-right: 5px;"></i>
						<b>在线时间：早10：00 - 晚22：00</b>
					</div>
					<a href="./content.html" target="_blank" style="color: #2e86de; text-decoration: none;">
						<i class="fa fa-commenting"></i> 点击可查看自助解决教程
					</a>
				</div>

				
			</div>
		</div>
	</div>

	<!-- 底部 -->
	<div style="border-top: 1px solid #bcd4f0; padding: 15px 30px; background: #f0f6ff; text-align: right;">
		<button type="button" onclick="closeModal()" style="background: linear-gradient(45deg, #2e86de, #54a0ff); color: #fff; border: none; border-radius: 20px; padding: 8px 25px; transition: all 0.3s ease;">
			<i class="fa fa-check-circle"></i> 知道啦
		</button>
	</div>
</div>

<!-- 遮罩层 -->
<div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>

<!-- JavaScript -->
<script>
	// 显示弹窗
	function showModal() {
		document.getElementById('lxkf').style.display = 'block';
		document.getElementById('modalOverlay').style.display = 'block';
	}

	// 关闭弹窗
	function closeModal() {
		document.getElementById('lxkf').style.display = 'none';
		document.getElementById('modalOverlay').style.display = 'none';
	}

	// 点击遮罩层关闭弹窗
	document.getElementById('modalOverlay').onclick = closeModal;

	// 修改a标签的点击事件
	document.addEventListener('DOMContentLoaded', function() {
		var links = document.querySelectorAll('a[href="#lxkf"]');
		links.forEach(function(link) {
			link.removeAttribute('data-toggle');
			link.onclick = function(e) {
				e.preventDefault();
				showModal();
			};
		});
	});
</script>