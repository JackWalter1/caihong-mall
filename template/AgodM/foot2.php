
<script src="./assets/Agod/vue.min.js"></script>
<script src="./assets/Agod/axios.min.js"></script>

<script>
      var hashsalt = <?php echo $addsalt_js ?>;
        const app = new Vue({
          el: '#app2',
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
            const _URL = new URL(window.location.href)
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
            }
          }
        })
      </script>
</script>