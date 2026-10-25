<!DOCTYPE html>
<html lang="en" style="font-size: 39px;" >
    
  <head>
      
    <meta charset="UTF-8" />
    <meta
      http-equiv="X-UA-Compatible"
      content="IE=edge"
    />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0"
    />
    <title><?php echo $hometitle?></title>
    <meta
      name="keywords"
      content="<?php echo $conf['keywords'] ?>"
    />
    <meta
      name="description"
      content="<?php echo $conf['description'] ?>"
    />
    <!--css依赖-->
   
            <link rel="stylesheet" href="./assets/Agod/bootstrap-reboot.min.css">
        <link rel="stylesheet" href="./assets/Agod/bootstrap-grid.css">
         <link rel="stylesheet" href="./assets/AgodM/mobile_main.css">
      <!--依赖js-->
  <script src="./assets/Agod/axios.min.js"></script>
  <script src="./assets/Agod/vue.min.js"></script>
  <script src="./assets/Agod/jquery.js"></script>
    <script src="./assets/Agod/nyro.js"></script>
  <script src="./assets/Agod/bootstrap.js"></script>
  <script src="./assets/Agod/layer.js"></script>
   <script src="./assets/Agod/jquery.cookie.min.js"></script>

  </head>
  <body>

<style>
  .bangz_boxs {
    position: fixed;
    width: auto !important;
    right: 0;
    top: 100px;
    z-index: 45;
  }
  .bangz_boxs .item {
    padding: 7px;
    background: linear-gradient(45deg, #3798f7, #3369ff);
    box-shadow: 0 0.093333rem 0.133333rem 0 rgb(54 144 248 / 23%);
    border-radius: 7px 0 0 7px;
    margin-top: 15px;
  }
  .bangz_boxs .item:first-child {
    background: linear-gradient(45deg, #fd0b27, #ff4a4a);
    box-shadow: 0 7px 10px 0 rgb(255 113 19 / 23%);
  }
  .bangz_boxs .item:nth-child(2) {
    background: linear-gradient(45deg, #f737e8, #3369ff);
    box-shadow: 0 0.093333rem 0.133333rem 0 rgb(54 144 248 / 23%);
  }
  .bangz_boxs .item a {
    text-decoration: none;
    text-align: center;
    color: #fff;
  }
  .bangz_boxs span {
    margin-left: 5px;
    font-weight: 600;
    font-size: 10px;
    color: #fff;
  }
</style>
<!--<div class="bangz_boxs">-->
<!--  <div class="item">-->
<!--    <a href="/user">-->
<!--      <span>投诉商家</span>-->
<!--    </a>-->
<!--  </div>-->
<!--  <div class="item">-->
<!--    <a-->
<!--      href="//wpa.qq.com/msgrd?v=1&amp;uin=<?php echo $conf['kfqq'];?>&amp;site=&amp;menu=yes"-->
<!--      target="_blank"-->
<!--    >-->
<!--      <span>卖家客服</span>-->
<!--    </a>-->
<!--  </div>-->
<!--</div>-->
