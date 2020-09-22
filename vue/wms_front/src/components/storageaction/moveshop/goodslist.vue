<template>
  <div class="grid-container">
    <div class="grid-toolbar">
        <button class="tool-btn btn-add" @click="addShop">添加购物车</button>
      <button class="tool-btn btn-add" @click="toShelf">进入购物车</button>
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
          width="200"
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
          prop="stock_no"
          :render-header="serachHeader"
          column-key="stock_no"
          label="库位"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="available_time"
          :render-header="serachHeader"
          column-key="available_time"
          label="效期"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="state_name"
          :render-header="serachHeader"
          column-key="state_name"
          label="状态"
          width="150"
        ></el-table-column>
        <el-table-column prop="number" label="可操作数量" width="150"></el-table-column>
        <!-- <el-table-column label="操作" width="100" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">详情</el-button>
            </div>
          </template>
        </el-table-column> -->
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
        <el-table-column prop="PRODCHINM" label="商品名" min-width="150"></el-table-column>
        <el-table-column prop="stock_no" label="库区" min-width="150"></el-table-column>
        <el-table-column prop="state_name" label="状态" min-width="100"></el-table-column>
        <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
        <el-table-column prop="number" label="数量" min-width="100"></el-table-column>
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
import { getToken } from "@/utils/auth";
import { out,cartList, goodsList2, addCart, delCart} from "@/api/out";
export default {
  name: "rolelist",
  data() {
    return {
      query: {
        page: 1,
        limit: 10,
        in_stock:1,
        sort:"stock_no"
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
      },
    };
  },
  mounted() {
    this.loadData();
  },
  created() {
    if (localStorage.movestock_query) {
      this.query = JSON.parse(localStorage.movestock_query);
      delete localStorage.movestock_query;
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
    addShop(){ 
       addCart(this.checked).then(res => {
          console.log(res);
          this.$message('添加购物车成功!');
          this.loadData();
        })
    },  
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
      if (localStorage.movestock_query) {
        this.query = JSON.parse(localStorage.movestock_query)
        delete localStorage.movestock_query
      };      
      cartList().then(res =>{
        this.query.ids = [];
         for(let i=0;i<res.data.length;i++){
          this.query.ids.push(res.data[i].ids)
        };
        console.log(res,this.query)
         goodsList2(this.query).then(res => {
          this.tableData = res.data.data;
          this.total = res.data.total;
        })  
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
      goodsList2({"in_stock":1}).then(res => {
        this.goodsList = res.data.data;
        this.dialogVisible = true;
      })
    },
    handleSelectionChange(val) {
      this.checked = val;
      console.log(this.checked)
    },
    toShelf() {
      var goodsIdsList = [];    
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].id);
      }
      localStorage.movestock_query = JSON.stringify(this.query)
      this.$router.push({
        path: "/storage_action/moveshop/detail",
        // query: { goodsIdsList: goodsIdsList }
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