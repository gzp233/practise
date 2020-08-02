<template>
  <div class="home-container">
    <post-list :list="postList" />
    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>
  </div>

</template>

<script>
import { getList } from "@/api/post";
import PostList from '@/views/common/PostList'

export default {
  name: "Home",
  data() {
    return {
      postList: [],
      query: {
        limit: 5,
        total: 0,
        page: 1
      }
    }
  },
  created() {
    this.fetchData();
  },
  components: { PostList },
  methods: {
    fetchData() {
      getList(this.query).then(response => {
        this.postList = response.data
        this.query.total = response.total
      });
    },
    handleCurrentChange(page) {
      this.query.page = page
      this.fetchData()
    }
  }
};
</script>

<style scoped>
.el-pagination {
  margin-bottom: 20px;
}
</style>
