<?php
if(checkmobile() && file_exists(TEMPLATE_ROOT.'Agod/mindex.php')){include TEMPLATE_ROOT.'Agod/mindex.php';exit;}
include 'head.php';
include 'nav.php';
if ($islogin2 == 1) {
  $price_obj = new \lib\Price($userrow['zid'], $userrow);
} elseif ($is_fenzhan == true) {
  $price_obj = new \lib\Price($siterow['zid'], $siterow);
} else {
  $price_obj = new \lib\Price(1);
}
$classhide = explode(',', $siterow['class']);
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$shua_class = array();
while ($res = $rs->fetch()) {
  if ($is_fenzhan && in_array($res['cid'], $classhide)) continue;
  $shua_class[$res['cid']] = $res['name'];
}
?>

<section class="section details__section section--first  section--last">
  <div class="container">
    <div class="row category">
      <div class="col-12">
        <div class="section__title-wrap">
          <h2 class="section__title">商品分类    
</h2>
        </div>
      </div>
      <!-- content wrap -->
      <div class="col-12 col-lg-12">
        <input name="cateid" id="cateid" type="hidden" value="8461">
        <div class="row category_list">
          <?php foreach($shua_class as $cid=>$classname){?>
          <div onclick="window.location.href = './?shop_cid=<?php echo $cid?>'"
            class="category_box col-auto cursor-pointer " data-cateid="<?php echo $cid?>">
            <div class="card">
              <div class="card__title pb-0">
                <h3>
                  <?php echo $classname?>
                </h3>
              </div>
              <img class="lite_img" src="./assets/Agod/cid.png">
            </div>
          </div>
          <?php }?>
        </div>
      </div>
      <!-- end content wrap -->
    </div>
    <hr class="mt-4">

    <div class="row goods">
      <div class="col-12">
        <div class="section__title-wrap">
          <h2 class="section__title section__title--pre">选择商品</h2>
        </div>
      </div>
      
      <div class="col-12 col-lg-12">
        
        <div class="row goods_list" style="max-height: 420px;overflow-x: auto;">
