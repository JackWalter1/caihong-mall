<?php
if(checkmobile() && file_exists(TEMPLATE_ROOT.'Agod/mquery.php')){include TEMPLATE_ROOT.'Agod/mquery.php';exit;}
?>
<html lang="zh">
  <head>
    <meta
      http-equiv="Content-Type"
      content="text/html; charset=UTF-8"
    />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1,user-scalable=no"
    />
    <meta
      name="format-detection"
      content="telephone=no"
    />
    <meta
      name="csrf-param"
      content="_csrf"
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
   <link href="//lib.baomitu.com/tailwindcss/2.2.9/tailwind.min.css" rel="stylesheet">
    <script src="./assets/Agod/jquery-3.6.0.min.js"></script>
    <script src="./assets/Agod/axios.min.js"></script>
    <script src="./assets/Agod/vue.min.js"></script>

  </head>
  <body>
      <style>
          .red{
              color: red;
          }
      </style>
    <div id="app">
      <body class="bg-gray-50 font-mono">
        <div class="relative mx-auto rounded-md bg-white">
          <div style="background-image: url('<?php echo $cdnserver?>assets/Agod/img/pc_header.jpg');" class="py-20 text-center text-4xl text-white shadow-md">
            订单查询
            <p class="pt-5 text-lg">轻松查询订单，即刻享受咔蜜自动交易</p>
          </div>
    <div class="pt-10 pb-5 text-center text-2xl text-gray-700">
      请输入要查询的订单 <a class="text-red-400 hover:text-blue-600" href="./"><span>返回首页</span></a>
    </div>
          <div class="py-10 text-center">
            <input
              v-model="user_val"
              placeholder="请输入联系方式进行查询"
              class="rounded-3xl border py-3 px-4 text-center text-lg text-gray-700 placeholder:text-gray-400 sm:w-96"
              value=""
              type="text"
            />
            <button
              @click="Query_order(user_val)"
              class="text-md rounded-3xl bg-purple-600 py-4 px-8 text-white shadow-md"
            >
              查询订单
            </button>
          </div>
          <div class="pb-5 text-center text-xs text-gray-400">
            免责声明：卖家次日可在平台申请提现，因此如果您对订单存在疑惑，请在购买当天23:59前在网站上投诉订单，逾期请自行与卖家协商解决。
          </div>
          <div class="py-8 text-center text-xl text-gray-600">订单查询结果</div>
          <div class="mx-auto mb-10 max-w-5xl py-10 shadow">
            <div class="mx-4 grid grid-cols-1 grid-rows-3 gap-4 sm:grid-cols-3">
              <a
                v-for="(item,index) in list"
                key="index"
                :href=`?mod=kminfo&id=${item.id}&skey=${item.skey}&value=${item.value}`
              >
                <div class="rounded-md py-3 px-2 shadow-md hover:shadow-xl">
                  <div class="mt-1 flex justify-center">
                    <p
                      class="rounded-md bg-blue-600 px-2 text-center text-white"
                    >
                      {{item.id}}
                    </p>
                    <span
                      v-if="item.status == 1"
                      class="ml-2 text-sm text-green-600"
                      >已完成</span
                    >
                    <span
                      v-else-if="item.status == 2"
                      class="ml-2 text-sm text-blue-500"
                      >正在处理</span
                    >
                    <span
                      v-else-if="item.status == 3"
                      class="ml-2 text-sm text-red-700"
                      >订单异常</span
                    >
                    <span
                      v-else-if="item.status == 4"
                      class="ml-2 text-sm text-gray-400"
                      >已经退单</span
                    >
                    <span
                      v-else-if="item.status == 4"
                      class="ml-2 text-sm text-indigo-600"
                      >待处理</span
                    >
                  </div>
                  <p class="pt-2 text-center font-bold">{{item.name}}</p>
                  <p class="text-center font-extralight text-gray-400">
                   <span :class="index == 0 ? 'red':'' ">{{item.addtime}}</span>
                  </p>
                </div>
              </a>
            </div>
          </div>
        </div>
      </body>


    </div>
        <script src="./assets/Agod/jquery-3.6.0.min.js"></script>
    
    <script>
      const _URLT = new URL(location.href)

      const _SEARCHT = _URLT.search
      const _QueryT = '?mod=query&type=0&qq=&page=1'
      
    
    
    

    </script>


    <script src="./assets/Agod/layer.js"></script>
    

      <script>
        const app = new Vue({
          el: '#app',
          data: {
            list: '',
            show: false,
            msg: '',
            _Query: './?mod=query&type=0&qq=0&page=1',
            query: '',
            user_val: ''
          },
          created() {
            //得到url参数
            const _URL = new URL(location.href)
            console.log(_URL);
            // 摘取参数
            const _SEARCH = _URL.search
            const _Query = '?mod=query&type=0&qq=&page=1'
            // 判断参数是否一样
            
            if (_SEARCH == _Query) {
              // 如果一样 则请求getALLQuery
              this.Query_order('')
             
            }
          },
          methods: {
            Query_order(_Query) {
              $.ajax({
                type: 'POST',
                url: './ajax.php?act=query',
                data: {
                  type: 0,
                  qq: _Query,
                  page: 1
                },
                dataType: 'JSON',
                success: res => {
                if(res.data.length == ''){
                        layer.msg('没有此订单')
                    }
                  this.list = res.data
                }
              })
            },
          }
        })
      </script>
<?php
include 'foot.php';
?>
