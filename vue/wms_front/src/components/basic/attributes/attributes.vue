<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="add">新增商品属性</button>
    </div>
    <div class="grid-content">
      <el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%">
        <el-table-column prop="id" label="商品属性ID" min-width="50"></el-table-column>
        <el-table-column prop="state_name" label="库位状态名称" min-width="100"></el-table-column>
        <el-table-column label="创建日期" min-width="100">
          <template slot-scope="scope">
            <span class="color-gred">{{scope.row.created_at}}</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" min-width="50" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button type="text" size="small" @click.native.prevent="edite(scope.row)">编辑</el-button>
              <el-button @click.native.prevent="deleteRow(scope.row.id)" type="text" size="small">删除</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="grid-page">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page.sync="query.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="query.limit"
        layout="total,->, prev, pager, next, jumper, sizes"
        :total="total"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
import { getIndex, del } from "@/api/attributes";
export default {
  name: "attributes",
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20
      },
      total: 0,
      loading: true,
      tableData: [],
      dialogVisible: false,
      detail: {}
    };
  },
  mounted: function() {
    this.loadData();
  },
  methods: {
    loadData: function() {
      this.loading = true;
      getIndex(this.query).then(response => {
        this.total = response.data.total;
        this.tableData = response.data.data;
      });
      setTimeout(() => {
        this.loading = false;
      }, 2000);
    },
    deleteRow: function(id) {
      this.$confirm("确定删除该该条记录吗?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          del({ id: id }).then(sponse => {
            if (sponse.code == 200) {
              this.$message({
                type: "success",
                message: "删除成功!"
              });
            }
            this.loadData();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    add: function() {
      this.$router.push({ path: "/basic/attributes/add" });
    },
    edite: function(row) {
      this.$router.push({
        path: "/basic/attributes/edit/" + row.id
      });
    }
  }
};
</script>
<style lang="less">
.grid-container {
  height: auto;
  overflow: hidden;
  .action-column {
    text-align: right;
  }
  .color-gred {
    color: #999;
  }
}
.el-dialog--dl-small {
  width: 600px;
}
</style>