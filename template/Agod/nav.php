<header class="header">
  <div class="container">
    <div class="row">
      <div class="col-12 d-flex align-items-center justify-content-between">
        <div class="header_left d-flex align-items-center">
          <div class="header_logo">
            <img src="<?php echo isset($logo) ? $logo : 'assets/img/logo.png';?>" alt="LOGO" />
            <?php echo $hometitle?>
          </div>

          <div class="online-btn">
            <svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="6561"
              data-spm-anchor-id="a313x.7781069.0.i5" width="16" height="16">
              <path
                d="M877.714286 170.666667v585.142857H575.463619l-282.526476 196.851809V755.809524H146.285714V170.666667h731.428572z m-97.52381 97.523809H243.809524v390.095238h146.651428l-0.024381 107.568762L544.841143 658.285714H780.190476V268.190476z m-365.714286 121.904762a73.142857 73.142857 0 1 1 0 146.285714 73.142857 73.142857 0 0 1 0-146.285714z m195.04762 0a73.142857 73.142857 0 1 1 0 146.285714 73.142857 73.142857 0 0 1 0-146.285714z"
                p-id="6562" fill="#ffffff"></path>
            </svg><span>卖家售后：<?php echo $conf['kfqq'];?></span>
          </div>
        </div>
        <div class="header_right d-flex align-items-center">
          <!--<a class="header_button" href="./user" style="margin-right:10px">-->
          <!--    <span style="">用户登录</span>-->
          <a class="header_button" href="./?mod=query">
            <svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="22"
              height="22">
              <path
                d="M455.253333 657.92c117.76 0 213.333333-95.573333 213.333334-213.333333s-95.573333-213.333333-213.333334-213.333334-213.333333 95.573333-213.333333 213.333334 95.573333 213.333333 213.333333 213.333333z m229.76-22.4l169.813334 169.813333c16.64 16.64 16.64 43.733333 0 60.373334-16.64 16.64-43.733333 16.64-60.373334 0l-172.8-172.8c-47.573333 32-104.746667 50.56-166.4 50.56-164.906667 0-298.666667-133.76-298.666666-298.666667s133.76-298.666667 298.666666-298.666667 298.666667 133.76 298.666667 298.666667c0 72.32-25.813333 138.88-68.906667 190.72z"
                fill="#ffffff"></path>
            </svg>
            <span>订单查询</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</header>