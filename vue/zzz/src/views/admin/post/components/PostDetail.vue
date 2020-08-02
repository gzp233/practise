<template>
  <div class="createPost-container">
    <el-form class="form-container" :model="postForm" :rules="rules" ref="postForm">

      <div class="sub-buttons">
        <el-button v-if="isEdit" v-loading="loading" style="margin-left: 10px;" type="success" @click="submitForm">更新
        </el-button>
        <el-button v-else v-loading="loading" style="margin-left: 10px;" type="success" @click="submitForm">保存
        </el-button>
        <router-link to="/admin/post/list">
          <el-button style="margin-left: 10px;" type="warning">
            取消
          </el-button>
        </router-link>
      </div>

      <div class="createPost-main-container">
        <el-row>
          <el-col :span="21">
            <el-form-item style="margin-bottom: 40px;" prop="title">
              <MDinput name="name" v-model.trim="postForm.title" required :maxlength="100">
                标题
              </MDinput>
            </el-form-item>
          </el-col>
        </el-row>

        <div class="postInfo-container">
          <el-row>
            <el-col :span="24">
              <el-form-item label-width="60px" label="分类:" class="postInfo-container-item">
                <el-select v-model="postForm.category_id" placeholder="选择分类">
                  <el-option v-for="category in categoryList" :key="category.id" :label="category.category_name" :value="category.id" />
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="24">
              <el-form-item label-width="90px" label="立即发布:" class="postInfo-container-item">
                <el-switch v-model="postForm.is_published"></el-switch>
              </el-form-item>
            </el-col>
          </el-row>
          <el-row>
            <el-col :span="24">
              <el-form-item label-width="60px" label="标签:" class="postInfo-container-item">
                <el-transfer filterable :titles="['未选','已选']" v-model="postForm.tags" :props="{ key: 'id',label: 'tag_name' }" :data="tagList">
                </el-transfer>
              </el-form-item>
            </el-col>
          </el-row>
        </div>

        <div class="editor-container">
          <markdown-editor v-model="postForm.content" ref="markdownEditor" height="800px" />
        </div>
      </div>
    </el-form>

  </div>
</template>

<script>
import MarkdownEditor from '@/components/MarkdownEditor'
import MDinput from '@/components/MDinput'
import { getById, create, update } from '@/api/admin/post'
import { getAll as getCategoryList } from '@/api/admin/postCategory'
import { getAll as getTagList } from '@/api/admin/postTag'

const defaultForm = {
  title: '',
  content: '',
  category_id: '',
  is_published: true,
  tags: [],
  id: undefined
}

export default {
  name: 'PostDetail',
  components: { MDinput, MarkdownEditor },
  props: {
    isEdit: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      postForm: Object.assign({}, defaultForm),
      loading: false,
      categoryList: [],
      tagList: [],
      rules: {
        title: [
          { required: true, message: '标题必填', trigger: 'blur' },
          { min: 2, max: 128, message: "长度在 2 到 128 个字符", trigger: 'blur' }
        ],
        content: [
          { required: true, message: '内容必填', trigger: 'blur' }
        ],
        category_id: [
          { required: true, message: '分类必选', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    if (this.isEdit) {
      const id = this.$route.params && this.$route.params.id
      this.fetchData(id)
    } else {
      this.postForm = Object.assign({}, defaultForm)
    }
    this.fetchBaseData()
  },
  methods: {
    fetchData(id) {
      getById(id).then(response => {
        this.postForm = response
        this.postForm.is_published = response.is_published === 1;
      })
    },
    fetchBaseData() {
      getCategoryList().then(response => {
        this.categoryList = response
      })
      getTagList().then(response => {
        this.tagList = response
      })
    },
    submitForm() {
      this.$refs.postForm.validate(valid => {
        if (valid) {
          if (this.isEdit) {
            update(this.postForm)
              .then(response => {
                this.loading = true
                this.$notify({
                  title: '成功',
                  message: '修改成功',
                  type: 'success',
                  duration: 2000
                })
                this.loading = false
                this.$router.push('/admin/post/list')
              })
              .catch({})
          } else {
            create(this.postForm)
              .then(response => {
                this.loading = true
                this.$notify({
                  title: '成功',
                  message: '发布成功',
                  type: 'success',
                  duration: 2000
                })
                this.loading = false
                this.$router.push('/admin/post/list')
              })
              .catch({})
          }
        }
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
@import "src/styles/mixin.scss";
.createPost-container {
  position: relative;
  .sub-buttons {
    height: 50px;
    z-index: 10;
    width: auto;
    background: #d0d0d0;
    line-height: 50px;
    position: relative;
    text-align: right;
    padding-right: 20px;
  }
  .createPost-main-container {
    padding: 40px 45px 20px 50px;
    .postInfo-container {
      position: relative;
      @include clearfix;
      margin-bottom: 10px;
      .postInfo-container-item {
        float: left;
      }
    }
    .editor-container {
      min-height: 500px;
      margin: 0 0 30px;
    }
  }
}
</style>

<style lang="scss">
.postInfo-container {
  .el-checkbox-group {
    .el-checkbox {
      display: block;
    }
  }
}
</style>
