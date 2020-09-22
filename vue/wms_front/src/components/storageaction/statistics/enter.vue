<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <!--<button class="tool-btn btn-add" @click="stockOut">集货</button>-->
      <button class="tool-btn btn-add" @click="exportOut">导出</button>
    </div>
    <div class="grid-content">
      <el-table :data="tableData"
       ref="multipleTable"
       v-loading.body="loading" height="800" @selection-change="handleSelectionChange" stripe border style="width: 100%">
        <el-table-column type="selection" :reserve-selection="true" width="55"></el-table-column>
        <el-table-column prop="PRODCHINM" label="品名" width="200"></el-table-column>
        <el-table-column
          prop="PRODUCTCD"
          :render-header="serachHeader"
          column-key="PRODUCTCD"
          label="产品代码"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="odd"
          :render-header="serachHeader"
          column-key="odd"
          label="发票号"
          width="150"
        ></el-table-column>
        <el-table-column prop="number" label="数量" width="80"></el-table-column>
        <el-table-column
          prop="state_name"
          :render-header="serachHeader"
          column-key="state_name"
          label="库位号"
          width="100"
        ></el-table-column>
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
  </div>
</template>

<script>
import { enter } from "@/api/statistics";
import { getToken } from "@/utils/auth";
export default {
  name: "location",
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20
      },
      total: 0,
      loading: true,
      tableData: [],
       checked: [],
      dialogVisible: false,
      detail: {}
    };
  },
  mounted: function() {
    this.loadData();
  },
  inject: ['reload'],
  methods: {
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
    loadData: function() {
      this.loading = true;
      enter(this.query).then(response => {
        this.total = response.data.total;
        this.tableData = response.data.data;
      });

      setTimeout(() => {
        this.loading = false;
      }, 2000);
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
      this.detail = detail;
      this.dialogVisible = true;
    },
     handleSelectionChange(val) {
       this.checked = val;
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var goodsIdsList = [];
      for (let i = 0; i < this.checked.length; i++) {
        goodsIdsList.push(this.checked[i].id);
      }
      let tableDataList = [];
      for (let i = 0; i < this.tableData.length; i++) {
        tableDataList.push(this.tableData[i].id);
      };      
      delete this.query.page;
      delete this.query.limit;
      if(Array.isArray(this.checked) && this.checked.length !== 0){  // 勾选商品
          window.open(
            "/api/statistics/export?id=" + goodsIdsList + "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
            "_blank"
          );
      }else{  // 没有勾选商品
         if(!this.query.PRODUCTCD && !this.query.odd && !this.query.state_name
         && !this.query.created_at){
            if(this.checked.length === 0){
                this.$message({
                  message: "请选择商品或搜索商品！",
                  type: "warning"
                });
                return;
            };
         }else{                      
          if(goodsIdsList){
                window.open(
                  "/api/statistics/export?query=" + JSON.stringify(this.query) + "&token=" + token[1],
                  "_blank"
                );  
            }else{       
                 window.open(
                  "/api/statistics/export?id=" + tableDataList + "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
                  "_blank"
                ); 
            };   
         }
      };         
      localStorage.sellOut_query = JSON.stringify(this.query);
      //清空所有的勾选状态
      this.$refs.multipleTable.clearSelection()
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
