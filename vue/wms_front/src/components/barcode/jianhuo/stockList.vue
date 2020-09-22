<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="numbers">单号：{{this.$route.params.id}}</div>
    <div class="head">
      <span
        style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
      >库位扫描</span>
      <el-input
        style="width:260px;position: absolute;right:0"
        v-model="tmpInput"
        class="button-item"
        placeholder="请扫描库位号"
        id="codeInput"
        @keyup.enter.native="handleAdd"
      ></el-input>
    </div>
    <div class="tip">* 红色：未拣货， 黄色：拣货中， 绿色：已拣货</div>
    <div class="status_scroll">
      <ul id="status" class="clearfloat">
        <li class="yellow"
          v-if="item.status == 1"
          v-for="(item, index) in stockList"
          :key="index"
        >{{ item.stock_no }} </li>
        <li class="red"
          v-if="item.status == 0"
          v-for="(item, index) in stockList"
          :key="index"
        >{{ item.stock_no }} </li>
        <li class="green"
          v-if="item.status == 2"
          v-for="(item, index) in stockList"
          :key="index"
        >{{ item.stock_no }}</li>
      </ul>
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" class="button-item back" @click="handleBack">返 回</el-button>
      <el-button type="primary" class="button-item next" @click="handleJianhuo">提 交</el-button>
    </span>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getJianhuoStockList, doJianhuo } from "@/api/barcode";
export default {
  name: "scan",
  data() {
    return {
      tmpInput: "",
      navData: [],
      stockList: []
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getJianhuoStockList({ id: this.$route.params.id }).then(res => {
      console.log(res.data)
      this.stockList = res.data;
    });
  },
  methods: {
    handleAdd() {
      console.log(this.stockList)
      if (this.stockList[this.tmpInput]) {
        this.$router.push({
          path: "/barcode/jianhuoDetail/",
          query: { id: this.$route.params.id, stock_no: this.tmpInput }
        });
        return;
      }
      alert("库位码有误，请重新扫描！");
      this.tmpInput = "";
    },
    handleBack() {
      this.$router.push({
        path: "/barcode/jianhuo/"
      });
    },
    handleJianhuo() {
      doJianhuo({ id: this.$route.params.id }).then(res => {
        this.$message({
          message: "拣货完成",
          type: "success"
        });
        this.$router.push({
          path: "/barcode/jianhuo/"
        });
      });
    }
  }
};
</script>


<style lang="less" scoped>
/*清除浮动*/
.clearfloat:after{display:block;clear:both;content:"";visibility:hidden;height:0}
.clearfloat{zoom:1}
.status_scroll{max-height: 280px;overflow-y: scroll;}
.numbers{margin: 70px 0 0 0;font-size: 18px}
.tip{color:red;font-size: 14px;margin: 17px 0 0 0;}
.red{background: rgb(219, 101, 101);}
.yellow{background: rgb(219, 219, 113);}
.green{background: rgb(108, 201, 108);}
#status li{float: left;text-align: center;list-style: none;font-size: 14px;padding:10px 15px;width: 20%;margin:20px 5px 0px 5px;color:white;box-shadow: 1px 1px 2px 2px rgb(202, 202, 202);border-radius: 6px}
.next,.back{width: 30%!important;}
.next{margin: 0 0 0 38%!important}
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
  margin: 20px 0 0 0;
  width: 100%;
}
</style>

<style lang="less">
.el-input__inner {
  height: 40px !important;
  font-size: 16px;
}
</style>