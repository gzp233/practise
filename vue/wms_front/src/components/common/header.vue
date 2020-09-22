<template>
  <div class="layout-header">
    <div class="header-logo"></div>
    <div class="head-navbar">
      <ul>
        <li v-for="item in headData" :class="item.headerClass">
          <router-link :to="'/'+item.module" :id="item.module">
            <span>{{item.text}}</span>
          </router-link>
        </li>
      </ul>
    </div>
    <div class="loyout-right">
      <span class="span-icon-loginout" @click="handleLogout()"></span>
      <!-- <span class="span-icon-message"></span> -->
      <span class="span-text">{{ username }}</span>
    </div>
  </div>
</template>
<script>
export default {
  name: 'appHeader',
  data: function() {
    return {
      module: 'index'
    }
  },
  props: ['navdata'],
  computed: {
    username() {
      return this.$store.getters.username
    },
    headData() {
      var headData = []
      const path = this.$route.path.split('/')
      const permissions = this.$store.getters.permissions
      for (var i = 0; i < this.navdata.length; i++) {
        if (
          this.$store.getters.roles.indexOf('admin') >= 0 ||
          !this.navdata[i].permission ||
          (this.navdata[i].permission &&
            permissions.indexOf(this.navdata[i].permission) >= 0)
        ) {
          if(this.navdata[i].module == path[1]) {
            this.navdata[i].headerClass = 'active'
          } else {
            this.navdata[i].headerClass = ''
          }
          headData.push(this.navdata[i])
        }
      }

      return headData
    }
  },
  methods: {
    handleLogout() {
      this.$confirm('确定退出吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          this.$store.dispatch('LogOut').then(res => {
            this.$message({
              type: 'success',
              message: '退出成功!'
            })
            this.$router.push({ path: '/login' })
          })
        })
        .catch(() => {
          this.$message({
            type: 'info',
            message: '已取消'
          })
        })
    }
  }
}
</script>
<style lang="less">
@media screen and (max-width: 750px) {
  .el-message-box{width:85%!important;}
};
.layout-header {
  position: absolute;
  height: 50px;
  left: 0;
  right: 0;
  top: 0;
  background: #3b86db;
  z-index: 99;
  .header-logo {
    float: left;
    width: 160px;
    height: 50px;
    background: #3b86db url(../../assets/logo.png) no-repeat center center;
  }
  .head-navbar {
    .active{
      background: #2267b5;
    }
    display: inline-block;
    float: left;
    height: 50px;
    overflow: hidden;
    ul {
      display: block;
      height: auto;
      overflow: hidden;
      li {
        display: inline-block;
        float: left;
        a {
          display: inline-block;
          height: 50px;
          line-height: 50px;
          text-align: center;
          padding: 0 20px;
          text-decoration: none;
          cursor: pointer;
          span {
            font-size: 14px;
            color: #fff;
          }
        }
        a.active {
          background: #439bff;
          font-weight: bold;
        }
      }
    }
  }
  .loyout-right {
    display: inline-block;
    float: right;
    height: 50px;
    span {
      position: relative;
      display: inline-block;
      min-width: 25px;
      height: 50px;
      margin: 0;
      padding: 0 10px;
      float: right;
    }
    span.span-text {
      line-height: 50px;
      color: #fff;
      font-size: 14px;
    }
    span.span-icon-message {
      cursor: pointer;
      background: url('../../assets/icon-msg.png') no-repeat center center;
    }
    span.span-icon-loginout {
      cursor: pointer;
      background: url('../../assets/icon-login-out.png') no-repeat center center;
    }
  }
}
</style>