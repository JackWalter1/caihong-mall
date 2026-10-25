<?php
$idcid = isset($conf['indexClass_ID']) && $conf['indexClass_ID'] != '' ? $conf['indexClass_ID'] : '1';
$shop_cid = isset($_GET['shop_cid']) && $_GET['shop_cid'] != '' ? $_GET['shop_cid'] : $idcid;
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
<div id="app">

<section  class="category">
    <div class="category_title d-flex align-content-center">
        <span>选择分类</span>
    </div>
  <div class="category_list" style="">

    <?php foreach($shua_class as $cid=>$classname){?>

    <div  onclick="window.location.href = './?shop_cid=<?php echo $cid?>'"
      style="cursor:pointer; width:45%" class="category_box tppp " data-cateid="<?php echo $cid?>">
      <img src="./assets/Agod/fenleixuan.png">
      <div class="cate_name">
        <?php echo $classname?>
      </div>
      <div class="cate_desc">自动发货</div>
    </div>
    <?php }?>

  </div>

</section>

<section class="goods">
  <div class="goods_title d-flex align-content-center">
    <span>选择商品</span>
  </div>

  <div
    class="goods_list"
    style="max-height: 380px; overflow-x: auto"
  >
      
      
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
    <div @click="getGoodsInfo('. $res['tid'].')"
      class="goods_box "
      style="cursor: pointer"
      data-goods_id="45992"
    >
      <img
        src="./assets/Agod/shangpinxuan.png"
        class="icon"
      />
      <div></div>
      <div class="goods_name">' . $res['name'] . '</div>
    <div class="goods_price">￥' . $price . '</div>
      <div
        class="goods_remark"
        style="color: #0db26a; background: #e1fff2"
      >
        <span>' . $status . '</span>
      </div>
    </div>
';
}
?>
    </div>

</section>




            <section class="desc">

                <div class="sale_message" id="isdiscount_span" style="display: none;">
                    <svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="18" height="18"><path d="M988.908308 614.006154c-58.525538-29.538462-76.091077-53.208615-76.091077-100.509539 0-44.386462 17.565538-68.017231 76.091077-100.548923 32.177231-20.716308 35.091692-38.439385 35.091692-68.01723v-168.566154C1024 123.116308 980.125538 78.769231 927.468308 78.769231H96.492308C43.874462 78.769231 0 123.116308 0 176.364308v168.566154c0 26.584615 2.914462 50.254769 35.091692 68.01723 23.433846 11.815385 76.091077 41.353846 76.091077 100.548923 0 65.024-38.045538 85.740308-73.137231 97.555693H35.052308C2.914462 631.768615 0 664.300308 0 679.069538v168.566154C0 900.883692 43.874462 945.230769 96.531692 945.230769H927.507692C980.125538 945.230769 1024 900.883692 1024 847.635692v-168.566154c0-32.531692-11.697231-44.347077-35.091692-65.063384z" fill="#7FBCFF" p-id="61311"></path><path d="M670.444308 530.116923c17.723077 0 32.571077 14.572308 32.571077 32.019692a32.571077 32.571077 0 0 1-32.571077 31.980308h-124.376616v122.171077a32.571077 32.571077 0 0 1-65.142154 0v-122.171077H356.548923a32.571077 32.571077 0 0 1-32.571077-31.980308c0-17.447385 14.808615-32.019692 32.571077-32.019692h124.376615v-75.618461H347.648A32.571077 32.571077 0 0 1 315.076923 422.478769c0-17.447385 14.808615-31.980308 32.571077-31.980307h97.713231L341.740308 288.689231a31.232 31.232 0 0 1 0-43.638154 32.610462 32.610462 0 0 1 44.425846 0l127.330461 125.085538 127.330462-125.085538a32.610462 32.610462 0 0 1 44.386461 0c11.854769 11.618462 11.854769 31.980308 0 43.638154l-103.620923 101.809231h94.759385c17.762462 0 32.571077 14.572308 32.571077 31.980307a32.571077 32.571077 0 0 1-32.571077 32.019693h-133.277538v75.618461h127.369846z" fill="#007AFF" p-id="61312"></path></svg>
                    <div class="content sale_message">
                        <span></span>
                    </div>
                </div>
                <div class="desc_content">
                    <svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="18" height="18"><path d="M392.7 958.9c22.5 0 40.7-18.2 40.7-40.7V630.9c0-22.5-18.2-40.7-40.7-40.7H105.4c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7h287.3zM860 958.9c22.5 0 40.7-18.2 40.7-40.7V630.9c0-22.5-18.2-40.7-40.7-40.7H572.7c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7H860zM392.7 492c22.5 0 40.7-18.2 40.7-40.7V164c0-22.5-18.2-40.7-40.7-40.7H105.4c-22.5 0-40.7 18.2-40.7 40.7v287.4c0 22.5 18.2 40.7 40.7 40.7h287.3z" fill="#1E94EE" p-id="42427"></path><path d="M948.3 336.4c15.9-15.9 15.9-41.6 0-57.5L745.1 75.7c-15.9-15.9-41.6-15.9-57.5 0L484.4 278.9c-15.9 15.9-15.9 41.6 0 57.5l203.2 203.2c15.9 15.9 41.6 15.9 57.5 0l203.2-203.2z" fill="#B4DCF5" p-id="42428"></path></svg>
                    <div class="content" v-html="info.desc ? info.desc:'请选择您需要的商品'" style="word-break: break-all;" id="remark"><p><br></p>
                    </div></div>
            </section>


            <section class="order_info">

                <div class="info_box">
                    <div class="info_left">
                        <span>*</span> 
                        <span>购买数量</span> 
                    </div> 
                    <div class="count_right">
                        <span @click="countoper('reduce')"  style="cursor:pointer"><p></p></span> 
                        <input v-model="num_val"  data-max="" id="num" type="text" value="1"  name="quantity" value="1"  title="本商品最少购买数量为1件！"> 
                        <span  @click="countoper('add')" style="cursor:pointer"><p></p> <p></p></span>
                    </div>
                </div>


                <div class="info_box">
                    <div class="info_left">
                        <span>*</span> 
                        <span>{{ info.input || '联系方式:(填写邮箱)' }}</span> 
                    </div> 
                    <div class="input_right opt">
