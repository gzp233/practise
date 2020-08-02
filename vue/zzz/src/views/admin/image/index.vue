<template>
  <div class="mixin-components-container">
    <el-row>
      <el-card>
        <div style="margin-bottom:50px;">
          <el-col :span="4" class="text-center">
            <el-button type="primary" @click="handleAdd">新增相册</el-button>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <div class="card-container">
      <el-card class="box-item" v-for="directory in directoryList" :key="directory.id">
        <div slot="header" class="clearfix">
          <span>{{ directory.directory_name }}</span>
        </div>
        <div class="component-item">
          <div class="img-container">
            <router-link :to="'/admin/image/image/' + directory.id">
              <img :src="directory.is_forbidden === 1 ? imgLocked : imgDefault" />
            </router-link>
          </div>
          <div class="directory-info">
            <div class="directory-item">
              <span>{{ directory.images_count }}</span>张图片
            </div>
            <div class="button-group">
              <el-button type="primary" size="mini" @click="handleEdit(directory)">修改</el-button>
              <el-button type="danger" size="mini" @click="handleDel(directory.id)">删除</el-button>
            </div>
          </div>
        </div>
      </el-card>
    </div>

    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>

    <el-dialog :title="(isEdit ? '编辑' : '新增') + '相册'" :visible.sync="dialogVisible" width="30%" center>
      <el-form ref="directoryForm" label-position="right" label-width="80px" :model="directoryForm" :rules="rules">
        <el-form-item label="名称" prop="directory_name" style="width: 70%;">
          <el-input v-model="directoryForm.directory_name"></el-input>
        </el-form-item>
        <el-form-item label="是否禁用" prop="is_forbidden">
          <el-switch v-model="directoryForm.is_forbidden"></el-switch>
        </el-form-item>
        <el-form-item label="相册描述" prop="desc">
          <el-input v-model="directoryForm.desc" type="textarea"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleSubmit">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getList, create, destroy, update } from "@/api/admin/imageDirectory";

export default {
  name: "ImageDirectory",
  data() {
    return {
      imgDefault: require("@/assets/images/default_directory.png"),
      imgLocked: require("@/assets/images/default_directory_locked.png"),
      dialogVisible: false,
      isEdit: false,
      directoryList: [],
      query: {
        limit: 20,
        page: 1,
        total: 0
      },
      directoryForm: {
        id: "",
        directory_name: "",
        desc: "",
        is_forbidden: false
      },
      rules: {
        directory_name: [
          { required: true, message: "请输入相册名称", trigger: "blur" },
          { min: 2, max: 32, message: "长度在 2 到 32 个字符", trigger: "blur" }
        ],
        desc: [
          { required: true, message: "请填写相册描述", trigger: "blur" },
          { max: 140, message: "长度最多140个字符", trigger: "blur" }
        ]
      }
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      getList().then(response => {
        this.directoryList = response.data;
        this.query.total = response.total;
      });
    },
    handleCurrentChange(page) {
      this.query.page = page;
      this.fetchData();
    },
    resetForm() {
      this.directoryForm = {
        id: "",
        directory_name: "",
        desc: "",
        is_forbidden: false
      };
    },
    handleSubmit() {
      this.$refs.directoryForm.validate(valid => {
        if (valid) {
          if (this.directoryForm.id) {
            update(this.directoryForm).then(response => {
              this.$notify({
                title: "成功",
                message: "修改成功",
                type: "success",
                duration: 3000
              });
              this.fetchData();
              this.dialogVisible = false;
            });
          } else {
            create(this.directoryForm).then(response => {
              this.$notify({
                title: "成功",
                message: "创建成功",
                type: "success",
                duration: 3000
              });
              this.fetchData();
              this.dialogVisible = false;
            });
          }
        }
      });
    },
    handleDel(id) {
      this.$confirm("此操作将永久删除该相册, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          destroy(id).then(response => {
            this.$notify({
              title: "成功",
              message: "删除成功",
              type: "success",
              duration: 3000
            });
            this.fetchData();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    handleAdd() {
      this.resetForm();
      this.isEdit = false;
      this.dialogVisible = true;
    },
    handleEdit(dir) {
      this.isEdit = true;
      this.directoryForm.id = dir.id;
      this.directoryForm.directory_name = dir.directory_name;
      this.directoryForm.desc = dir.desc;
      this.directoryForm.is_forbidden = dir.is_forbidden === 1;
      this.dialogVisible = true;
    }
  }
};
</script>

<style lang="scss" scoped>
.mixin-components-container {
  background-color: #f0f2f5;
  padding: 30px;
  min-height: calc(100vh - 50px);
}
.el-card {
  min-width: 260px;
}
.card-container {
  margin-top: 10px;
  position: relative;
  height: 100%;
  .box-item {
    margin-bottom: 10px;
    margin-left: 1%;
    max-width: 24%;
    display: inline-block;
  }
}

.component-item {
  min-height: 100px;
  .img-container {
    float: left;
    img {
      width: 80px;
      height: 80px;
    }
  }
  .directory-info {
    padding-left: 20px;
    overflow: auto;
    .directory-item {
      font-size: 12px;
      margin-bottom: 10px;
      color: #bbb0b0;
      span {
        font-size: 14px;
        margin: 0 5px;
        color: grey;
      }
    }
    .button-group {
      padding: auto;
      button {
        margin: 10px 10px 0 0;
      }
    }
  }
}
</style>
