<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <!--<button class="tool-btn btn-add" @click="toShelf">冻结</button>-->
      <!--<button class="tool-btn btn-add" @click="Shelf">解冻</button>-->
      <button class="tool-btn btn-add" @click="exportOut">导出</button>
    </div>

    <div class="grid-content">
      <el-table
        :data="tableData"
        v-loading.body="loading"
        height="800"
        @selection-change="handleSelectionChange"
        stripe
        border
        style="width: 100%"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column
                prop="stock_no"
                :render-header="serachHeader"
                column-key="stock_no"
                label="库位"
                width="150"
        ></el-table-column>

        <el-table-column
          prop="NewPRODUCTCD"
          :render-header="serachHeader"
          column-key="NewPRODUCTCD"
          label="新产品代码"
          width="150"
        ></el-table-column>
        <el-table-column
                prop="PRODUCTCD"
                :render-header="serachHeader"
                column-key="PRODUCTCD"
                label="产品代码"
                width="150"
        ></el-table-column>
        <el-table-column
                prop="PRODCHINM"
                :render-header="serachHeader"
                column-key="PRODCHINM"
                label="商品名"
                width="200"
        ></el-table-column>
        <el-table-column
                prop="PRODFLGNM"
                :render-header="serachHeader"
                column-key="PRODFLGNM"
                label="产品分类"
                width="200"
        ></el-table-column>
        <el-table-column
                prop="BRANDNM"
                :render-header="serachHeader"
                column-key="BRANDNM"
                label="品牌分类"
                width="200"
        ></el-table-column>
        <el-table-column
          prop="state_name"
          :render-header="serachHeader"
          column-key="state_name"
          label="库位代码"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="available_time"
          :render-header="serachHeader"
          column-key="available_time"
          label="有效期"
          width="150"
        ></el-table-column>
        <el-table-column prop="number" label="库存数量" width="100"></el-table-column>
        <el-table-column prop="frozen_number" label="已冻结数量" width="120"></el-table-column>
        <!--<el-table-column label="冻结数量" width="100">-->
          <!--<template slot-scope="scope">-->
            <!--<div class="table-form-item">-->
              <!--<el-input-->
                <!--@keyup.native.prevent="inputKeyup(scope.row)"-->
                <!--size="mini"-->
                <!--v-model="scope.row.valnumber"-->
              <!--&gt;</el-input>-->
            <!--</div>-->
          <!--</template>-->
        <!--</el-table-column>-->
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
import { goodsList, getGoodsByProductId, getByNo, relieve } from "@/api/goods";
import { getToken } from "@/utils/auth";

export default {
  name: "rolelist",
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
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
  created() {
    this.loadData();
  },
  computed: {
    leaves() {
      return id => {
        var total = 0;
        for (let k = 0; k < this.tableData.length; k++) {
          if (this.tableData[k].id == id) {
            total = this.tableData[k].number;
          }
        }
        return total;
      };
    },
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
    inputKeyup(row) {
      let leaves = this.leaves(row.id);
      if (leaves < row.valnumber) {
        this.$message({
          message: "商品数量不能超过可分配数量",
          type: "warning"
        });
        row.valnumber = leaves;
      }
    },
    loadData() {
      if (localStorage.goodsStock_query) {
        this.query = JSON.parse(localStorage.goodsStock_query)
        delete localStorage.goodsStock_query
      }
      goodsList(this.query).then(res => {
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
      goodsList(this.query).then(res => {
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
        goodsIdsList.push(this.checked[i]);
      }
      getByNo(goodsIdsList).then(res => {
        goodsIdsList = res.data;
        localStorage.goodsStock_query = JSON.stringify(this.query)
        this.loadData();
      });
    },
    Shelf() {
      var goodsIdsList = [];
      if (this.checked.length === 0) {
        this.$message({
          message: "请选择商品！",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i]);
      }
      relieve(goodsIdsList).then(res => {
        goodsIdsList = res.data;
        localStorage.goodsStock_query = JSON.stringify(this.query)
        this.loadData();
      });
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var goodsIdsList = [];
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].id);
      }
      window.open(
              "/api/goods/export?id=" + goodsIdsList + "&token=" + token[1],
              "_blank"
      );
      this.$router.push({
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
