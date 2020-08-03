<template>
	<div class="page-login">
		<div class="header-login">
			<h1 class="login"><span>仓库管理系统</span></h1>
			<!--<span class="header-tel">0571-87791393</span>-->
		</div>
		<div class="content">
			<div class="wraper">
				<div class="wraper-login">
					<div class="login-content">
						<div class="form-content">
							<h2>用户登录</h2>
							<!-- <p class="text-error">错误提示</p> -->
							<div class="form-group">
								<label class="label-user"><input type="text" v-model.trim="loginForm.username" @keyup.enter="handleLogin" placeholder="请输入用户名"></label>
								<span class="line"></span>
								<label class="label-password"><input type="password" v-model.trim="loginForm.password" @keyup.enter="handleLogin" placeholder="请输入密码"></label>
							</div>
							<!-- <div class="form-item">
								<label class="label-code"><input type="text" placeholder="请输入验证码"></label>
								<div>
									<span class="code-img"></span>
									<a href="javascript:void(0)">换一张</a>
								</div>
							</div> -->
							<div class="form-item"  >
								<mt-button size="large" class="btn-login"  @click="handleLogin()">登 录</mt-button>
							</div>
							<div class="form-item">
								<span><a href="javascript:void(0)">忘记密码？</a>请联系系统管理员修改密码</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="footer-login">
			<!--<h3>浙江仓海联储网络科技有限公司</h3>-->
			<!--<p>&copy;2017 优仓网ycang.cn 保留所有权利 浙ICP备16040246号-1</p>-->
		</div>
	</div>
</template>

<script>
import { indexedDBFn } from "@/utils/indexedDB";
import { getIndex }from '@/api/location'
export default {
  name: 'login',
  data() {
    return {
      loginForm: {
        username: '',
        password: '',
      }
    }
  },
  created() {
    //  let lett = this;
    //  document.onkeyup = function (event){
    //     let key = event.keyCode;
    //     if(key == 13) lett.handleLogin();
    //  }    
  },
  methods: { 
    locationALLFn (){
        // 获取所有库位
        getIndex({
          page: 1,
          limit: 100000
        }).then(response => {
          let array = [];
          if(Array.isArray(response.data.data) && response.data.data.length !== 0){
            for(let i=0,len=response.data.data.length;i<len;i++){
                let parameters = {};
                parameters["stock_no"] = response.data.data[i]["stock_no"];
                array.push(parameters);
            };
            indexedDBFn(array);
          };
        });  
    },
    handleLogin() {
      if (this.loginForm.username === '' || this.loginForm.password === '') {
        this.$notify({
          title: '提示',
          message: '用户或者密码不能为空',
          type: 'error',
          duration: 2000
        })
        return
      };
      let copyObj = JSON.parse(JSON.stringify(this.loginForm));
      let pubKey = `-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyNPSkeiN4uhrhET863+9
6t9cXn8lVh1nY+V0fB7HzU2446FAvgDzSVbbhpiyQoVE5+KT5ArQR9y/lYRBqhfv
Ph0noz197DdalOrFhxV0ytXlM0m11oW7pkQQZFgL2IPbnm5f0s6sPhiIol4j5UDb
h8rnevBzA8NQDYtFbkiEuKW8UiovpwoVe1gPBrtxqzhrPnIG01/elHLKGHNfRh8t
BIzh+kyue9zyqkTcu8xhcHZBTiPPj3nvq19N1WWykFaBWbuV9ossYRw/NY6/0Sno
ZR7TvCf39F117X/8Wb8r0t7iV6A2Du85UgD5f7x6nbV+iR64GakQ/IAj4ZE7GgZV
JQIDAQAB
-----END PUBLIC KEY-----`;
      copyObj.password = this.$getRsaCode(pubKey,copyObj.password);
      this.$store
        .dispatch('Login', copyObj)
        .then((res) => {          
            let ua = navigator.userAgent;
            let ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
            isIphone =!ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
            isAndroid = ua.match(/(Android)\s+([\d.]+)/),
            isMobile = isIphone || isAndroid;            
            if(isMobile){   //手机端
              this.$router.push({path:'/barcode/menu'})  
            }else{  //PC端
              this.$router.push({ path: '/' });
            }
            this.locationALLFn();
        })
        .catch((error) => {
          console.log(error)
        })
    }
  }
}
</script>


