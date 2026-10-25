(function () {
    'use strict';
    var select = document.getElementById('tid');
    var buy = document.getElementById('submit_buy');
    if (!select || !buy || document.getElementById('ranger-product-video')) return;
    var anchor = buy.closest('.form-group');
    if (!anchor) return;
    var base = '/s/assets/media/ranger-177-20260909';
    var panel, toggle, body, video, error, ready = false;

    function stop() {
        if (!video) return;
        var old = video;
        video = null;
        old.pause();
        old.removeAttribute('src');
        old.load();
        old.remove();
    }
    function close(returnFocus) {
        stop();
        if (!panel) return;
        body.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        error.hidden = true;
        if (returnFocus) toggle.focus();
    }
    function hide() {
        close(false);
        if (panel) panel.hidden = true;
    }
    function open() {
        if (select.value !== '177') { hide(); return; }
        if (!body.hidden) { close(true); return; }
        error.hidden = true;
        body.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');
        video = document.createElement('video');
        video.id = 'ranger-video-player';
        video.controls = true;
        video.preload = 'none';
        video.setAttribute('playsinline', '');
        video.setAttribute('webkit-playsinline', '');
        video.setAttribute('aria-label', 'Ranger 实战演示');
        video.poster = base + '.jpg';
        var current = video;
        current.addEventListener('error', function () {
            if (video === current) error.hidden = false;
        });
        body.insertBefore(current, error);
        // Attach the media URL only in the user's playback click handler.
        current.src = base + '.mp4';
        var playing = current.play();
        if (playing && playing.catch) playing.catch(function () {
            // Native controls remain available if the browser requires another tap.
            if (video === current && current.error) error.hidden = false;
        });
    }
    function create() {
        panel = document.createElement('div');
        panel.id = 'ranger-product-video';
        panel.hidden = true;
        panel.innerHTML = '<button id="ranger-video-toggle" type="button" aria-expanded="false" aria-controls="ranger-video-body">' +
            '<img width="96" height="54" alt="" loading="lazy" decoding="async">' +
            '<span class="ranger-video-label"><i class="fa fa-play-circle" aria-hidden="true"></i> Ranger 实战演示</span>' +
            '<span class="ranger-video-time">00:28</span></button>' +
            '<div id="ranger-video-body" hidden><div class="ranger-video-toolbar"><button id="ranger-video-close" type="button" aria-label="收起视频" title="收起视频"><i class="fa fa-times" aria-hidden="true"></i><span>收起</span></button></div>' +
            '<p id="ranger-video-error" role="status" hidden>视频暂时无法播放，请稍后重试。</p></div>';
        toggle = panel.querySelector('#ranger-video-toggle');
        body = panel.querySelector('#ranger-video-body');
        error = panel.querySelector('#ranger-video-error');
        toggle.addEventListener('click', open);
        panel.querySelector('#ranger-video-close').addEventListener('click', function () { close(true); });
        panel.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !body.hidden) { event.preventDefault(); close(true); }
        });
        panel.querySelector('img').src = base + '.jpg';
        anchor.insertAdjacentElement('afterend', panel);
        var css = document.createElement('link');
        css.rel = 'stylesheet';
        css.href = '/s/assets/css/ranger-video-20260909.css';
        css.onload = function () { ready = true; sync(); };
        document.head.appendChild(css);
    }
    function sync() {
        if (select.value !== '177') { hide(); return; }
        if (!panel) create();
        panel.hidden = !ready;
    }
    select.addEventListener('change', sync);
    var category = document.getElementById('cid');
    if (category) category.addEventListener('change', hide);
    // Existing jQuery code refills options and sets .val() without a DOM change event.
    if (window.MutationObserver) {
        new MutationObserver(function () { close(false); sync(); }).observe(select, {
            childList: true, subtree: true, attributes: true, attributeFilter: ['selected']
        });
        // Bootstrap and existing category code may hide the purchase area without a click.
        var visibility = new MutationObserver(function () {
            if (video && (!anchor.getClientRects().length || getComputedStyle(anchor).visibility === 'hidden')) close(false);
        });
        for (var parent = anchor.parentElement; parent; parent = parent.parentElement) {
            visibility.observe(parent, { attributes: true, attributeFilter: ['class', 'style', 'hidden'] });
        }
    }
    document.addEventListener('click', function (event) {
        if (event.target.closest && event.target.closest('.nav-tabs, .backType, .goodTypeChange')) close(false);
    });
    window.addEventListener('pagehide', function () { close(false); });
    document.addEventListener('visibilitychange', function () {
        if (document.hidden && video) video.pause();
    });
    sync();
}());