<?php
$idcid = isset($conf['indexClass_ID']) && $conf['indexClass_ID'] != '' ? $conf['indexClass_ID'] : '1';
$shop_cid = isset($_GET['shop_cid']) && $_GET['shop_cid'] != '' ? $_GET['shop_cid'] : $idcid;
$rs2 = $DB->query("SELECT * FROM pre_tools WHERE cid='$shop_cid' and active=1 order by sort asc");
while ($res = $rs2->fetch()) {
  if (isset($price_obj)) {
    $price_obj->setToolInfo($res['tid'], $res);
    if ($price_obj->getToolDel($res['tid']) == 1) continue;
    $price = $price_obj->getToolPrice($res['tid']);
  } else $price = $res['price'];
  if ($res['is_curl'] == 4) {
    $count = $DB->getColumn("SELECT count(*) FROM pre_faka WHERE tid='{$res['tid']}' and orderid=0");
    if ($count > 0 && (isset($conf['faka_showleft']) && $conf['faka_showleft'] == 0)) $status = '' . $count . '个';
    elseif ($count > 0) $status = '充足';
    else $status = '缺货';
  } elseif ($res['stock'] !== null) {
    $count = $res['stock'];
    if ($count > 0 && (isset($conf['faka_showleft']) && $conf['faka_showleft'] == 0)) $status = '库存' . $count . '张</font>';
    elseif ($count > 0) $status = '充足';
    else $status = '缺货';
  } else {
    if ($res['close'] == 1) $status = '已下架';
    else $status = '正常';
  }
  if ($res['is_curl'] == 1 || $res['is_curl'] == 2 || $res['is_curl'] == 4 || $res['is_curl'] == 5) {
    $isauto = true;
  } else {
    $isauto = false;
  }
  echo '
            <div class="goods_box col-6 col-sm-6 col-xl-3 cursor-pointer " data-goods_id="45992">
            <div @click="getGoodsInfo('. $res['tid'].')" class="card ">
              <div class="row">
                <div class="col-md-12">
                  <div class="card__wrap">
                    <div class="card__detail">
                      <h3>' . $res['name'] . '</h3>
  
                    </div>
                    <div class="card__detail"><span class="card__detail_price">￥' . $price . '</span></div>
                                        <div class="card__detail"><span class="card__detail_stock"><span>' . $status . '</span></span></div>
                  </div>
                </div><img class="lite_img"
                  src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAVCAYAAABc6S4mAAABo0lEQVQ4jbXUP0gcURDH8TOgTbAQi3QK1hbCvNPgn0KEQBqxUAK2aZzZiyKKIoJuSGmKIIiIjQqCRZprBCEWSSESAiEQSMCkUJRACkEL/6H3tVgip7e3t56bhR8sPN6HeTNvN5X6Dw9QIcbbPp+qxHFZoNIpK6LQrtQkij8b5bFT1kVBFGSIusTwZo9aMbZvcAU3SGMieKtHvRg/8nFREI+WB+NukEZR9gtwhbRH14NwMdpFOQzDRcFl6C6/8gzdzjgphouCZOgvDzdeOuUyEg9moOXgk6LkSuIKaWM0Nuz7PHIes3HgmxkYfiy8z6fKGWv3wUVBjJmSeNsY1c74EFEl2S2YXg5dm4/EW4Z4IsqXqCrnsgDw/lPo+mpRPK00iLFzd9OLN+DNBu/ji5DLweef8PRVyAmUbDhuNDnjT1jFX3/B5RUsbcDpOez9hc6RoifcLMDF6HTGUbGWPJ+AnYOgLccn0DMVOeTtW7gzesU4K3U7Oobh4zcYeFfymn7Pb4vF+jrvl92gLR6vE4b/5TAlyrhTft+KsSfBX7IwMVqYl4tr5HDZyKPmZr8AAAAASUVORK5CYII="
                  alt="">
              </div>
            </div>
          </div>
';
}
?>

        </div>
      </div>

    </div>


    <hr class="mt-4">
    <div class="row">
      <div class="col-12  mt-2">
        <div class="d-flex mt-0 align-items-center">
          <svg t="1600050312195" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"
            p-id="42426" width="18" height="18">
            <path
              d="M392.7 958.9c22.5 0 40.7-18.2 40.7-40.7V630.9c0-22.5-18.2-40.7-40.7-40.7H105.4c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7h287.3zM860 958.9c22.5 0 40.7-18.2 40.7-40.7V630.9c0-22.5-18.2-40.7-40.7-40.7H572.7c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7H860zM392.7 492c22.5 0 40.7-18.2 40.7-40.7V164c0-22.5-18.2-40.7-40.7-40.7H105.4c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7h287.3z"
              fill="#1E94EE" p-id="42427"></path>
            <path
              d="M948.3 336.4c15.9-15.9 15.9-41.6 0-57.5L745.1 75.7c-15.9-15.9-41.6-15.9-57.5 0L484.4 278.9c-15.9 15.9-15.9 41.6 0 57.5l203.2 203.2c15.9 15.9 41.6 15.9 57.5 0l203.2-203.2z"
              fill="#B4DCF5" p-id="42428"></path>
          </svg>
          <h2 class="section__title_2 mb-0 ml-2">
            商品描述
          </h2>
        </div>
        <div class="text_box mt-3" style="word-break: break-all;" id="remark">
          <p v-html="info.desc ? info.desc:'请选择您需要的商品'" id="_dec"><br></p>
        </div>
      </div>

    </div>

    <hr class="mt-4">

    <div class="row mt-4">
      <div class="col-12" id="order_box">
        <div class="ure_info_box">
          <div class="ure_info_hide">
            <span>填写信息/购买方式</span>
          </div>
          <div class="ure_info" id="inputsname">
              
            <div class="ure_info_input_box">
              <div class="ure_info_input_box_hide">
                <p>*</p>
                <p> 联系方式:(填写邮箱) </p>
              </div>
              <div class="input">
                <input id="inputvalue" name="contact" class="phone_num" type="text" placeholder="[必填]推荐填写邮箱" required="">
              </div>
              <div class="msg">
                联系邮箱特别重要,可用来查询订单
              </div>
            </div>
            
          </div>
          <div class="pay_type">
            <div class="pay_type_hide">
              选择支付方式
            </div>
            <div class="pay_type_box">
              <div class="pay_type_leng pay_type_leng_xz" data-pid="1" data-rate="0.04">
                <span>在线支付</span>
                <img class="xiadui"
                  src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAVCAYAAABc6S4mAAABo0lEQVQ4jbXUP0gcURDH8TOgTbAQi3QK1hbCvNPgn0KEQBqxUAK2aZzZiyKKIoJuSGmKIIiIjQqCRZprBCEWSSESAiEQSMCkUJRACkEL/6H3tVgip7e3t56bhR8sPN6HeTNvN5X6Dw9QIcbbPp+qxHFZoNIpK6LQrtQkij8b5bFT1kVBFGSIusTwZo9aMbZvcAU3SGMieKtHvRg/8nFREI+WB+NukEZR9gtwhbRH14NwMdpFOQzDRcFl6C6/8gzdzjgphouCZOgvDzdeOuUyEg9moOXgk6LkSuIKaWM0Nuz7PHIes3HgmxkYfiy8z6fKGWv3wUVBjJmSeNsY1c74EFEl2S2YXg5dm4/EW4Z4IsqXqCrnsgDw/lPo+mpRPK00iLFzd9OLN+DNBu/ji5DLweef8PRVyAmUbDhuNDnjT1jFX3/B5RUsbcDpOez9hc6RoifcLMDF6HTGUbGWPJ+AnYOgLccn0DMVOeTtW7gzesU4K3U7Oobh4zcYeFfymn7Pb4vF+jrvl92gLR6vE4b/5TAlyrhTft+KsSfBX7IwMVqYl4tr5HDZyKPmZr8AAAAASUVORK5CYII="
                  alt="">
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>


  </div>


