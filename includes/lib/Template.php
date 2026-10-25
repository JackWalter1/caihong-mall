<?php
namespace lib;

class Template {

	static public function getList(){
		$dir = TEMPLATE_ROOT;
		$dirArray = array();
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                // 仅保留非隐藏目录（排除 . .. 隐藏目录与文件），避免空项/隐藏项混入模板列表
                if ($file != "." && $file != ".." && substr($file, 0, 1) !== '.' && is_dir($dir . $file)) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            closedir($handle);
        }
        return $dirArray;
	}

	static public function load($name = 'index'){
		global $conf;
		$template = $conf['template']?$conf['template']:'default';
		if(checkmobile() && $conf['template_m'] && $conf['template_m']!='0')$template = $conf['template_m'];
		// 模板名合法性校验：允许中文/字母/数字/横线/下划线，禁止 . / \ 防止路径穿越
		if(!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9\-_]+$/u',$name))exit('error');
		$filename = TEMPLATE_ROOT.$template.'/'.$name.'.php';
		$filename_default = TEMPLATE_ROOT.'default/'.$name.'.php';
		if(file_exists($filename)){
			return $filename;
		}elseif(file_exists($filename_default)){
			return $filename_default;
		}else{
			exit('Template file not found');
		}
	}

	static public function loadConfig(){
		global $conf;
		$template = $conf['template']?$conf['template']:'default';
		if(checkmobile() && $conf['template_m'] && $conf['template_m']!='0')$template = $conf['template_m'];
		$filename = TEMPLATE_ROOT.$template.'/config.php';
		if(file_exists($filename)){
			include($filename);
			return $template_info;
		}else{
			return false;
		}
	}

	static public function loadSetting(){
		global $conf;
		$template = $conf['template']?$conf['template']:'default';
		if(checkmobile() && $conf['template_m'] && $conf['template_m']!='0')$template = $conf['template_m'];
		$filename = TEMPLATE_ROOT.$template.'/config.php';
		if(file_exists($filename)){
			include($filename);
			return $template_settings;
		}else{
			return false;
		}
	}

	static public function loadRoute(){
		global $conf;
		$template = $conf['template']?$conf['template']:'default';
		if(checkmobile() && $conf['template_m'] && $conf['template_m']!='0')$template = $conf['template_m'];
		$filename = TEMPLATE_ROOT.$template.'/config.php';
		if(file_exists($filename)){
			include($filename);
			$var = checkmobile()&&isset($template_route_m)?$template_route_m:$template_route;
			return $var;
		}else{
			return false;
		}
	}

	static public function exists($template){
		$filename = TEMPLATE_ROOT.$template.'/index.php';
		if(file_exists($filename)){
			return true;
		}else{
			return false;
		}
	}

	static public function random_picture(){
		// 随机背景图片列表
		$pictures = [
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rB9hATbiaYR8DKeoBjvXKDiaztELl90ImXtQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rEshdOekfrjFoGh0hBA8c2vibktcVN3H4VQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rAqMVFGXIjpQDdGwL1n1LvNquw24Crs5mg/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rLyFCru0fnP8oWnG93s6OEsa8fk2RD0EHg/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rFMmsT2rFzmtWB348ZqZ4LMNicymcMN7aXA/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rABIXwWibeVsTyEPCic3rgFd5Ub3Ws2icOqPQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rNW4Uq2HNeh8aHey8bmupSJ3yO7RPpZkCg/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rNNWdJiab8eNj8ZChtz0TgXVg1kHrObSqSA/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rLNOpGUsNEDKCZpYoDahH3mDCyrKND9ibDA/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rIoIJlvYCU6opxj4JJO6yMKFaicjJgic6ANw/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rG3771XCyQ5icOLEicWRpicdibyQZMjmy2etZA/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rOs3FibDFlCNW2aC9vT9LNGXic9g7GQLxQfA/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rHHlAmEkUg7Jmjiatiaqz78XYCx8xuLTib59Q/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rMib5Dm9OgbxulhqbpiahUIyk9qakuvFiaSDQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rBG38IngZdEnl4NT7DELu5guRSILZrpPdw/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rHpQ82QF1aWtAh0Hm04BicibHtaYYRQgLVpQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rD7nSficJUDnkic8RzzJmrFB11F5mlofSvibg/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rDhhxIRqibnG3euW0K6SicW2ZTkbg0up6WUQ/0',
			'//puep.qpic.cn/coral/Q3auHgzwzM4fgQ41VTF2rMgTxnRYrIaz01y9pXd8EBZJwyibOYgUjoQ/0',
		];

		return $pictures[array_rand($pictures)];
	}

	static public function getBackground($path=null){
		global $conf,$DB,$CACHE;
		if($conf['ui_bing']==1){
			$background_image = self::random_picture();
			$background_css = '<style>body{ background:#ecedf0 url("'.$background_image.'") fixed;background-repeat:no-repeat;background-size:100% 100%;}</style>';
		}elseif($conf['ui_bing']==2){
			if(date("Ymd")==$conf['ui_bing_date']){
				$background_image=$conf['ui_backgroundurl'];
				if(checkmobile()==true)$background_image=str_replace('1920x1080','768x1366',$background_image);
			}else{
				$url = 'https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=zh-CN';
				$bing_data = get_curl($url);
				$bing_arr=json_decode($bing_data,true);
				if (!empty($bing_arr['images'][0]['url'])) {
					$background_image='//cn.bing.com'.$bing_arr['images'][0]['url'];
					saveSetting('ui_backgroundurl', $background_image);
					saveSetting('ui_bing_date', date("Ymd"));
					$CACHE->clear();
					if(checkmobile()==true)$background_image=str_replace('1920x1080','768x1366',$background_image);
				}
			}
			$background_css = '<style>body{ background:#ecedf0 url("'.$background_image.'") fixed;background-repeat:no-repeat;background-size:100% 100%;}</style>';
		}elseif($conf['ui_bing']==3){
			if($conf['ui_colorto']==1){
				$background_css = '<style>body{ background: linear-gradient(to right,'.$conf['ui_color1'].','.$conf['ui_color2'].') fixed;}</style>';
			}else{
				$background_css = '<style>body{ background: linear-gradient(to bottom,'.$conf['ui_color1'].','.$conf['ui_color2'].') fixed;}</style>';
			}
		}else{
			// 检查是否启用了背景图片
			if($conf['background_image_enable'] == 1){
				$background_image=$path.'assets/img/bj.png';
				if($conf['ui_background']==0)
				$repeat='background-repeat:repeat;';
				elseif($conf['ui_background']==1)
				$repeat='background-repeat:repeat-x; background-size:auto 100%;';
				elseif($conf['ui_background']==2)
				$repeat='background-repeat:repeat-y; background-size:100% auto;';
				elseif($conf['ui_background']==3)
				$repeat='background-repeat:no-repeat; background-size:100% 100%;';
				$background_css = '<style>body{ background:#ecedf0 url("'.$background_image.'") fixed;'.$repeat.'}</style>';
			}else{
				// 禁用背景图片，只设置背景色
				$background_css = '<style>body{ background:#ecedf0; }</style>';
			}
			
			// 添加背景动画效果
			if($conf['background_enable'] == 1){
				$background_foreground = $conf['background_foreground'] ?? 'none';
				$background_style = $conf['background_style'] ?? 'gradient';
				$background_speed = $conf['background_speed'] ?? 5;
				$background_color = $conf['background_color'] ?? '#3498db';
				$ui_colorto = $conf['ui_colorto'] ?? 0;
				$ui_color1 = $conf['ui_color1'] ?? '#3498db';
				$ui_color2 = $conf['ui_color2'] ?? '#2980b9';
				$gradientDirection = $ui_colorto == 0 ? '180deg' : '90deg';
				
				$background_css .= '<style>
					/* 背景美化样式 - 底层背景 */
					.background-style {
					  position: fixed;
					  top: 0;
					  left: 0;
					  width: 100%;
					  height: 100%;
					  z-index: -2;
					}
					
					/* 背景美化样式 - 前景动画 */
					#background-canvas {
					  position: fixed;
					  top: 0;
					  left: 0;
					  width: 100%;
					  height: 100%;
					  z-index: -1;
					}
					
					/* 渐变背景样式 */
					.gradient-background {
					  position: fixed;
					  top: 0;
					  left: 0;
					  width: 100%;
					  height: 100%;
					  z-index: -2;
					  background: linear-gradient('.$gradientDirection.', '.$ui_color1.' 0%, '.$ui_color2.' 100%);
					  animation: gradient-animation '.(20 - ($background_speed * 1.5)).'s ease infinite;
					}
					
					@keyframes gradient-animation {
					  0% { background-position: 0% 50%; }
					  50% { background-position: 100% 50%; }
					  100% { background-position: 0% 50%; }
					}
					
					/* 网格背景样式 */
					.grid-background {
					  position: fixed;
					  top: 0;
					  left: 0;
					  width: 100%;
					  height: 100%;
					  z-index: -2;
					  background-color: #F3F3F3;
					  background-image: linear-gradient(0deg, transparent 24%, #E1E1E1 25%, #E1E1E1 26%, transparent 27%,transparent 74%, #E1E1E1 75%, #E1E1E1 76%, transparent 77%,transparent),
						linear-gradient(90deg, transparent 24%, #E1E1E1 25%, #E1E1E1 26%, transparent 27%,transparent 74%, #E1E1E1 75%, #E1E1E1 76%, transparent 77%,transparent);
					  background-size: 55px 55px;
					}
					
					/* 自定义图片背景样式 */
					.image-background {
					  position: fixed;
					  top: 0;
					  left: 0;
					  width: 100%;
					  height: 100%;
					  z-index: -2;
					  background-image: url(/assets/img/bj.png);
					  background-size: cover;
					  background-position: center;
					}
				</style>';
				
				$background_css .= '<script>
					// 背景美化效果
					document.addEventListener("DOMContentLoaded", function() {
					  const foregroundType = "'.$background_foreground.'";
					  const styleType = "'.$background_style.'";
					  const backgroundSpeed = '.$background_speed.';
					  const backgroundColor = "'.$background_color.'";
					  const uiColor1 = "'.$ui_color1.'";
					  const uiColor2 = "'.$ui_color2.'";
					  const uiColorto = '.$ui_colorto.';
					  
					  // 创建背景样式（底层）
					  function createBackgroundStyle() {
						if (styleType === "gradient") {
						  // 渐变背景
						  const gradientDiv = document.createElement("div");
						  gradientDiv.className = "gradient-background";
						  const gradientDirection = uiColorto === 0 ? "180deg" : "90deg";
						  gradientDiv.style.background = `linear-gradient(${gradientDirection}, ${uiColor1} 0%, ${uiColor2} 100%)`;
						  document.body.appendChild(gradientDiv);
						} else if (styleType === "grid") {
						  // 网格背景
						  const gridDiv = document.createElement("div");
						  gridDiv.className = "grid-background";
						  document.body.appendChild(gridDiv);
						} else if (styleType === "image") {
						  // 自定义背景图片
						  const imageDiv = document.createElement("div");
						  imageDiv.className = "image-background";
						  document.body.appendChild(imageDiv);
						}
					  }
					  
					  // 创建前景动画（叠加层）
					  function createForegroundAnimation() {
						if (foregroundType === "none") {
						  return;
						}
						
					  // 创建canvas
					  const canvas = document.createElement("canvas");
					  canvas.id = "background-canvas";
					  document.body.appendChild(canvas);
					  
					  const ctx = canvas.getContext("2d");
					  canvas.width = window.innerWidth;
					  canvas.height = window.innerHeight;
					  
					  // 根据前景类型绘制不同效果
					  if (foregroundType === "particles") {
						// 粒子效果
						const particles = [];
						const particleCount = 100;
						
						// 初始化粒子
						for (let i = 0; i < particleCount; i++) {
						  particles.push({
							x: Math.random() * canvas.width,
							y: Math.random() * canvas.height,
							radius: Math.random() * 3 + 1,
							color: backgroundColor,
							speedX: (Math.random() - 0.5) * (backgroundSpeed / 2),
							speedY: (Math.random() - 0.5) * (backgroundSpeed / 2)
						  });
						}
						
						// 动画循环
						function animate() {
						  ctx.clearRect(0, 0, canvas.width, canvas.height);
						  
						  particles.forEach(particle => {
							// 更新位置
							particle.x += particle.speedX;
							particle.y += particle.speedY;
							
							// 边界检测
							if (particle.x < 0 || particle.x > canvas.width) {
							  particle.speedX *= -1;
							}
							if (particle.y < 0 || particle.y > canvas.height) {
							  particle.speedY *= -1;
							}
							
							// 绘制粒子
							ctx.beginPath();
							ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
							ctx.fillStyle = particle.color;
							ctx.fill();
						  });
						  
						  requestAnimationFrame(animate);
						}
						animate();
					  } else if (foregroundType === "matrix") {
						// 黑客效果
						const characters = "01"; 
						const fontSize = 14;
						const columns = canvas.width / fontSize;
						const drops = [];
						
						// 初始化雨滴位置
						for (let x = 0; x < columns; x++) {
						  drops[x] = 1;
						}
						
						// 绘制矩阵
						function drawMatrix() {
						  // 半透明背景覆盖
						  ctx.fillStyle = "rgba(0, 0, 0, 0.04)";
						  ctx.fillRect(0, 0, canvas.width, canvas.height);
						  
						  ctx.fillStyle = "#00ff00";
						  ctx.font = fontSize + "px monospace";
						  
						  for (let i = 0; i < drops.length; i++) {
							const text = characters.charAt(Math.floor(Math.random() * characters.length));
							ctx.fillText(text, i * fontSize, drops[i] * fontSize);
							
							if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
							  drops[i] = 0;
							}
							drops[i]++;
						  }
						}
						setInterval(drawMatrix, 33 / (backgroundSpeed / 5));
					  } else if (foregroundType === "bubbles") {
						// 气泡效果
						const bubbles = [];
						const bubbleCount = 50;
						
						// 初始化气泡
						for (let i = 0; i < bubbleCount; i++) {
						  bubbles.push({
							x: Math.random() * canvas.width,
							y: Math.random() * canvas.height,
							radius: Math.random() * 5 + 1,
							color: backgroundColor,
							speedX: (Math.random() - 0.5) * (backgroundSpeed / 3),
							speedY: -Math.random() * (backgroundSpeed / 3) - 1
						  });
						}
						
						// 动画循环
						function animateBubbles() {
						  ctx.clearRect(0, 0, canvas.width, canvas.height);
						  
						  bubbles.forEach(bubble => {
							// 更新位置
							bubble.x += bubble.speedX;
							bubble.y += bubble.speedY;
							
							// 边界检测
							if (bubble.x < -bubble.radius || bubble.x > canvas.width + bubble.radius) {
							  bubble.x = Math.random() * canvas.width;
							  bubble.y = canvas.height + bubble.radius;
							}
							if (bubble.y < -bubble.radius) {
							  bubble.y = canvas.height + bubble.radius;
							}
							
							// 绘制气泡
							ctx.beginPath();
							ctx.arc(bubble.x, bubble.y, bubble.radius, 0, Math.PI * 2);
							ctx.fillStyle = bubble.color;
							ctx.globalAlpha = 0.5;
							ctx.fill();
							ctx.globalAlpha = 1;
						  });
						  
						  requestAnimationFrame(animateBubbles);
						}
						animateBubbles();
					  }
					}
					  
					  // 创建背景样式
					  createBackgroundStyle();
					  // 创建前景动画
					  createForegroundAnimation();
					});
				</script>';
			}
		}
		return [$background_image, $background_css];
	}
}
