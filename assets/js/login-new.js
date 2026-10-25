// 登录页动画交互脚本
(function() {
  'use strict';

  let mouseX = 0, mouseY = 0;
  let isTyping = false;
  let showPassword = false;
  let passwordLen = 0;
  let purpleBlink = false, blackBlink = false;
  let lookingAtEachOther = false;
  let purplePeeking = false;

  // 等待DOM加载完成
  function init() {
    const $purple = document.getElementById("purple");
    const $black = document.getElementById("black");
    const $orange = document.getElementById("orange");
    const $yellow = document.getElementById("yellow");
    const $purpleEyes = document.getElementById("purple-eyes");
    const $blackEyes = document.getElementById("black-eyes");
    const $orangeEyes = document.getElementById("orange-eyes");
    const $yellowEyes = document.getElementById("yellow-eyes");
    const $yellowMouth = document.getElementById("yellow-mouth");
    const $userInput = document.querySelector('input[name="user"]');
    const $passwordInput = document.querySelector('input[name="pass"]');
    const $togglePw = document.getElementById("togglePw");
    const $eyeIcon = document.getElementById("eyeIcon");
    const $eyeOffIcon = document.getElementById("eyeOffIcon");

    if (!$purple) return; // 没有动画角色则退出

    // 鼠标移动监听
    document.addEventListener("mousemove", (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    // 用户名输入框事件
    if ($userInput) {
      $userInput.addEventListener("focus", () => {
        isTyping = true;
        triggerLookAtEachOther();
      });
      $userInput.addEventListener("blur", () => {
        isTyping = false;
        lookingAtEachOther = false;
      });
    }

    // 密码输入框事件
    if ($passwordInput) {
      $passwordInput.addEventListener("input", () => {
        passwordLen = $passwordInput.value.length;
      });
      $passwordInput.addEventListener("focus", () => {
        isTyping = true;
        triggerLookAtEachOther();
      });
      $passwordInput.addEventListener("blur", () => {
        isTyping = false;
        lookingAtEachOther = false;
      });
    }

    // 密码可见性切换
    if ($togglePw && $eyeIcon && $eyeOffIcon) {
      $togglePw.addEventListener("click", () => {
        showPassword = !showPassword;
        if ($passwordInput) {
          $passwordInput.type = showPassword ? "text" : "password";
        }
        $eyeIcon.style.display = showPassword ? "none" : "";
        $eyeOffIcon.style.display = showPassword ? "" : "none";
      });
    }

    // 眨眼动画调度
    scheduleBlink((v) => { purpleBlink = v; });
    scheduleBlink((v) => { blackBlink = v; });

    // 偷看动画调度
    const peekInterval = setInterval(() => {
      if (passwordLen > 0 && showPassword && !purplePeeking) schedulePeek();
    }, 1000);

    // 开始渲染动画
    requestAnimationFrame(() => render({
      $purple, $black, $orange, $yellow,
      $purpleEyes, $blackEyes, $orangeEyes, $yellowEyes, $yellowMouth
    }));
  }

  function triggerLookAtEachOther() {
    lookingAtEachOther = true;
    setTimeout(() => {
      lookingAtEachOther = false;
    }, 800);
  }

  function scheduleBlink(setter) {
    const delay = Math.random() * 4000 + 3000;
    setTimeout(() => {
      setter(true);
      setTimeout(() => {
        setter(false);
        scheduleBlink(setter);
      }, 150);
    }, delay);
  }

  function schedulePeek() {
    if (passwordLen > 0 && showPassword) {
      const delay = Math.random() * 3000 + 2000;
      setTimeout(() => {
        if (passwordLen > 0 && showPassword) {
          purplePeeking = true;
          setTimeout(() => {
            purplePeeking = false;
            schedulePeek();
          }, 800);
        }
      }, delay);
    }
  }

  function calcPos(el) {
    const rect = el.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 3;
    const dx = mouseX - cx;
    const dy = mouseY - cy;
    return {
      faceX: Math.max(-15, Math.min(15, dx / 20)),
      faceY: Math.max(-10, Math.min(10, dy / 30)),
      bodySkew: Math.max(-6, Math.min(6, -dx / 120)),
    };
  }

  function eyePupilOffset(el, maxDist, forceX, forceY) {
    if (forceX !== undefined && forceY !== undefined)
      return { x: forceX, y: forceY };
    const rect = el.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;
    const dx = mouseX - cx;
    const dy = mouseY - cy;
    const dist = Math.min(Math.sqrt(dx * dx + dy * dy), maxDist);
    const angle = Math.atan2(dy, dx);
    return { x: Math.cos(angle) * dist, y: Math.sin(angle) * dist };
  }

  function render(els) {
    const { $purple, $black, $orange, $yellow, $purpleEyes, $blackEyes, $orangeEyes, $yellowEyes, $yellowMouth } = els;
    
    const pp = calcPos($purple);
    const bp = calcPos($black);
    const op = calcPos($orange);
    const yp = calcPos($yellow);
    
    const isHiding = passwordLen > 0 && !showPassword;
    const isShowingPw = passwordLen > 0 && showPassword;

    // Purple character
    if (isShowingPw) {
      $purple.style.transform = "skewX(0deg)";
      $purple.style.height = "400px";
    } else if (isTyping || isHiding) {
      $purple.style.transform = `skewX(${(pp.bodySkew || 0) - 12}deg) translateX(40px)`;
      $purple.style.height = "440px";
    } else {
      $purple.style.transform = `skewX(${pp.bodySkew || 0}deg)`;
      $purple.style.height = "400px";
    }

    const purpleEyeL = $purpleEyes.children[0];
    const purpleEyeR = $purpleEyes.children[1];
    purpleEyeL.style.height = purpleBlink ? "2px" : "18px";
    purpleEyeR.style.height = purpleBlink ? "2px" : "18px";
    
    let pfx, pfy;
    if (isShowingPw) {
      $purpleEyes.style.left = "20px";
      $purpleEyes.style.top = "35px";
      pfx = purplePeeking ? 4 : -4;
      pfy = purplePeeking ? 5 : -4;
    } else if (lookingAtEachOther) {
      $purpleEyes.style.left = "55px";
      $purpleEyes.style.top = "65px";
      pfx = 3;
      pfy = 4;
    } else {
      $purpleEyes.style.left = 45 + pp.faceX + "px";
      $purpleEyes.style.top = 40 + pp.faceY + "px";
      pfx = undefined;
      pfy = undefined;
    }
    setPupil(purpleEyeL, 5, pfx, pfy);
    setPupil(purpleEyeR, 5, pfx, pfy);

    // Black character
    if (isShowingPw) {
      $black.style.transform = "skewX(0deg)";
    } else if (lookingAtEachOther) {
      $black.style.transform = `skewX(${(bp.bodySkew || 0) * 1.5 + 10}deg) translateX(20px)`;
    } else if (isTyping || isHiding) {
      $black.style.transform = `skewX(${(bp.bodySkew || 0) * 1.5}deg)`;
    } else {
      $black.style.transform = `skewX(${bp.bodySkew || 0}deg)`;
    }

    const blackEyeL = $blackEyes.children[0];
    const blackEyeR = $blackEyes.children[1];
    blackEyeL.style.height = blackBlink ? "2px" : "16px";
    blackEyeR.style.height = blackBlink ? "2px" : "16px";
    
    let bfx, bfy;
    if (isShowingPw) {
      $blackEyes.style.left = "10px";
      $blackEyes.style.top = "28px";
      bfx = -4;
      bfy = -4;
    } else if (lookingAtEachOther) {
      $blackEyes.style.left = "32px";
      $blackEyes.style.top = "12px";
      bfx = 0;
      bfy = -4;
    } else {
      $blackEyes.style.left = 26 + bp.faceX + "px";
      $blackEyes.style.top = 32 + bp.faceY + "px";
      bfx = undefined;
      bfy = undefined;
    }
    setPupil(blackEyeL, 4, bfx, bfy);
    setPupil(blackEyeR, 4, bfx, bfy);

    // Orange character
    $orange.style.transform = isShowingPw
      ? "skewX(0deg)"
      : `skewX(${op.bodySkew || 0}deg)`;
    
    let ofx, ofy;
    if (isShowingPw) {
      $orangeEyes.style.left = "50px";
      $orangeEyes.style.top = "85px";
      ofx = -5;
      ofy = -4;
    } else {
      $orangeEyes.style.left = 82 + (op.faceX || 0) + "px";
      $orangeEyes.style.top = 90 + (op.faceY || 0) + "px";
      ofx = undefined;
      ofy = undefined;
    }
    setPupilOnly($orangeEyes.children[0], 5, ofx, ofy);
    setPupilOnly($orangeEyes.children[1], 5, ofx, ofy);

    // Yellow character
    $yellow.style.transform = isShowingPw
      ? "skewX(0deg)"
      : `skewX(${yp.bodySkew || 0}deg)`;
    
    let yfx, yfy;
    if (isShowingPw) {
      $yellowEyes.style.left = "20px";
      $yellowEyes.style.top = "35px";
      $yellowMouth.style.left = "10px";
      $yellowMouth.style.top = "88px";
      yfx = -5;
      yfy = -4;
    } else {
      $yellowEyes.style.left = 52 + (yp.faceX || 0) + "px";
      $yellowEyes.style.top = 40 + (yp.faceY || 0) + "px";
      $yellowMouth.style.left = 40 + (yp.faceX || 0) + "px";
      $yellowMouth.style.top = 88 + (yp.faceY || 0) + "px";
      yfx = undefined;
      yfy = undefined;
    }
    setPupilOnly($yellowEyes.children[0], 5, yfx, yfy);
    setPupilOnly($yellowEyes.children[1], 5, yfx, yfy);

    requestAnimationFrame(() => render(els));
  }

  function setPupil(eyeEl, maxDist, forceX, forceY) {
    const pupil = eyeEl.querySelector(".pupil");
    if (!pupil) return;
    const o = eyePupilOffset(eyeEl, maxDist, forceX, forceY);
    pupil.style.transform = `translate(${o.x}px, ${o.y}px)`;
  }

  function setPupilOnly(el, maxDist, forceX, forceY) {
    const o = eyePupilOffset(el, maxDist, forceX, forceY);
    el.style.transform = `translate(${o.x}px, ${o.y}px)`;
  }

  // DOM加载完成后初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