</section>


<footer>
  <div class="container">
    <p>Copyright © 2019-<?php echo date('Y')?></p>
  </div>
</footer>


<section class="section_buttom">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <div class="goods_name">
        <svg class="icon" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
          <path
            d="M847.872 734.208L784.384 368.64H239.616l-63.488 365.568c-9.216 51.2 30.72 105.472 81.92 105.472h506.88c52.224 0 92.16-54.272 82.944-105.472zM512 648.192c-71.68 0-130.048-58.368-130.048-130.048 0-6.144 4.096-10.24 10.24-10.24s10.24 4.096 10.24 10.24c0 60.416 49.152 109.568 109.568 109.568s109.568-49.152 109.568-109.568c0-6.144 4.096-10.24 10.24-10.24s10.24 4.096 10.24 10.24c0 71.68-58.368 130.048-130.048 130.048z"
            fill="#2F8AF5"></path>
          <path
            d="M791.552 358.4l-19.456-96.256c-7.168-39.936-46.08-67.584-87.04-67.584H340.992c-40.96 0-74.752 28.672-81.92 68.608L243.712 358.4h547.84z"
            fill="#83C1FF"></path>
        </svg>
        <span id="_shop_name">{{info.name ? info.name:'未选商品'}}</span>
      </div>

      <div class="shuliang_box d-flex align-items-center">
        <div @click="countoper('reduce')" class="btn">
          <span style="background: rgb(153, 153, 153) none repeat scroll 0% 0%;"></span>
        </div>
        <div class="input">
          <input v-model="num_val"  id="num" name="num" type="number" id="num"  value="1" title="本商品最少购买数量为1件！">
        </div>
        <div @click="countoper('add')" class="btn">
          <span></span>
          <span></span>
        </div>

        <div class="jiage d-flex align-items-center">
          <span>支付金额 : </span>
          <span id='_Pic'>￥{{total || '请选择商品' }}</span>
          <span></span>
        </div>
      </div>


      <div class="queding_btn">
        <button name="check_pay" class="check_pay"  @click="PayGoods()" id="check_pay">确认支付</button>
      </div>

    </div>

  </div>
</section>


<!--<div class="ewm">-->
<!--  <div id="qrcode" style="width: 150px; height: 150px" title="">-->
<!--    <canvas width="150" height="150" style="display: none"></canvas>-->
<!--  </div>-->
<!--  <br />-->
<!--  客服微信-->
<!--</div>-->
</div>
<script src="./assets/Agod/qrcode.min.js"></script>
<script src="./assets/Agod/app.js"></script>

<script>
var hashsalt = <?php echo $addsalt_js ?>;
//默认点亮第N个ID
var active_ClassID = <?php echo $shop_cid?>;
// var qrcode = new QRCode(document.getElementById('qrcode'), {
//     text: window.location.href,
//     width: 150,
//     height: 150,
//     colorDark: '#000000',
//     colorLight: '#ffffff',
//     correctLevel: QRCode.CorrectLevel.H
//   })
  getGoodsPro(active_ClassID);
</script>
<?php
include 'foot.php';
?>