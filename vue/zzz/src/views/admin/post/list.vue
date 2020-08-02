<template>
  <div class="app-container">
    <el-row>
      <el-card class="box-card">
        <div style="margin-bottom:50px;">
          <el-col :span="4" class="text-center">
            <el-button type="primary" @click="handleAdd">新增文章</el-button>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column min-width="150px" align="center" label="标题">
        <template slot-scope="scope">
          <span>{{ scope.row.title }}</span>
        </template>
      </el-table-column>

      <el-table-column min-width="150px" align="center" label="标签">
        <template slot-scope="scope">
          <span>{{ tags(scope.row.tags) }}</span>
        </template>
      </el-table-column>

      <el-table-column width="120px" align="center" label="作者">
        <template slot-scope="scope">
          <span>{{ scope.row.user ? scope.row.user.username : '未知' }}</span>
        </template>
      </el-table-column>

      <el-table-column width="150px" align="center" label="分类">
        <template slot-scope="scope">
          <span>{{ scope.row.category ? scope.row.category.category_name : '未知' }}</span>
        </template>
      </el-table-column>

      <el-table-column width="100px" align="center" label="状态">
        <template slot-scope="scope">
          <span>{{ scope.row.is_published ? '已发布' : '未发布' }}</span>
        </template>
      </el-table-column>

      <el-table-column width="80px" align="center" label="评论">
        <template slot-scope="scope">
          <span>
            <router-link :to="'/admin/post/comment/' + scope.row.id">{{ scope.row.comments_count }}</router-link>
          </span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="发布时间">
        <template slot-scope="scope">
          <span>{{ scope.row.published_at ? scope.row.published_at : '未发布' }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="220px">
        <template slot-scope="{row}">
          <el-button type="warning" v-if="row.is_published" size="mini" @click="handleToogle(row.id, false)">撤销</el-button>
          <el-button type="success" v-else size="mini" @click="handleToogle(row.id)">发布</el-button>
          <el-button type="primary" size="mini" @click="handleEdit(row)">编辑</el-button>
          <el-button type="danger" size="mini" @click="handleDel(row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>

  </div>
</template>

<script>
import { getList, destroy, togglePublish } from "@/api/admin/post";

export default {
  name: "Post",
  data() {
    return {
      list: null,
      loading: true,
      query: {
        limit: 20,
        page: 1,
        total: 0
      }
    };
  },
  created() {
    this.fetchData();
  },
  computed: {
    tags() {
      return tags => {
        const tmp = []
        tags.forEach(tag => {
          tmp.push(tag.tag_name)
        });
        return tmp.join(',')
      }
    }
  },
  methods: {
    fetchData() {
      this.loading = true;
      getList(this.query).then(response => {
        this.list = response.data;
        this.query.total = response.total;
        this.loading = false;
      });
    },
    handleCurrentChange(page) {
      this.query.page = page;
      this.fetchData();
    },
    handleDel(id) {
      this.$confirm("此操作将永久删除该文章, 是否继续?", "提示", {
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
      this.$router.push('/admin/post/create')
    },
    handleEdit(row) {
      this.$router.push('/admin/post/edit/' + row.id)
    },
    handleToogle(id, status = true) {
      togglePublish({ id: id, status: status }).then(response => {
        this.$notify({
          title: "成功",
          message: "操作成功",
          type: "success",
          duration: 3000
        });
        this.fetchData();
      })
    }
  }
};
</script>

<style lang="scss" scoped>
.el-pagination {
  margin-top: 20px;
}
</style>