<input id="inputvalue" name="contact" class="phone_num" type="text" :placeholder="'[必填]' + (info.input || '推荐填写邮箱')" required="" style="
float: left !important;
    padding: 10px;
    margin-left: 10px;
    border: 1px solid;
    border-radius: 5px;
">
                    </div>
                    <div class="info_box_msg">
                        联系方式特别重要,可用来查询订单
                    </div>
                </div>
             
                <style>
                    .order_info .info_box .opt {
                        
                     display: inline-block !important;
                     margin-right: 0.213333rem;
                     width: 5.5rem;
                      margin-top: 0.15rem;
                     padding-bottom: 0.15rem;
                     padding: 10px,5px,10px,5px !important;
                        }
                </style>
                
                <!-- 动态显示商品设置的输入框 - 博客地址：zhonguo.ren QQ群：915043052 -->
                <div v-for="(input, index) in inputs" :key="index" class="info_box">
                    <div class="info_left">
                        <span>*</span> 
                        <span>{{ input }}</span> 
                    </div> 
                    <div class="input_right opt">
<input :id="'inputvalue' + (index + 2)" :name="'inputvalue' + (index + 2)" class="phone_num" type="text" :placeholder="'[必填]' + input" required="" style="
float: left !important;
    padding: 10px;
    margin-left: 10px;
    border: 1px solid;
    border-radius: 5px;
">
                    </div>
                    <div class="info_box_msg">
                        请填写{{ input }}
                    </div>
                </div>
            </section>

            <section class="xh_box" style="display:none">
                <div style="color: #545454;margin-bottom: .2rem;font-size: .373333rem;font-weight: 700;">
                    已选号码<a style="font-size: 12px;float: right;border: 1px solid rgb(51, 105, 255);border-radius: 5px;padding: 2px 6px;color: rgb(51, 105, 255);" href="javascript:void(0)" onclick="selectForm()">重新选择</a>
                </div>
                <div class="xh_box_content"><p style="color: #74788d;padding:10px">未指定号码，系统将随机发货！</p></div>
            </section>

            <div class="border"></div>

            <section class="pay">
                <div class="pay_title">
                    支付方式
                </div>

                <div class="pay_list">
                                        <div style="cursor:pointer" class="pay_box  active" data-pid="2" data-rate="0.04">
                        <div>
                            <span>在线支付</span> 
                            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAwCAMAAABdTmjTAAABU1BMVEUzaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf8zaf////9hi//b5f/d5v/z9v81av84bf9Acv9Bc/9Edf9Jef9Me/9NfP9Wg/9Xg/81a/9rkv9tlP9ulf92mv99oP9/of+Gpv+Nq/+Qrf+UsP+fuP+huv+uxP+vxP+yx//B0f/C0v/J2P/M2f83bP8+cf/i6v/t8v/u8//x9P88b//5+v/6+//8/f/9/f/+/v89cf+QnoDYAAAAQHRSTlMAAQIDBgoMDxMVFhwfIiQsLS83PkJDTlFUW2Nma3Z4f4KHiJSWmZykq66xtLy9wcPEyNDS09vg4+rs8PP1+fz+e7c8kwAAATpJREFUeF6V0ldPAlEYhGGRRcQu9t7FrqgIImUO1d672Hv3/1cP0Q3Z8EGG9/q5mkxZCblKsJWzvHV4g7Q1pkBj+xhobBsBjwfA4x7wuDPK47YIaNwcBo3dK6BxfQA0rvGDxi4faOycB40dM6CxMQEa2zzg8RB43Aced4PHHVEet4ZB46YQaNwYBI3rAhaTXi2Cq5dg6TKxVRC7Fq32VKnnWAFc4bXa/S+l1LGMjWmr3U5qm4GI7eOmekS29Sdtb3ZFbBs17cHrHoD0vbbxH4h40LQ7SfV5Alxp+7IJEffC7FbpMmfafsQg4q7cedbu1H9HEHF7BLkerv/sBUTcEoal8+/sECkRu/OOdviu4m+QcMMy8oolNiDhWj+EUpBwlQ9iEnYugCmUtY45cGlrTILG5R7weBg87gffL8pDCUuj0TqxAAAAAElFTkSuQmCC" class="icon">
                        </div>
                    </div>

                                    </div>
            </section>

            <section class="copyright">
                <p>Copyright © JFmao.art <?php echo date("Y")?> </p>
            </section>

            <footer class="footer">
                <div class="to_pay" @click="PayGoods()" id="check_pay">
                    <span><span>立即支付￥{{total || '请选择商品' }}</span>
       
                    </span> 
                </div>
            </footer>
                
</div>