<template>
  <div class="app-sidebar">
    <el-card class="box-card" shadow="always">
      <div slot="header" class="clearfix">
        <span>标签</span>
      </div>
      <el-tag class="tags" v-for="tag in tagList" :key="tag.id">
        <router-link :to="'/postTag/' + tag.id">{{ tag.tag_name }}</router-link>
      </el-tag>
    </el-card>

    <el-card class="box-card" shadow="always">
      <div slot="header" class="clearfix">
        <span>网站信息</span>
      </div>
      <div class="text item"><a href="http://www.gezhipeng.cn">www.gezhipeng.cn</a></div>
      <div class="text item">©2019-2021 苏ICP备18037004号-1</div>
      <div class="text item">
        <a href="https://www.php.net/" target="_blank">php</a>
        <a href="https://wiki.swoole.com/" target="_blank">swoole</a>
        <a href="https://cn.vuejs.org/" target="_blank">vue.js</a>
        <a href="https://learnku.com/docs/laravel/5.8" target="_blank">laravel</a>
      </div>
    </el-card>
  </div>
</template>

<script>
import { getTagList } from "@/api/post";

export default {
  name: "Sidebar",
  data() {
    return {
      className: 'sidebar-hide',
      tagList: []
    }
  },
  created() {
    this.fetchData()
  },
  methods: {
    fetchData() {
      getTagList().then(response => {
        this.tagList = response
      })
    }
  }
};
</script>

<style lang="scss" scoped>
.app-sidebar {
  .el-card {
    font-size: 16px;
    margin-bottom: 20px;
    font-family: Helvetica Neue, Helvetica, PingFang SC, Hiragino Sans GB,
      Microsoft YaHei, SimSun, sans-serif;
    color: #001528;
    .el-tag {
      margin-right: 10px;
      margin-bottom: 10px;
    }
    .item {
      font-size: 14px;
      color: grey;
      margin-bottom: 10px;
      a {
        margin-right: 5px;
      }
    }
  }
}
</style>

<style lang="scss" scoped>
.app-sidebar {
  width: 352px;
  float:left;
}
</style>
