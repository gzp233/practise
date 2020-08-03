<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="head">
      <span
        style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
      >出库单号</span>
      <el-input
        style="width:260px;position: absolute;right:0"
        v-model="tmpInput"
        class="button-item"
        placeholder="请扫描单号"
        id="codeInput"
        @keyup.enter.native="handleAdd"
      ></el-input>
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" class="button-item" @click="doCreate">返 回</el-button>
    </span>
    <div class="tip">* 红色：失败， 黄色：发送中</div>
    <el-table
      :data="tableData"
      v-loading.body="loading"
      :row-class-name="tableRowClassName"
      style="width: 100%"
    >
      <el-table-column prop="SHIPMENTID" label="调度单号" width="120"></el-table-column>
      <el-table-column prop="CUSTOMERNAME" label="客户名称"></el-table-column>
    </el-table>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getList, checkFangchuanhuoOrder } from "@/api/fangchuanhuo";
import { clearTimeout } from 'timers';
export default {
  name: "scan",
  data() {
    return {
      tmpInput: "",
      loading: false,
      tableData: [],
      navData: []
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    this.loadData();
  },
  methods: {
    tableRowClassName(row) {
      if (row.status == '失败') {
        return "fail-row";
      } else if (row.status == '发送中') {
        return "sending-row";
      }
      return "";
    },
    loadData() {
      getList().then(res => {
        this.tableData = res.data;
      });
    },
    handleAdd() {
      let tit = this.tmpInput;
      setTimeout(() => {
        this.tmpInput = "";
      }, 50);      
      if (tit.length != 10) {
        alert("出库编号必须是十位！");
        return;
      }
      checkFangchuanhuoOrder({ id: tit }).then(res => {
        this.$router.push({
          path: "/barcode/fangchuanhuo/detail/" + tit,
          query: { type: res.data }
        });
      });
    },
    doCreate() {
      this.$router.push({
        path: "/barcode/menu/"
      });
    }
  }
};
</script>
<style>
.el-table--enable-row-hover .el-table__body tr:hover>td{
background-color: transparent !important;
}
</style>
<style lang="less" scoped>
.tip{color:red;font-size: 14px;margin: 17px 0;}
.layout-header {
  position: fixed;
}
.head {
  width: 100%;
  position: relative;
  height: 60px;
}
h1 {
  font-size: 26px;
  text-align: center;
}
.input-group {
  margin: 50px auto;
  max-width: 750px;
  width: 94%;
}
.input-group .button-item {
  margin: 20px 0;
  width: 100%;
}
</style>

<style lang="less">
.el-input__inner {
  height: 40px !important;
  font-size: 16px;
}
.el-table .fail-row {
  background: rgb(219, 101, 101);
  color:#fff;
}

.el-table .sending-row {
  background: rgb(219, 219, 113);
  color:#fff;
}
</style>