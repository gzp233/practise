<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="head">
      <span ref="tabTag" style="text-align:center;position: absolute;top:32px;left:10px">出库库位扫码</span>
      <el-input
        style="width:235px;position: absolute;right:0"
        v-model="tmpInput"
        class="button-item"
        placeholder="请扫描库位号"
        id="codeInput"
        @keyup.enter.native="handleAdd"
      ></el-input>
    </div>
    <div class="status_scroll">
      <ul id="status" class="clearfloat">
        <li class="green"
          v-for="(item, index) in stockList"
          :key="index"
        >{{ item }}</li>
      </ul>
    </div>
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" class="button-item back" @click="handleBack">返 回</el-button>
      <el-button type="primary" class="button-item next" :disabled="disabled" @click="handleyiku">提 交</el-button>
    </span>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getJianhuoStockList, doJianhuo } from "@/api/barcode";
import { getStockListByNo, getStock, submit,getOriginStockList,submitOrigin } from "@/api/out";
import { getLocations } from "@/api/location";
export default {
  name: "scan",
  data() {
    return {
      tmpInput: "",
      navData: [],
      stockList: [],
      activeName: 'first',
      area_ids:["1","2","3","4","5"],
      disabled: false
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getOriginStockList({ "yiku_no": this.$route.query.moveNumber }).then(res => {  //移库位号 数据格式转换
      this.stockList = res.data;
      console.log(res)
    });
  },
  methods: {
    handleAdd() {        // 扫描结果
        let re = /^([A-Z0-9]{3})-([0-9]{2})-([0-9]{2})$/;
        getLocations({area_ids: this.area_ids,"stock_no":this.tmpInput}).then(res =>{
          if(res.data.length == 0){
              this.$message({
                type:"warning",
                message:"库位码有误！"
              });
              this.tmpInput="";
              return;
          }else{
             if(Object.is(this.tmpInput,res.data[0].stock_no)){
                if(!re.test(this.tmpInput)){
                  this.$message({
                    type:"warning",
                    message:"格式有误！"
                  });
                  this.tmpInput="";
                  return;
                }else{
                  this.$router.push({
                      path: "/barcode/movement/createoutdet/",
                      query: { moveNumber: this.$route.query.moveNumber, stock_no: this.tmpInput }
                  });
                  this.tmpInput = "";
                };
              };
          };
        });
    },
    handleBack() {  //返回
      this.$router.push({
        path: "/barcode/movement/"
      });
    },
    handleyiku() {  // 提交
        if(this.stockList.length >= 1){  // 至少一个已完成
           this.disabled = true;
           submitOrigin({"yiku_no":this.$route.query.moveNumber}).then(res => {
             if(Object.is(res.code,200)){
                this.$router.push({
                  path: "/barcode/movement/createin/",
                  query:{moveNumber: this.$route.query.moveNumber}
                });
             };
           }).catch(res =>{
             this.disabled = false;
           });
        }else{
           this.disabled = true;
           this.$message.warning("未扫描完");
           this.disabled = false;
           return;
        }
    }
  }
};
</script>

<style>
.el-tabs__nav{width: 100%!important;}
.el-tabs--border-card>.el-tabs__header .el-tabs__item{width:50%!important;text-align: center}
.el-tabs--border-card{border: none;box-shadow:none;background:#f0f0f0;}
.el-tabs--border-card>.el-tabs__content {padding: 8px;}
</style>
<style lang="less" scoped>
.status_scroll{max-height: 280px;overflow-y: scroll;}
/*清除浮动*/
.clearfloat:after{display:block;clear:both;content:"";visibility:hidden;height:0}
.clearfloat{zoom:1}
.status_scroll{max-height: 280px;overflow-y: scroll;}
.numbers{margin: 0 0 0 10px;}
.tip{color:red;font-size: 14px;margin: 17px 0 0 10px;}
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
  width: 100%;
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