<style lang="less" scoped>
input[placeholder],
[placeholder],
*[placeholder] {
  color: #999;
}
.page-login {
  height: auto;
  overflow: hidden;
}
.page-login .header-login {
  height: 50px;
  width: 100%;
  margin: 0 auto;
  padding: 40px 0 20px;
}
.page-login .header-login .login {
  display: inline-block;
  line-height: 64px;
  padding-left: 117px;
  background: url(../assets/logo_01.png) no-repeat left center;
}
.page-login .header-login .login span {
  display: inline-block;
  border-left: 1px solid #d1d1d1;
  padding-left: 20px;
  height: 40px;
  line-height: 40px;
  margin-top: 5px;
  font-size: 24px;
  color: #333;
}
.page-login .header-login .header-tel {
  display: inline-block;
  float: right;
  height: 50px;
  line-height: 50px;
  color: #ff9f74;
  font-size: 24px;
  padding-left: 35px;
  background: url(../assets/icon_tel_01.png) no-repeat left center;
}
.page-login .content {
  height: 540px;
  width: 100%;
  overflow: hidden;
  background: url(../assets/bg_01.png) repeat-x center;
}
.page-login .content .wraper {
  width: 100%;
  height: 100%;
  background: url(../assets/bg_02.png) no-repeat center center;
  margin: 0 auto;
  overflow: hidden;
}
.page-login .content .wraper .wraper-login {
  height: 359px;
  width: 349px;
  border: 1px solid #e5e5e5;
  background: #f5f5f5;
  margin: 80px auto 0;
  box-shadow: 1px 3px 4px #dcdcdc;
  padding: 10px;
}
.page-login .content .wraper .wraper-login .login-content {
  height: auto;
  overflow: hidden;
  height: 359px;
  width: 349px;
}
.page-login .content .wraper .wraper-login .login-content .form-content {
  padding: 30px 30px 0;
}
.page-login .content .wraper .wraper-login .login-content .form-content h2 {
  font-size: 20px;
  padding-bottom: 15px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .text-error {
  padding: 10px;
  text-align: center;
  font-size: 12px;
  clear: both;
  color: #f97042;
  border: 1px solid #fee7a9;
  border-radius: 4px;
  background: #fffbee;
  margin-bottom: 5px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group {
  height: 78px;
  border: 1px solid #d2d2d2;
  border-radius: 4px;
  background: #fff;
  padding: 10px;
  margin-bottom: 15px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group label,
.page-login .content .wraper .wraper-login .login-content .form-content .form-group span {
  display: block;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group label {
  height: 39px;
  padding-right: 20px;
  background: url(../assets/icon_login.png) no-repeat;
  overflow: hidden;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group label input {
  display: block;
  width: 100%;
  height: 39px;
  line-height: 40px;
  outline: none;
  font-size: 14px;
  color: #333;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group .label-user {
  background-position: right 10px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group .label-password {
  background-position: right -44px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-group span {
  height: 1px;
  background: #e2e2e2;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item {
  height: 50px;
  overflow: hidden;
  margin-bottom: 15px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > span {
  display: block;
  text-align: center;
  padding-top: 10px;
  font-size: 12px;
  color: #999;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > span > a {
  color: #333;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item .btn-login {
  display: block;
  height: 48px;
  line-height: 48px;
  text-align: center;
  background: #5da4f3;
  border: 1px solid #5199ea;
  font-size: 16px;
  color: #fff;
  border-radius: 4px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item .label-code {
  display: inline-block;
  float: left;
  width: 130px;
  height: 48px;
  overflow: hidden;
  border: 1px solid #d2d2d2;
  border-radius: 4px;
  background: #fff;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item .label-code input {
  display: inline-block;
  padding: 10px;
  width: 110px;
  height: 28px;
  outline: none;
  font-size: 14px;
  color: #333;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > div {
  margin-left: 130px;
  height: 50px;
  line-height: 50px;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > div span,
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > div a {
  display: inline-block;
  float: left;
  margin-left: 10px;
  font-size: 14px;
  color: #444444;
  text-decoration: underline;
}
.page-login .content .wraper .wraper-login .login-content .form-content .form-item > div .code-img {
  width: 90px;
  height: 50px;
  background: red;
}
.page-login .footer-login {
  height: auto;
  overflow: hidden;
  text-align: center;
  min-width: 1200px;
}
.page-login .footer-login h3 {
  font-size: 14px;
  font-weight: normal;
  color: #666666;
  margin-bottom: 10px;
}
.page-login .footer-login p {
  font-size: 12px;
  color: #999999;
}

</style>