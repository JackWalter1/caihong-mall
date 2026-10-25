<style>
.notice-mask {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0, 0, 0, 0.5) !important;
    z-index: 999999 !important;
    display: none !important;
    justify-content: center !important;
    align-items: center !important;
    animation: noticeFadeIn 0.3s ease;
}
.notice-mask.show {
    display: flex !important;
}
.notice-mask.show {
    display: flex;
}
@keyframes noticeFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes noticeSlideUp {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.mark_notice {
    width: 400px;
    max-width: 92vw;
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: noticeSlideUp 0.4s ease;
}
.mark_notice_top {
    background: #106be6;
    padding: 30px 20px 20px;
    text-align: center;
    position: relative;
}
.mark_notice_top .title {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
}
.mark_notice_top .title .t {
    color: #fff;
}
.mark_notice_top .subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 13px;
    display: flex;
    justify-content: center;
    gap: 16px;
}
.mark_notice_top .subtitle b {
    color: rgba(255, 255, 255, 0.6);
    margin-right: 4px;
}
.mark_notice_top .close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 24px;
    height: 24px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}
.mark_notice_top .close:hover {
    opacity: 1;
}
.mark_notice_bottom {
    padding: 16px;
    max-height: 50vh;
    overflow-y: auto;
}
.mark_notice_bottom .sh_notice p {
    margin-bottom: 8px;
    line-height: 1.7;
    font-size: 13px;
}
.mark_notice_bottom .sh_notice .tip-title {
    color: #f9963b;
    font-weight: bold;
    font-size: 14px;
}
.mark_notice_bottom .sh_notice .item-1 {
    color: #1c487f;
}
.mark_notice_bottom .sh_notice .item-2 {
    color: #8baa4a;
}
.mark_notice_bottom .sh_notice .item-3 {
    color: #000;
}
.mark_notice_bottom .sh_notice .after-title {
    color: #c24f4a;
    font-weight: bold;
    font-size: 14px;
}
.mark_notice_bottom .sh_notice .after-content {
    color: #c24f4a;
    font-size: 13px;
}
.mark_notice .exp {
    display: flex;
    padding: 0 16px 16px;
    gap: 10px;
}
.mark_notice .exp .jump {
    flex: 1;
    display: block;
    text-align: center;
    padding: 10px;
    background: #106be6;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: background 0.2s;
}
.mark_notice .exp .jump:hover {
    background: #0d5bc4;
}
.mark_notice .exp .close-btn {
    flex: 1;
    text-align: center;
    padding: 10px;
    background: #f0f0f0;
    color: #666;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
    user-select: none;
}
.mark_notice .exp .close-btn:hover {
    background: #e0e0e0;
}
@media (max-width: 480px) {
    .mark_notice {
        width: 92vw;
    }
    .mark_notice_top .title {
        font-size: 18px;
    }
}
</style>

<div class="notice-mask" id="noticeMask">
    <div class="mark_notice">
        <div class="mark_notice_top">
            <svg class="close" onclick="closeNoticeModal()" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            <div class="title"><span class="t">「7x24」</span>小时自动发货</div>
            <div class="subtitle">
                <span><b>·</b>选购商品,在线付款</span>
                <span><b>·</b>系统自动发货</span>
            </div>
        </div>
        <div class="mark_notice_bottom">
            <div class="sh_notice">
                <p><span class="tip-title">温馨提示：</span><span class="tip-title">请务必仔细阅读以下内容！</span></p>
                <p class="item-1">1、本平台仅提供寄售服务，非销售商，建议先咨询商家软件使用方法和用途后购买。</p>
                <p class="item-2">2、购买后有任何疑问请先联系商家处理，店铺和订单都有商家联系方式，如果联系不上或商家拒绝售后，再点下方申请售后，平台介入处理。</p>
                <p class="item-3">3、虚拟商品具有可复制性，商品没有问题的情况下，不支持无理由退款，请确定需要再下单。</p>
                <p><span class="after-title">售后须知：</span></p>
                <p class="after-content">所有订单资金将在平台停留24小时，超过此时间，资金将自动结算给商家，如您支付后遇到商品问题，请务必在24小时内申请售后，逾期后，平台将不再介入处理相关事务！</p>
            </div>
        </div>
        <span class="exp">
            <a class="jump" target="_blank" href="./user/workorder.php">申请售后</a>
            <div class="close-btn" onclick="closeNoticeModal()">关闭</div>
        </span>
    </div>
</div>

<script>
function closeNoticeModal() {
    localStorage.setItem('caihong_notice_closed', Date.now().toString());
    var mask = document.getElementById('noticeMask');
    if (mask) {
        mask.classList.remove('show');
        setTimeout(function(){ mask.style.display = 'none'; }, 300);
    }
}
window.addEventListener('load', function() {
    var NOTICE_KEY = 'caihong_notice_closed';
    var COOLDOWN = 8 * 60 * 60 * 1000;
    var lastClosed = parseInt(localStorage.getItem(NOTICE_KEY) || '0', 10);
    if (Date.now() - lastClosed < COOLDOWN) return;
    setTimeout(function() {
        var mask = document.getElementById('noticeMask');
        if (mask) {
            mask.style.display = 'flex';
            mask.classList.add('show');
        }
    }, 500);
});
</script>