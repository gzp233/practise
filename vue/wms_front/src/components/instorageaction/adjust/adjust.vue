<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="exportOut">导出</button>
    </div>
    <div class="grid-content">
      <el-table :data="tableData" v-loading.body="loading" :row-key="getRowKeys" @selection-change="handleSelectionChange" height="800" stripe border style="width: 100%">
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
          label="入库库位代码"
          width="150"
        ></el-table-column>
        <el-table-column prop="ShopSignNM" label="商店" width="200"></el-table-column>
        <el-table-column prop="DeliverAdd" label="送货地" width="200"></el-table-column>
        <el-table-column
                prop="state"
                :render-header="serachStatusHeader"
                column-key="state"
                label="状态"
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
                @click.native.prevent="getDoc(scope.row)"
                v-if="scope.row.is_finished == 0"
              >入库单</el-button>
              <el-button
                type="text"
                size="small"
                @click.native.prevent="handleStockIn(scope.row)"
                v-if="scope.row.is_finished == 0"
              >入库</el-button>
              <el-button
                type="text"
                size="small"
                @click.native.prevent="handleConfirm(scope.row)"
                v-if="scope.row.is_confirmed == 0"
              >确认</el-button>
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
  </div>
</template>

<script>
import { fetchList } from "@/api/adjust_in";
import { getToken } from "@/utils/auth";

export default {
  name: "rolelist",
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20
      },
      loading: true,
      tableData: [],
      total: 0,
      checked: []
    };
  },
  mounted: function() {
    this.loadData();
  },
  created() {
    if (localStorage.in_adjust_query) {
      this.query = JSON.parse(localStorage.in_adjust_query);
      delete localStorage.in_adjust_query;
    }
    this.handleCurrentChange(this.query.page);
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
    <el-option  value="未入库" label="未入库" />
              <el-option value="入库中" label="入库中" />
              <el-option  value="已入库" label="已入库" />
              <el-option  value="已回传" label="已回传" />
              </el-select>
              </div>);
    },
    loadData: function() {
      if (localStorage.in_adjust_query) {
        this.query = JSON.parse(localStorage.in_adjust_query);
        delete localStorage.in_adjust_query;
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
    showDetail: function(detail) {
      localStorage.in_adjust_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/instorage_action/adjust/detail/" + detail.AdjustNo
      });
    },
    handleStockIn(detail) {
      localStorage.in_adjust_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/instorage_action/adjust/stockIn/" + detail.AdjustNo
      });
    },
    getRowKeys(row) {
      return row.AdjustNo;
    },
    handleConfirm(detail) {
      localStorage.in_adjust_query = JSON.stringify(this.query);
      this.$router.push({
        path: "/instorage_action/adjust/confirm/" + detail.AdjustNo
      });
    },
    handleSelectionChange(val) {
      this.checked = val;
    },
    getDoc(row) {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust_in/exportDoc?id=" + row.AdjustNo + "&token=" + token[1],
        "_blank"
      );
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var goodsIdsList = [];
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].AdjustNo);
      }
      window.open(
              "/api/adjust_in/export?id=" + goodsIdsList+ "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
              "_blank"
      );
      localStorage.in_adjust_query = JSON.stringify(this.query);
      this.loadData()
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
