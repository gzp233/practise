<template>
  <div class="post-container">
    <div class="post">
      <h3 class="post-title">
        <router-link :to="'/post/' + post.id">{{ post.title }}</router-link>
      </h3>
      <div class="post-info">
        <div class="post-info-item">
          发布时间：
          <span>{{ post.published_at }}</span>
        </div>
        <div class="post-info-item">
          分类：
          <span>{{ post.category ? post.category.category_name : '默认' }}</span>
        </div>
      </div>
      <viewer :value="post.content" />
      <el-divider></el-divider>
      <div class="post-footer">
        <i class="el-icon-collection-tag"></i>
        <el-tag size="small" class="post-tag" v-for="tag in post.tags" :key="tag.id">
          <router-link :to="'/postTag/' + post.id">{{ tag.tag_name }}</router-link>
        </el-tag>
      </div>
    </div>

    <div class="comments" id="comment">
      <el-row class="comment-form">
        <el-col :span="4">
          <el-avatar :src="(avatar ? avatar : require('@/assets/images/default_avatar.gif'))" class="user-avatar"></el-avatar>
        </el-col>
        <el-col :span="16">
          <div class="comment-reply" v-if="reply">
            <div class="comment-reply-content">{{ reply }}</div>
            <a href="javascript:;" @click="cancelReply">取消</a>
          </div>
          <el-input type="textarea" placeholder="请输入内容" v-model.trim="comment" :rows="3" resize="none" maxlength="140" show-word-limit></el-input>
        </el-col>
        <el-col :span="4">
          <el-button type="primary" @click="submitComment" round>发表评论</el-button>
        </el-col>
      </el-row>
      <div v-for="item in comments" :key="item.id">
        <el-divider></el-divider>
        <el-row class="comment">
          <el-col :span="4">
            <el-avatar :src="(item.user.avatar ? item.user.avatar : require('@/assets/images/default_avatar.gif'))" class="user-avatar">
            </el-avatar>
          </el-col>
          <el-col :span="20">
            <div class="comment-user">{{ item.user.username }}</div>
            <div class="comment-content">{{ item.content }}</div>
            <div class="comment-footer">{{ item.created_at }} <a href="#comment" @click="hangdleReply(item)">回复</a>
            </div>
          </el-col>
        </el-row>
      </div>
      <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total">
      </el-pagination>
    </div>
  </div>
</template>

<script>
import { show, getCommentList, publishComment } from "@/api/post";
import 'tui-editor/dist/tui-editor-contents.css';
import 'highlight.js/styles/github.css';
import { Viewer } from '@toast-ui/vue-editor'

export default {
  name: "Post",
  components: { Viewer },
  data() {
    return {
      comment: "",
      reply: '',
      parent_id: 0,
      comments: [],
      post: "",
      query: {
        post_id: 0,
        limit: 20,
        page: 1,
        total: 0
      }
    };
  },
  computed: {
    avatar() {
      return this.$store.getters.avatar;
    }
  },
  created() {
    this.query.post_id = this.$route.params.id;
    this.fetchData();
    this.fetchComments();
  },
  methods: {
    fetchData() {
      show(this.query.post_id)
        .then(response => {
          this.post = response;
        })
        .catch(() => {
          this.$router.push("/");
        });
    },
    fetchComments() {
      getCommentList(this.query).then(response => {
        this.query.total = response.total
        this.comments = response.data
      })
    },
    handleCurrentChange(page) {
      this.query.page = page
      this.fetchComments()
    },
    submitComment() {
      if (this.comment.length === 0) {
        this.$notify({
          title: "错误",
          message: "评论内容不能为空",
          type: "error",
          duration: 1000
        });
        return false
      }
      publishComment({ post_id: this.query.post_id, content: this.reply + '   ' + this.comment, parent_id: this.parent_id }).then(response => {
        this.$notify({
          title: "成功",
          message: "评论成功",
          type: "success",
          duration: 1000
        });
        this.comment = ''
        this.reply = ''
        this.parent_id = 0
        this.query.page = Math.ceil((this.query.total + 1) / this.query.limit)
        this.fetchComments()
      })
    },
    hangdleReply(comment) {
      this.reply = '@' + comment.user.username
      this.parent_id = comment.id
    },
    cancelReply() {
      this.reply = ''
      this.parent_id = 0
    }
  }
};
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
  .comments {
    background: #fff;
    margin-bottom: 30px;
    border-radius: 4px;
    padding: 30px;
    .comment-form {
      .user-avatar {
        width: 70px;
        height: 70px;
      }
      .comment-reply {
        font-size: 14px;
        line-height: 20px;
        color: #6d757a;
        margin-bottom: 5px;
        .comment-reply-content {
          margin-right: 20px;
          float: left;
        }
        a {
          color: cornflowerblue;
        }
      }
      .el-textarea {
        font-size: 12px;
        background-color: #f0f2f5;
      }
      .el-button {
        float: left;
        margin-left: 10px;
        margin-top: 15px;
      }
    }
    .comment {
      .user-avatar {
        width: 70px;
        height: 70px;
      }
      .comment-user {
        color: #6d757a;
        margin-bottom: 10px;
      }
      .comment-content {
        font-size: 14px;
        color: #333;
        margin-bottom: 15px;
      }
      .comment-footer {
        color: grey;
        font-size: 14px;
        a {
          color: #409eff;
          margin-left: 20px;
        }
      }
    }
  }
}
</style>
