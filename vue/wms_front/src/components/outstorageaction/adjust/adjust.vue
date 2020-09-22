<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <!--<button class="tool-btn btn-add" @click="stockOut">集货</button>-->
      <button class="tool-btn btn-add" @click="exportOut">导出</button>
    </div>
    <div class="grid-content">
      <el-table
        :data="tableData"
        v-loading.body="loading"
        @selection-change="handleSelectionChange"
        border
        stripe
        :row-key="getRowKeys"
        height="800"
        style="width: 100%"
      >
        <el-table-column type="selection" :reserve-selection="true" width="55"></el-table-column>
        <el-table-column
          prop="AdjustNo"
          :render-header="serachHeader"
          column-key="AdjustNo"
          label="调整编号"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="OutStcNo"
          :render-header="serachHeader"
          column-key="OutStcNo"
          label="出库编号"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="FineFlg"
          :render-header="serachHeader"
          column-key="FineFlg"
          label="出库库位代码"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="ShopSignNM"
          :render-header="serachHeader"
          column-key="ShopSignNM"
          label="商店"
          width="150"
        ></el-table-column>
        <el-table-column prop="type" label="型号数" width="100"></el-table-column>
        <el-table-column prop="number" label="总数量" width="100"></el-table-column>
        <el-table-column
          prop="status"
          :render-header="serachStatusHeader"
          column-key="status"
          label="状态"
          width="120"
        ></el-table-column>
         <el-table-column
          prop="zt"
          :render-header="serachStatusZt"
          column-key="zt"
          label="回传状态"
          width="150"
        ></el-table-column>
        <el-table-column
          :render-header="serachTimeHeader"
          column-key="created_at"
          prop="created_at"
          label="订单导入时间"
          width="200"
        ></el-table-column>
        <el-table-column
          :render-header="serachTimeHeader"
          column-key="time"
          prop="time"
          label="订单创建时间"
          width="200"
        ></el-table-column>
        <el-table-column label="操作" width="150" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button
                type="text"
                size="small"
                v-if="scope.row.zt !== '已回传'"
                :disabled="disabled"
                @click.native.prevent="handleComes(scope.row)"
              >回传</el-button>
              <el-button
                type="text"
                size="small"
                v-if="scope.row.is_finished === 0"
                @click.native.prevent="handleOut(scope.row)"
              >出库</el-button>
              <!--<el-button-->
                <!--type="text"-->
                <!--size="small"-->
                <!--v-if="scope.row.is_finished === 1 &&  scope.row.pass === 0"-->
                <!--@click.native.prevent="OkOut(scope.row)"-->
              <!--&gt;确认出库</el-button>-->
              <el-button
                type="text"
                v-if="scope.row.is_finished === 1"
                size="small"
                @click.native.prevent="showDetail(scope.row)"
              >查看</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="grid-page">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page.sync ="query.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="query.limit"
        layout="total,->, prev, pager, next, jumper, sizes"
        :total="total"
      ></el-pagination>
    </div>

    <el-dialog
      title="库区"
      :visible.sync="dialogShelfVisible"
      width="50%"
      :modal-append-to-body="false"
    >
      <el-table :data="goodsList" stripe style="width: 100%">
        <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
        <el-table-column prop="stock_no" label="库区" min-width="150"></el-table-column>
        <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
        <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
        <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogShelfVisible = false">取 消</el-button>
        <el-button :disabled="buttonDisabled" type="primary" @click="OkOuts">确 定</el-button>
      </span>
    </el-dialog>
  </div>
  <!--,dialogShelfVisible = false-->
</template>

<script>
import { fetchList } from "@/api/adjust";
import { backNo } from "@/api/sales_out";
import { getByNo, adjustNo } from "@/api/out_ensure";
import { getToken } from "@/utils/auth";

