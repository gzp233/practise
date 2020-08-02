<template>
  <div class="app-container">
    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column width="120px" align="center" label="评论者">
        <template slot-scope="scope">
          <span>{{ scope.row.user ? scope.row.user.username : '未知' }}</span>
        </template>
      </el-table-column>

      <el-table-column min-width="150px" align="center" label="内容">
        <template slot-scope="scope">
          <span>{{ scope.row.content }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="发布时间">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="200px">
        <template slot-scope="{row}">
          <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDel(row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>

  </div>
</template>

<script>
import { getList, destroy } from "@/api/admin/postComment";

export default {
  name: "Comment",
  data() {
    return {
      id: undefined,
      list: null,
      post: '',
      loading: true,
      query: {
        limit: 20,
        page: 1,
        total: 0
      }
    };
  },
  created() {
    this.id = this.$route.params.id
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.loading = true;
      getList({ id: this.id }).then(response => {
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
      this.$confirm("此操作将永久删除该评论, 是否继续?", "提示", {
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
    }
  }
};
</script>

<style lang="scss" scoped>
.el-pagination {
  margin-top: 20px;
}
</style>
