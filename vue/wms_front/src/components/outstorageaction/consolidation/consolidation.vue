<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="OkOut">集货</button>
      <button class="tool-btn btn-add" @click="OkList">集货列表</button>
    </div>
    <div class="grid-content">
      <el-table
              :data="tableData"
              v-loading.body="loading"
              @selection-change="handleSelectionChange"
              border
              stripe
              height="800"
              :row-key="getRowKeys"
              style="width: 100%"
      >
        <el-table-column type="selection" :reserve-selection="true" width="55"></el-table-column>
        <el-table-column
                prop="vbeln"
                :render-header="serachHeader"
                column-key="vbeln"
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
                :render-header="serachTimeHeader"
                column-key="created_at"
                prop="created_at"
                label="创建时间"
                width="200"
        ></el-table-column>
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

    <el-dialog
            title="集货"
            :visible.sync="dialogShelfVisible"
            width="50%"
            :modal-append-to-body="false"
    >
      <el-table :data="goodsList" stripe style="width: 100%">
        <el-table-column prop="clientele" label="客户数" min-width="150"></el-table-column>
        <el-table-column prop="indent" label="订单数" min-width="150"></el-table-column>
        <el-table-column prop="storage" label="库位数" min-width="150"></el-table-column>
        <el-table-column prop="type" label="型号数" min-width="150"></el-table-column>
        <el-table-column prop="number" label="支数" min-width="150"></el-table-column>
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogShelfVisible = false">取 消</el-button>
        <el-button type="primary" @click="stockOut">确 定</el-button>
      </span>
    </el-dialog>

  </div>
  <!--,dialogShelfVisible = false-->
</template>

<script>
  import { fetchList ,getByNo,getList} from "@/api/consolidation";
  import {  adjustNo } from "@/api/out_ensure";
  import { getToken } from "@/utils/auth";

  export default {
    name: "rolelist",
    data: function() {
      return {
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
        checked: []
      };
    },
    mounted: function() {
      this.loadData();
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
        on-Change={this.loadData}
      />
      </div>
      );
      },
      loadData: function() {
        fetchList(this.query).then(res => {
        this.tableData = res.data;
        this.total = res.data.total;
      });
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
      }, 1000);
      },
      handleSelectionChange(val) {
        this.checked = val;
      },
      handleSizeChange: function(val) {
        this.query.limit = val;
        this.loadData();
      },
      getRowKeys(row) {
        return row.sapNo;
      },
      handleCurrentChange: function(val) {
        this.query.page = val;
        this.loadData();
      },
      showDetail: function(detail) {
        this.$router.push({
          path: "/outstorage_action/adjust/detail/" + detail.OutStcNo
        });
      },
      handleOut(detail) {
        this.$router.push({
          path: "/outstorage_action/adjust/out/" + detail.OutStcNo
        });
      },
      OkOuts() {
        this.query.id = this.checked;
        adjustNo(this.query).then(res => {
          this.goodsList = res.data;
        this.dialogShelfVisible = false;
      });
        this.loadData(this.$route.params.id);
      },
      stockOut(){
        var goodsIdsList = [];
        for (let i = 0; i < this.checked.length; i++) {
          goodsIdsList.push(this.checked[i].vbeln);
        }
        getList(goodsIdsList).then(res => {
          this.$message({
          message: "创建成功",
          type: "success"
        });
        this.loadData(this.$route.params.id);
        this.dialogShelfVisible = false;
      });
      },
      OkList(){
        this.$router.push({
          path: "/outstorage_action/detail"
        });
      },

      stockOutsss() {
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
          goodsIdsList.push(this.checked[i].vbeln);
        }
        window.open(
                "/api/consolidation/wave?id=" + goodsIdsList + "&token=" + token[1],
                "_blank"
        );
        this.$router.push({
          query: { goodsIdsList: goodsIdsList }
        });
      },
      exportOut() {
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
          goodsIdsList.push(this.checked[i].vbeln);
        }

        window.open(
                "/api/adjust/export?id=" + goodsIdsList + "&token=" + token[1],
                "_blank"
        );
        this.$router.push({
          query: { goodsIdsList: goodsIdsList }
        });
      },

      OkOut() {
        var goodsIdsList = [];
        if (this.checked.length === 0) {
          this.$message({
            message: "请选择商品！",
            type: "warning"
          });
          return;
        }
        for (let i = 0; i < this.checked.length; i++) {
          goodsIdsList.push(this.checked[i].vbeln);
        }
        getByNo(goodsIdsList).then(res => {
        this.goodsList = res.data;
        this.dialogShelfVisible = true;
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
  .el-dialog--dl-small {
    width: 600px;
  }
</style>
