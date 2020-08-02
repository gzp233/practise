<template>
  <div class="mixin-components-container">
    <el-row>
      <el-card>
        <div style="margin-bottom:20px;">
          <el-col :span="24" class="directory-info">
            <div class="directory-info-item">
              <span>相册：</span>
              {{ directoryInfo.directory_name }}
            </div>
            <div class="directory-info-item">
              <span>创建者：</span>
              {{ directoryInfo.user ? directoryInfo.user.username : '未知' }}
            </div>
            <div class="directory-info-item desc">
              <span>描述：</span>
              {{ directoryInfo.desc }}
            </div>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <div class="image-container" v-if="query.total > 0">
      <el-card shadow="always" v-for="value in images" :key="value.id">
        <el-image style="width: 100%;" fit="contain" :preview-src-list="[value.src]" :src="value.src"></el-image>
      </el-card>
    </div>

    <el-row :gutter="20" v-else style="margin-top:20px;" class="image-container">
      <el-col :span="24">暂无图片</el-col>
    </el-row>

    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>
  </div>
</template>

<script>
import { getDirectoryById, getImagesByDirectoryId } from "@/api/image";

export default {
  name: "Iamges",
  data() {
    return {
      dialogImageUrl: "",
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
      }).catch(() => {
        this.$router.push('/')
      })
    },
    fetchImages() {
      getImagesByDirectoryId(this.query).then(response => {
        this.images = response.data;
        this.query.total = response.total;
      }).catch(() => {
        this.$router.push('/')
      })
    },
    handleCurrentChange(page) {
      this.query.page = page;
      this.fetchImages();
    },
    handlePreview(file) {
      this.dialogImageUrl = file.url;
      this.uploadDialogVisible = true;
    }
  }
};
</script>

<style lang="scss" scoped>
.image-container {
  font-size: 14px;
  color: grey;
  .el-card {
    width: 194px;
    display: inline-block;
    margin: 10px 4px;
    .el-image {
      height: 225px;
    }
  }
  .el-card:nth-child(4n+1){
    margin-left: 0;
  }
  .el-card:nth-child(4n){
    margin-right: 0;
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
</style>