export default {
  name: "rolelist",
  data: function() {
    return {
      buttonDisabled:false,
      query: {
        page: 1,
        limit: 20,
        type: "adj_out_dirt"
      },
      loading: true,
      tableData: [],
      goodsList: [],
      total: 0,
      tempRow: {},
      dialogShelfVisible: false,
      checked: [],
      disabled: false
    };
  },
  mounted: function() {
    this.loadData();
  },
  created() {
    if (localStorage.out_adjust_query) {
      this.query = JSON.parse(localStorage.out_adjust_query);
      delete localStorage.out_adjust_query;
    }
    this.handleCurrentChange(this.query.page);
  },
  methods: {
    handleComes(row){
       this.disabled = true;
       backNo({OutStcNo: row.OutStcNo,type:'adj'}).then(res =>{
          if(res.code === 200){
             this.disabled = false;
             this.$message.success("回传成功!");
             this.loadData();
          };
       }).catch(res =>{
         this.loadData();
         this.disabled = false;
       });
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
    serachTimeHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-date-picker
            v-model={this.query[column.columnKey]}
            type="datetimerange"
            range-separator=" 至 "
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            on-Change={this.loadData}
            placeholder={column.label}
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
        <el-option value="所有" label="所有" />
        <el-option value="未处理" label="未处理" />
        <el-option value="拣货中" label="拣货中" />
        <el-option value="复核中" label="复核中" />
        <el-option value="待发运" label="待发运" />
        <el-option value="发货完成" label="发货完成" />
          </el-select>
        </div>);
    },
    serachStatusZt(h, { column, $index }, index){
        return (<div class="header-custom-stype">
      <el-select
            v-model={this.query[column.columnKey]}
            on-Change={this.loadData}
            placeholder={column.label}
          >
        <el-option value="未回传" label="未回传" />
        <el-option value="已回传" label="已回传" />
          </el-select>
        </div>);
    },
    getRowKeys(row) {
      return row.AdjustNo;
    },
    loadData: function() {
      if (localStorage.out_adjust_query) {
        this.query = JSON.parse(localStorage.out_adjust_query);
        delete localStorage.out_adjust_query;
        this.handleCurrentChange(Number(this.query.page))
      }
      fetchList(this.query).then(res => {
        this.tableData = res.data.data;
        this.total = res.data.total;
      });
      this.loading = true;
      setTimeout(() => {
        this.loading = false;
        this.buttonDisabled = false;
      }, 1000);
    },
    handleSelectionChange(val) {
      this.checked = val;
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    showDetail: function(detail) {
      localStorage.out_adjust_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/outstorage_action/adjust/detail/" + detail.OutStcNo
      });
    },
    handleOut(detail) {
      localStorage.out_adjust_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/outstorage_action/adjust/out/" + detail.OutStcNo
      });
    },
    OkOut(detail) {
      this.query.id = detail.OutStcNo;
      this.tempRow = detail;
      getByNo(this.query).then(res => {
        this.goodsList = res.data;
        this.dialogShelfVisible = true;
      });
    },
    OkOuts() {
      this.buttonDisabled = true;
      this.query.id = this.tempRow.OutStcNo;
      adjustNo(this.query).then(res => {
        this.goodsList = res.data;
        this.dialogShelfVisible = false;
      });
      localStorage.out_adjust_query = JSON.stringify(this.query);
      this.loadData();
    },
    stockOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var goodsIdsList = [];
      if (this.checked.length === 0) {
        this.$message({
          message: "请选择商品！",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].AdjustNo);
      }
      window.open(
        "/api/adjust/wave?id=" + goodsIdsList + "&token=" + token[1],
        "_blank"
      );
      localStorage.out_adjust_query = JSON.stringify(this.query);
      this.loadData();
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var goodsIdsList = [];
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].AdjustNo);
      }
      window.open(
        "/api/adjust/export?id=" + goodsIdsList + "&query=" + JSON.stringify(this.query)  + "&token=" + token[1],
        "_blank"
      );
      localStorage.out_adjust_query = JSON.stringify(this.query);
      this.loadData();
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
