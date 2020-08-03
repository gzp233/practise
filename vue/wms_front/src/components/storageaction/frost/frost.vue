<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="handleCreate">新建冻结</button>

      <!--<button class="tool-btn btn-refresh" @click="handleList">解冻</button>-->
    </div>

    <div class="grid-content">
      <el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%" height="800" border>
        <el-table-column
                prop="code"
                :render-header="serachHeader"
                column-key="code"
                label="冻结编号"
                width="150"
        ></el-table-column>
        <el-table-column prop="stock_no" label="库位数" width="100"></el-table-column>
        <el-table-column prop="type" label="型号数" width="100"></el-table-column>
        <el-table-column prop="number" label="数量" width="100"></el-table-column>
        <el-table-column
                prop="status"
                :render-header="serachStatusHeader"
                column-key="status"
                label="状态"
                width="150"
        ></el-table-column>
        <el-table-column
                :render-header="serachTimeHeader"
                column-key="created_at"
                prop="created_at"
                label="创建时间"
                width="200"
        ></el-table-column>
        <el-table-column label="操作" width="150" class-name="action-column">
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
              title="新建冻结"
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
          <el-input v-model.trim="codeInput" placeholder="产品代码" @keyup.enter.native="handleAdd"></el-input>
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
          <el-table-column prop="available_time" label="效期" min-width="100"></el-table-column>
          <el-table-column prop="frozen_number" label="已冻结数量" min-width="150"></el-table-column>
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
        <div class="selected-title">
        <span style="margin-left:10px;">
                冻结单号:
                <span>{{box_code}}</span>
              </span>
        </div>
        <div class="code-div">
          <el-input v-model.trim="tmpInput" placeholder="备注" @keyup.enter.native="handleAdd"></el-input>
        </div>
        <el-table :data="postShowList" stripe style="width: 100%">
          <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
          <el-table-column prop="stock_no" label="库位" min-width="100"></el-table-column>
          <el-table-column prop="state_name" label="状态" min-width="100"></el-table-column>
          <el-table-column prop="number" label="数量" min-width="100"></el-table-column>
          <el-table-column prop="available_time" label="效期" min-width="100"></el-table-column>
          <el-table-column prop="frozen_number" label="已冻结数量" min-width="100"></el-table-column>
          <el-table-column label="冻结数量" width="100">
            <template slot-scope="scope">
              <div class="table-form-item">
                <el-input
                        @keyup.native.prevent="inputKeyup(scope.row)"
                        size="mini"
                        v-model="scope.row.valnumber"
                ></el-input>
              </div>
            </template>
          </el-table-column>
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
          unfreezeIndex,
          getByNo,
          getAllProducts,
          getAllGoods,
          getDetailById
  } from "@/api/goods";

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
        goodscode: [],
        tmpInput: "",
        codeInput: "",
        total: 0,
        queryGoods: {
          checkedProduct: "",
          product_id: ""
        },
        timeout: null,
        postGoods: [],
        postGood: [],
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
        box_code: "",
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
      },
      leaves() {
        return id => {
          var total = 0
          for (let k = 0; k < this.postShowList.length; k++) {
            if (this.postShowList[k].id == id) {
              total = this.postShowList[k].number;
            }
          }
          return total;
        };
      },
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
      serachTimeHeader(h, { column, $index }, index) {
        return (
                <div class="header-custom-stype">
                <el-date-picker
        v-model={this.query[column.columnKey]}
        type="datetimerange"
        range-separator=" 至 "
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        on-change={this.loadData}
      />
      </div>
      );
      },
      serachStatusHeader(h, { column, $index }, index) {
        return (<div class="header-custom-stype">
                <el-select
        v-model={this.query[column.columnKey]}
        on-Change={this.loadData}
        placeholder={column.label}
      >
      <el-option  value="全部" label="全部" />
                <el-option value="已冻结" label="已冻结" />
                <el-option value="已释放" label="已释放" />
                </el-select>
                </div>);
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
        unfreezeIndex(this.query).then(res => {
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
        this.box_code = new Date()-0+Math.floor(Math.random()*100);
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
          product_id: this.queryGoods.product_id,
          codeInput: this.codeInput
        }).then(res => {
          this.goods = res.data;
      });
      },
      doCreate() {
        if (this.postGoods.length === 0) {
          this.$message({
            message: "请选择要冻结的商品",
            type: "warning"
          });
          return;
        }
        this.postGood = [];
        this.postGood.push({
          code: this.tmpInput,
          box_code:this.box_code,
          postGoods:this.postGoods
        });
        getByNo(this.postGood).then(res => {
          this.$message({
          message: "创建成功",
          type: "success"
        });
        localStorage.instorageprocess_query = JSON.stringify(this.query);
        this.postGood = [];
        this.loadData();
        this.dialogVisible = false;
      });
      },
      handleList() {
        localStorage.instorageprocess_query = JSON.stringify(this.query);
        this.$router.push({ path: "/storage_action/frost/list" });
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
      showDetail: function(detail) {
        localStorage.in_adjust_query = JSON.stringify(this.query);
        this.$router.push({
          path: "/storage_action/frost/detail/"+detail.code
        });
      },
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
