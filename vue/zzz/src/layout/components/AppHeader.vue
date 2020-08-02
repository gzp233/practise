<template>
  <div class="home-menu">
    <el-menu :default-active="$route.path" :router="true" mode="horizontal">
      <el-menu-item index="/">首页</el-menu-item>
      <el-submenu index="/imageDirectory">
        <template slot="title">图片</template>
        <el-menu-item
          :index="'/imageDirectory/' + dir.id"
          v-for="dir in imageDirectoryList"
          :key="dir.id"
        >{{ dir.directory_name }}</el-menu-item>
      </el-submenu>
      <el-submenu index="/postCategory">
        <template slot="title">文章</template>
        <el-menu-item
          :index="'/postCategory/' + category.id"
          v-for="category in postCategoryList"
          :key="category.id"
        >{{ category.category_name }}</el-menu-item>
      </el-submenu>
      <el-menu-item index="/chatroom">聊天室</el-menu-item>
      <!-- 右侧用户导航 -->
      <el-submenu index="/user" v-if="username" class="user-menu-right">
        <template slot="title">
          {{ username }}
          <el-avatar :src="(avatar ? avatar : require('@/assets/images/default_avatar.gif'))"></el-avatar>
        </template>
        <el-menu-item index="/logout" @click="handleLogout">退出</el-menu-item>
        <el-menu-item index="/admin" v-if="is_admin">后台</el-menu-item>
      </el-submenu>
      <el-submenu index="/user" v-else class="user-menu-right">
        <template slot="title">用户</template>
        <el-menu-item index="/login">登录</el-menu-item>
        <el-menu-item index="/register">注册</el-menu-item>
      </el-submenu>
    </el-menu>
  </div>
</template>

<script>
import { getDirectoryList } from "@/api/image";
import { getCategoryList } from "@/api/post";

export default {
  name: "AppHeader",
  data() {
    return {
      imageDirectoryList: [],
      postCategoryList: []
    };
  },
  computed: {
    username() {
      return this.$store.getters.name;
    },
    avatar() {
      return this.$store.getters.avatar;
    },
    is_admin() {
      return this.$store.getters.is_admin;
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      getDirectoryList().then(response => {
        this.imageDirectoryList = response;
      });
      getCategoryList().then(response => {
        this.postCategoryList = response;
      });
    },
    async handleLogout() {
      await this.$store.dispatch("user/logout");
      this.$router.push("/");
    }
  }
};
</script>

<style lang="scss" scoped>
.home-menu {
  background: #fff;
  font-family: Helvetica Neue, Helvetica, PingFang SC, Hiragino Sans GB,
    Microsoft YaHei, SimSun, sans-serif;
    border-bottom: solid 1px #e6e6e6;
  .user-menu-right {
    float: right;
  }
  .el-avatar {
    margin-left: 10px;
  }
}
/deep/.el-menu {
  border-bottom: none;
  margin:0 auto;
  width: 1160px;
  .el-menu-item {
    font-size: 12px;
    padding: 0 10px;
  }
  .el-submenu {
    font-size: 12px;
  }
  .el-submenu__title {
    font-size: 12px;
    padding: 0 10px;
  }
}
.el-menu-item {
  font-size: 12px;
  padding: 0 10px;
}
</style>
