/* theme-toggle.js  tt2(2026-09-15,复审后)—— 顶栏「太阳/月亮」深浅色开关,前台与代理后台共用。
   由 <script src=".../theme-toggle.js" data-prefix="fp|gl" data-mount="容器选择器" defer> 配置:
     data-prefix  fp = 前台(html.fp-dark / html.fp-light),gl = 代理后台(html.gl-dark / html.gl-light)
     data-mount   把按钮插到哪个容器的最前面;容器是 <ul> 时自动包一层 <li class="tt-li">
   状态机(三态,只用一个按钮):
     无记忆      → 跟随系统(prefers-color-scheme)
     点一下      → 切到相反色并写 localStorage <prefix>_theme = dark|light
     再点回来    → 若目标色 == 系统色,清掉记忆回到「跟随系统」;否则记住目标色
   首屏防闪由页面 <head> 里的内联脚本负责(按 ?theme= / localStorage 先加 class);这里只管按钮和切换。
   tt2 改动:不再 blur() 抢走键盘焦点;过渡计时器有句柄(快速连点不硬切);跨标签页 storage 与 bfcache pageshow 时重新对齐记忆。 */
(function () {
  var sc = document.currentScript; if (!sc) return;
  var P = sc.getAttribute('data-prefix') || 'fp', KEY = P + '_theme', MOUNT = sc.getAttribute('data-mount') || '';
  var html = document.documentElement, CD = P + '-dark', CL = P + '-light', CT = P + '-theming';
  var mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme:dark)') : null;
  var btn = null, timer = 0;
  function sysDark() { return !!(mq && mq.matches); }
  function stored() { try { var v = localStorage.getItem(KEY); return (v === 'dark' || v === 'light') ? v : null; } catch (e) { return null; } }
  function eff() {
    if (html.classList.contains(CD)) return 'dark';
    if (html.classList.contains(CL)) return 'light';
    return sysDark() ? 'dark' : 'light';
  }
  function apply(m) {
    html.classList.remove(CD, CL);
    if (m === 'dark') html.classList.add(CD); else if (m === 'light') html.classList.add(CL);
  }
  function save(m) { try { if (m) localStorage.setItem(KEY, m); else localStorage.removeItem(KEY); } catch (e) {} }
  function pulse() {
    html.classList.add(CT);
    if (timer) clearTimeout(timer);
    timer = setTimeout(function () { html.classList.remove(CT); timer = 0; }, 380);
  }
  var SUN = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>';
  var MOON = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>';
  function render() {
    if (!btn) return;
    var d = eff() === 'dark', tip = d ? '切换到浅色' : '切换到深色';
    btn.innerHTML = d ? SUN : MOON;
    btn.title = tip; btn.setAttribute('aria-label', tip); btn.setAttribute('aria-pressed', d ? 'true' : 'false'); btn.setAttribute('data-mode', d ? 'dark' : 'light');
  }
  function toggle() {
    var next = eff() === 'dark' ? 'light' : 'dark', sys = sysDark() ? 'dark' : 'light';
    pulse();
    if (next === sys) { apply(null); save(null); } else { apply(next); save(next); }
    render();
  }
  /* 记忆在别处变了(另一个标签页点了开关 / bfcache 回退):按记忆重新对齐,没有记忆就回到跟随系统 */
  function resync() {
    var m = stored();
    if (m !== (html.classList.contains(CD) ? 'dark' : html.classList.contains(CL) ? 'light' : null)) { pulse(); apply(m); }
    render();
  }
  function mount() {
    if (document.getElementById('theme-toggle')) return;
    var host = MOUNT ? document.querySelector(MOUNT) : null; if (!host) return;
    btn = document.createElement('button');
    btn.type = 'button'; btn.id = 'theme-toggle'; btn.className = 'tt-btn';
    btn.addEventListener('click', function (e) { e.preventDefault(); toggle(); });
    var node = btn;
    if (host.tagName === 'UL') { node = document.createElement('li'); node.className = 'tt-li'; node.appendChild(btn); }
    host.insertBefore(node, host.firstChild);
    render();
    if (mq) { var on = function () { render(); }; if (mq.addEventListener) mq.addEventListener('change', on); else if (mq.addListener) mq.addListener(on); }
    window.addEventListener('storage', function (ev) { if (!ev.key || ev.key === KEY) resync(); });
    window.addEventListener('pageshow', function (ev) { if (ev.persisted) resync(); });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount); else mount();
})();
