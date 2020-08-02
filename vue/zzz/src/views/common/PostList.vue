<template>
  <div class="post-container">
    <div v-if="list.length > 0">
      <div class="post" v-for="post in list" :key="post.id">
        <h3 class="post-title">
          <router-link :to="'/post/' + post.id">{{ post.title }}</router-link>
        </h3>
        <div class="post-info">
          <div class="post-info-item">发布时间：<span>{{ post.published_at }}</span></div>
          <div class="post-info-item">分类：<span>{{ post.category ? post.category.category_name : '默认' }}</span></div>
        </div>
        <viewer :value="post.content" />
        <div class="post-more">
          <router-link :to="'/post/' + post.id">更多>>></router-link>
        </div>
        <el-divider></el-divider>
        <div class="post-footer">
          <i class="el-icon-collection-tag"></i>
          <el-tag size="small" class="post-tag" v-for="tag in post.tags" :key="tag.id">
            <router-link :to="'/postTag/' + post.id">{{ tag.tag_name }}</router-link>
          </el-tag>
        </div>
      </div>
    </div>

    <div class="no-data" v-if="list.length === 0">
      暂无数据
    </div>
  </div>
</template>

<script>
import 'tui-editor/dist/tui-editor-contents.css';
import 'highlight.js/styles/github.css';
import { Viewer } from '@toast-ui/vue-editor'

export default {
  name: 'PostList',
  components: { Viewer },
  props: {
    list: {
      type: Array,
      default: () => {
        return []
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.post-container {
  .post {
    background: #fff;
    margin-bottom: 30px;
    border-radius: 4px;
    padding: 30px;
    .post-title {
      text-align: center;
      color: #409eff;
    }
    .post-info {
      text-align: center;
      color: grey;
      font-size: 14px;
      margin-bottom: 30px;
      .post-info-item {
        margin-right: 10px;
        display: inline-block;
      }
    }
    .post-more {
      margin-top: 30px;
      font-size: 14px;
      color: #409eff;
    }
    .post-footer {
      font-size: 16px;
      color: cornflowerblue;
      i {
        margin-right: 10px;
      }
      .post-tag {
        display: inline-block;
        margin-right: 10px;
      }
    }
  }
  .no-data {
    background: #fff;
    text-align: center;
    font-size: 16px;
    line-height: 60px;
  }
}
</style>

