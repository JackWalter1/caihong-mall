<html lang="zh">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no" />
  <meta name="format-detection" content="telephone=no" />
  <meta name="csrf-param" content="_csrf" />
  <title>
    <?php echo $hometitle?>
  </title>
  <meta name="keywords" content="<?php echo $conf['keywords'] ?>" />
  <meta name="description" content="<?php echo $conf['description'] ?>" />

  <!--css-->
        <!-- CSS -->
        <link rel="stylesheet" href="./assets/Agod/bootstrap-reboot.min.css">
        <link rel="stylesheet" href="./assets/Agod/bootstrap-grid.css">
        <link rel="stylesheet" href="./assets/Agod/pc_main.css">

  <!--依赖js-->
  <script src="./assets/Agod/axios.min.js"></script>
  <script src="./assets/Agod/vue.min.js"></script>
  <script src="./assets/Agod/jquery.js"></script>
  <script src="./assets/Agod/nyro.js"></script>
  <script src="./assets/Agod/bootstrap.js"></script>
  <script src="./assets/Agod/layer.js"></script>
  <script src="./assets/Agod/jquery.cookie.min.js"></script>
  <!--<?php echo $background_css;?>-->
</head>
<body>
<div id="app">
<style>
    <?php echo isset($conf['userStyle']) ? $conf['userStyle'] : '';?>
</style>