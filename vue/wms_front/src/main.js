// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import ES6Promise from 'es6-promise'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css';

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
import JSEncrypt from 'jsencrypt'
Vue.prototype.$getRsaCode = function(pubKey,str){ // 注册方法
  let encryptStr = new JSEncrypt();
  encryptStr.setPublicKey(pubKey); // 设置 加密公钥
  let data = encryptStr.encrypt(str);  // 进行加密
  return data;
};

import './permission' // permission control

Vue.use(ElementUI);
Vue.use(MintUI);
ES6Promise.polyfill();

Vue.config.productionTip = false

// 注册按钮权限指令
import permissionDirective from './directive/permission'
Vue.directive("permission",permissionDirective);

/* eslint-disable no-new */
new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: { App }
});
