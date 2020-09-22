<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="handleCreate">新建加工</button>

      <button class="tool-btn btn-refresh" @click="handleList">加工区</button>
    </div>

    <div class="grid-content">
      <el-table :data="tableData" v-loading.body="loading" stripe height="800" style="width: 100%" border>
        <el-table-column
          prop="product.NewPRODUCTCD"
          label="新产品代码"
          width="150"
          :render-header="serachHeader"
          column-key="NewPRODUCTCD"
        ></el-table-column>
        <el-table-column
          prop="product.PRODCHINM"
          label="产品名"
          width="200"
          :render-header="serachHeader"
          column-key="PRODCHINM"
        ></el-table-column>
        <el-table-column label="动作" width="100">
          <template slot-scope="scope">
            <span>{{ scope.row.type === 1 ? '上架' : '加工' }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="number" label="数量" width="100"></el-table-column>
        <el-table-column label="操作" width="75" class-name="action-column">
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

      <!-- dialog -->
      <el-dialog
        title="新建加工"
        :visible.sync="dialogVisible"
        width="50%"
        :modal-append-to-body="false"
      >
        <div class="choose-ensure">
          <el-autocomplete
            v-model="queryGoods.checkedProduct"
            :fetch-suggestions="querySearchAsync"
            placeholder="请输入产品名"
            @select="handleSelect"
          ></el-autocomplete>
          <el-button type="primary" @click="getGoods">查 找</el-button>
          <el-button type="primary" @click="addGoods">添 加</el-button>
        </div>

        <el-table
          :data="showList"
          stripe
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
          <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
          <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
          <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
        </el-table>
        <el-pagination
          @size-change="handleQSizeChange"
          @current-change="handleQCurrentChange"
          :current-page.sync="q.page"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="q.limit"
          layout="total,->, prev, pager, next, jumper, sizes"
          :total="goods.length"
        ></el-pagination>
        <div class="selected-title">已选择列表</div>

        <el-table :data="postShowList" stripe style="width: 100%">
          <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
          <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
          <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
          <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
          <el-table-column label="操作" width="100">
            <template slot-scope="scope">
              <div class="action-column">
                <el-button type="text" size="small" @click.native.prevent="delRow(scope.row)">删除</el-button>
              </div>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          @size-change="handleSSizeChange"
          @current-change="handleSCurrentChange"
          :current-page.sync="s.page"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="s.limit"
          layout="total,->, prev, pager, next, jumper, sizes"
          :total="postGoods.length"
        ></el-pagination>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">取 消</el-button>
          <el-button type="primary" @click="doCreate">确 定</el-button>
        </span>
      </el-dialog>

      <!-- dialog -->
      <el-dialog
        title="库区"
        :visible.sync="dialogShowVisible"
        width="50%"
        :modal-append-to-body="false"
      >
        <el-table :data="detailList" stripe style="width: 100%">
          <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
          <el-table-column prop="stock_no" label="转入库区" min-width="150"></el-table-column>
          <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
          <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
          <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
          <el-table-column prop="available_time" label="有效期" min-width="150"></el-table-column>
        </el-table>
        <el-pagination
          @size-change="handleDSizeChange"
          @current-change="handleDCurrentChange"
          :current-page.sync="d.page"
          :page-sizes="[10, 20, 50, 100]"
          :page-size="d.limit"
          layout="total,->, prev, pager, next, jumper, sizes"
          :total="detail.length"
        ></el-pagination>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogShowVisible = false">取 消</el-button>
          <el-button type="primary" @click="dialogShowVisible = false">确 定</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import {
  fetchList,
  move,
  getAllProducts,
  getAllGoods,
  getDetailById
} from "@/api/instorage_process";

export default {
  name: "rolelist",
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20
      },
      loading: true,
      dialogVisible: false,
      tableData: [],
      products: [],
      total: 0,
      queryGoods: {
        checkedProduct: "",
        product_id: ""
      },
      timeout: null,
      postGoods: [],
      goods: [],
      checked: [],
      q: {
        page: 1,
        limit: 20
      },
      s: {
        page: 1,
        limit: 20
      },
      detail: [],
      dialogShowVisible: false,
      d: {
        page: 1,
        limit: 20
      }
    };
  },
  mounted: function() {
    this.loadData();
    this.loadProducts();
  },
  created() {
    if (localStorage.instorageprocess_query) {
      this.query = JSON.parse(localStorage.instorageprocess_query);
      delete localStorage.instorageprocess_query;
    }
    this.handleCurrentChange(this.query.page);
  },
  computed: {
    showList() {
      let offset = this.q.limit * (this.q.page - 1);
      return this.goods.slice(offset, offset + this.q.limit);
    },
    postShowList() {
      let offset = this.s.limit * (this.s.page - 1);
      return this.postGoods.slice(offset, offset + this.s.limit);
    },
    detailList() {
      let offset = this.d.limit * (this.d.page - 1);
      return this.detail.slice(offset, offset + this.d.limit);
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
    querySearchAsync(queryString, cb) {
      this.queryGoods.product_id = "";
      var products = this.products;
      var results = queryString
        ? products.filter(this.createStateFilter(queryString))
        : products;
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        cb(results);
      }, 3000 * Math.random());
    },
    createStateFilter(queryString) {
      return state => {
        return state.PRODCHINM.indexOf(queryString) === 0;
      };
    },
    handleSelect(item) {
      this.queryGoods.product_id = item.id;
    },
    loadProducts() {
      getAllProducts().then(res => {
        this.products = res.data;
        for (let i = 0; i < this.products.length; i++) {
          this.products[i].value = this.products[i].PRODCHINM;
        }
      });
    },
    loadData: function() {
      if (localStorage.instorageprocess_query) {
        this.query = JSON.parse(localStorage.instorageprocess_query);
        delete localStorage.instorageprocess_query;
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
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    handleQSizeChange: function(val) {
      this.q.limit = val;
    },
    handleQCurrentChange: function(val) {
      this.q.page = val;
    },
    handleSSizeChange: function(val) {
      this.s.limit = val;
    },
    handleSCurrentChange: function(val) {
      this.s.page = val;
    },
    handleDSizeChange: function(val) {
      this.d.limit = val;
    },
    handleDCurrentChange: function(val) {
      this.d.page = val;
    },
    handleCreate() {
      this.postGoods = [];
      this.goods = [];
      this.checked = [];
      this.queryGoods.product_id = "";
      this.queryGoods.checkedProduct = "";
      this.dialogVisible = true;
    },
    handleSelectionChange(val) {
      this.checked = val;
    },
    getGoods() {
      getAllGoods({
        product_id: this.queryGoods.product_id
      }).then(res => {
        this.goods = res.data;
      });
    },
    doCreate() {
      if (this.postGoods.length === 0) {
        this.$message({
          message: "请选择要加工的商品",
          type: "warning"
        });
        return;
      }
      move(this.postGoods).then(res => {
        this.$message({
          message: "创建成功",
          type: "success"
        });
        localStorage.instorageprocess_query = JSON.stringify(this.query);
        this.loadData();
        this.dialogVisible = false;
      });
    },
    handleList() {
      localStorage.instorageprocess_query = JSON.stringify(this.query);
      this.$router.push({ path: "/instorage_action/instorage_process/list" });
    },
    delRow(row) {
      this.postGoods.splice(this.postGoods.indexOf(row), 1);
    },
    addGoods() {
      if (this.checked.length == 0) {
        this.$message({
          message: "要添加的商品为空",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.checked.length; i++) {
        let flag = 0;
        for (let j = 0; j < this.postGoods.length; j++) {
          if (this.postGoods[j].id === this.checked[i].id) {
            flag = 1;
          }
        }
        if (flag === 0) this.postGoods.push(this.checked[i]);
      }
      this.$message({
        message: "添加成功",
        type: "success"
      });
    },
    showDetail(row) {
      getDetailById({ id: row.id }).then(res => {
        this.dialogShowVisible = true;
        this.detail = res.data;
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
.choose-ensure {
  .el-input {
    width: 300px;
    display: inline-block;
    margin-right: 10px;
  }
  margin-bottom: 30px;
}
.el-dialog--dl-small {
  width: 600px;
}
.selected-title {
  margin: 15px 0;
  font-size: 16px;
}
</style>
