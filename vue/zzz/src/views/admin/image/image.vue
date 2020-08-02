<template>
  <div class="mixin-components-container">
    <el-row>
      <el-card>
        <div style="margin-bottom:50px;">
          <el-col :span="6" class="btn-group">
            <el-button type="success" size="mini" @click="dialogVisible = true">上传</el-button>
            <el-button type="danger" size="mini" @click="handleDel">删除</el-button>
            <el-button type="primary" size="mini" @click="handleBack">返回</el-button>
          </el-col>
          <el-col :span="18" class="directory-info">
            <div class="directory-info-item">
              <span>相册：</span>
              {{ directoryInfo.directory_name }}
            </div>
            <div class="directory-info-item">
              <span>创建者：</span>
              {{ directoryInfo.user ? directoryInfo.user.username : '未知' }}
            </div>
            <div class="directory-info-item">
              <span>状态：</span>
              {{ directoryInfo.is_forbidden ? '禁用' : '启用' }}
            </div>
            <div class="directory-info-item">
              <span>创建时间：</span>
              {{ directoryInfo.created_at }}
            </div>
            <div class="directory-info-item desc">
              <span>描述：</span>
              {{ directoryInfo.desc }}
            </div>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <div v-if="query.total > 0" style="margin-top:50px;" class="image-container">
      <el-card v-for="value in images" :key="value.id" shadow="always">
        <div slot="header">
          <span>{{ value.created_at }}</span>
          <el-checkbox class="card-checkbox" v-model="value.checked"></el-checkbox>
        </div>
        <el-image style="width: 100%;" fit="contain" :preview-src-list="[value.src]" :src="value.src"></el-image>
      </el-card>
    </div>

    <el-row :gutter="20" v-else style="margin-top:50px;" class="image-container">
      <el-col :span="24">暂无图片</el-col>
    </el-row>

    <el-pagination v-if="query.total > 0" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>

    <el-dialog title="上传图片" :visible.sync="dialogVisible" width="50%" center>
      <el-upload action="#" ref="uploadImage" accept="image/*" :limit="5" :multiple="true" list-type="picture-card" :auto-upload="false" :on-preview="handlePreview">
        <i class="el-icon-plus"></i>
      </el-upload>
      <el-dialog :visible.sync="uploadDialogVisible" append-to-body>
        <img width="100%" :src="dialogImageUrl" alt />
      </el-dialog>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="submitUpload" :disabled="btnDisabled">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getImagesByDirectoryId, upload, deleteByIds } from "@/api/admin/image";
import { getDirectoryById } from "@/api/admin/imageDirectory";

export default {
  name: "DirectoryDetail",
  data() {
    return {
      dialogVisible: false,
      uploadDialogVisible: false,
      dialogImageUrl: "",
      btnDisabled: false,
      directoryInfo: {},
      images: [],
      query: {
        page: 1,
        limit: 20,
        total: 0,
        id: 0
      }
    };
  },
  created() {
    this.query.id = this.$route.params.id;
    this.fetchData();
    this.fetchImages();
  },
  methods: {
    fetchData() {
      getDirectoryById(this.query.id).then(response => {
        this.directoryInfo = response;
      });
    },
    fetchImages() {
      getImagesByDirectoryId(this.query).then(response => {
        this.images = response.data;
        this.query.total = response.total;
      });
    },
    handleCurrentChange(page) {
      this.query.page = page;
      this.fetchImages();
    },
    handlePreview(file) {
      this.dialogImageUrl = file.url;
      this.uploadDialogVisible = true;
    },
    submitUpload() {
      this.btnDisabled = true
      const files = this.$refs.uploadImage.uploadFiles;
      if (files.length === 0) {
        this.$notify({
          title: "错误",
          message: "请选择上传的图片",
          type: "error",
          duration: 1000
        });
        this.btnDisabled = false
        return false;
      }
      const form = new FormData();
      form.append("directory_id", this.query.id);
      const FILE_MAX_SIZE = 8 * 1024 * 1024;
      for (const file of files) {
        if (file > FILE_MAX_SIZE) {
          this.$notify({
            title: "错误",
            message: "文件大小不能超过8M",
            type: "error",
            duration: 1000
          });
          this.btnDisabled = false
          return false;
        }
        form.append("files[]", file.raw);
      }
      upload(form).then(response => {
        this.$notify({
          title: "成功",
          message: "上传成功",
          type: "success",
          duration: 1000
        });
        this.dialogVisible = false;
        this.btnDisabled = false
        this.$refs.uploadImage.clearFiles();
        this.fetchImages()
      }).catch(() => {
        this.btnDisabled = false
      });
    },
    handleDel() {
      const ids = [];
      for (const image of this.images) {
        if (image.checked) ids.push(image.id);
      }
      if (ids.length === 0) {
        this.$notify({
          title: "错误",
          message: "请选择要删除的图片",
          type: "error",
          duration: 1500
        });
        return false;
      }
      this.$confirm("确定删除这些图片吗?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          deleteByIds({ ids: ids }).then(response => {
            this.$notify({
              title: "成功",
              message: "删除成功",
              type: "success",
              duration: 3000
            });
            this.fetchImages();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    handleBack() {
      this.$router.push({ path: "/admin/image/index" });
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
.image-container {
  font-size: 14px;
  color: grey;
  .el-card {
    max-width: 24%;
    display: inline-block;
    margin-right: 1%;
    margin-bottom: 20px;
    .card-checkbox {
      float: right;
    }
    .el-image {
      height: 40vh;
    }
  }
}
.directory-info {
  padding-left: 15px;
  font-size: 14px;
  color: #b3b3b3;
  .directory-info-item {
    min-width: 150px;
    margin-bottom: 10px;
    float: left;
    span {
      font-weight: bold;
    }
  }
  .desc {
    width: 100%;
  }
}
.btn-group {
  button {
    margin-bottom: 10px;
  }
  .el-button + .el-button {
    margin-left: 0;
    margin-right: 10px;
  }
}
</style>
