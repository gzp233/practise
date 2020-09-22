<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="toShelf">上架</button>
    </div>

    <div class="grid-content">
      <el-table
        :data="tableData"
        v-loading.body="loading"
        @selection-change="handleSelectionChange"
        stripe
        border
        style="width: 100%"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column
          prop="PRODCHINM"
          :render-header="serachHeader"
          column-key="PRODCHINM"
          label="商品名"
          width="300"
        ></el-table-column>
        <el-table-column
          prop="NewPRODUCTCD"
          :render-header="serachHeader"
          column-key="NewPRODUCTCD"
          label="新产品代码"
          width="200"
        ></el-table-column>
        <el-table-column
          prop="PRODUCTCD"
          :render-header="serachHeader"
          column-key="PRODUCTCD"
          label="产品代码"
          width="200"
        ></el-table-column>
        <el-table-column prop="total" label="库存数量" width="100"></el-table-column>
        <el-table-column label="操作" width="100" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">详情</el-button>
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

    <!-- dialog -->
    <el-dialog title="库区" :visible.sync="dialogVisible" width="50%" :modal-append-to-body="false">
      <el-table :data="stockedList" stripe style="width: 100%">
        <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
        <el-table-column prop="stock_no" label="库区" min-width="150"></el-table-column>
        <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
        <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
        <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
        <el-table-column prop="available_time" label="有效期" min-width="150"></el-table-column>
      </el-table>
      <el-pagination
        @size-change="handleQSizeChange"
        @current-change="handleQCurrentChange"
        :current-page.sync="q.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="q.limit"
        layout="total,->, prev, pager, next, jumper, sizes"
        :total="goodsList.length"
      ></el-pagination>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, getGoodsByProductId } from "@/api/goods";

export default {
  name: "rolelist",
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
        state_name: "C302",
        stock_no: "加工区",
        status: 2
      },
      loading: true,
      tableData: [],
      wareHouseList: [],
      total: 0,
      goodsList: [],
      checked: [],
      dialogVisible: false,
      q: {
        limit: 10,
        page: 1
      }
    };
  },
  mounted() {
    this.loadData();
  },
  created() {
    if (localStorage.instorageprocess_list_query) {
      this.query = JSON.parse(localStorage.instorageprocess_list_query);
      delete localStorage.instorageprocess_list_query;
    }
    this.handleCurrentChange(this.query.page);
  },
  computed: {
    stockedList() {
      let offset = this.q.limit * (this.q.page - 1);
      return this.goodsList.slice(offset, offset + this.q.limit);
    }
  },
  methods: {
    serachHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.query[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.loadData()}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    loadData() {
      if (localStorage.instorageprocess_list_query) {
        this.query = JSON.parse(localStorage.instorageprocess_list_query);
        delete localStorage.instorageprocess_list_query;
      }
      fetchList(this.query).then(res => {
        this.tableData = res.data.data;
        this.total = res.data.total;
      });
      this.loading = true;
      setTimeout(() => {
        this.loading = false;
      }, 1000);
    },
    handleQSizeChange: function(val) {
      this.q.limit = val;
    },
    handleQCurrentChange: function(val) {
      this.q.page = val;
    },
    handleSizeChange(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange(val) {
      this.query.page = val;
      this.loadData();
    },
    showDetail(row) {
      this.query.id = row.id;
      getGoodsByProductId(this.query).then(res => {
        this.goodsList = res.data;
        this.dialogVisible = true;
      });
    },
    handleSelectionChange(val) {
      this.checked = val;
    },
    toShelf() {
      var goodsIdsList = [];
      if (this.checked.length === 0) {
        this.$message({
          message: "请选择商品！",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].goods_ids);
      }
      localStorage.instorageprocess_list_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/instorage_action/instorage_process/detail",
        query: { goodsIdsList: goodsIdsList }
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
.warehouse-select {
  margin-right: 20px;
  margin-bottom: 20px;
}
.stock-item {
  display: block;
  margin: 10px 0;
  .stock-item-title {
    display: inline-block;
    width: 100px;
  }
  .el-input {
    width: 200px;
  }
}
.el-pagination {
  margin: 10px 0;
}
</style>