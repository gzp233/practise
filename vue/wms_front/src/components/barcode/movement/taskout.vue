<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <el-tabs v-model="activeName" type="border-card" @tab-click="handleClick">
        <el-tab-pane label="移库-出" name="first"></el-tab-pane>
        <el-tab-pane label="移库-入" name="second"></el-tab-pane>       
    </el-tabs>
    <div class="numbers">移库单号：{{this.$route.query.moveNumber}}</div>
    <div class="head">
      <span ref="tabTag" style="text-align:center;position: absolute;top:32px;left:10px">出库库位扫描</span>
      <el-input
        style="width:235px;position: absolute;right:0"
        v-model="tmpInput"
        class="button-item"
        placeholder="请扫描库位号"
        id="codeInput"
        @keyup.enter.native="handleAdd"
      ></el-input>
    </div>
    <div class="tip">* 红色：未开始， 黄色：进行中， 绿色：已完成</div>
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
      <el-button type="primary" v-show="sub" class="button-item next" :disabled="disabled" @click="handleyiku">提 交</el-button>
    </span>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getJianhuoStockList, doJianhuo } from "@/api/barcode";
import { getStockListByNo, getStock, submit } from "@/api/out";
export default {
  name: "scan",
  data() {
    return {
      tmpInput: "",
      navData: [],
      stockList: [],
      activeName: 'first',
      type:"",
      outList:"",
      inList:"",
      resultout:"",
      resultin:"",
      sub:false,
      disabled: false
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getStockListByNo({ "yiku_no": this.$route.query.moveNumber }).then(res => {  //移库位号 数据格式转换
      console.log(res.data);
      this.resultout = res.data.origin;
      this.resultin = res.data.to;
      this.outList = Object.keys(res.data.origin).map(key=> res.data.origin[key]);
      this.inList = Object.keys(res.data.to).map(key=> res.data.to[key]);
      //this.stockList = this.outList;
       if(Object.is(this.$route.query.tag,"in")){
          this.activeName = "second";
          this.sub = true;
          this.$refs.tabTag.innerHTML = "入库库位扫描" 
          this.stockList = this.inList; 
        }else{
          this.activeName = "first";
          this.sub = false;
          this.$refs.tabTag.innerHTML = "出库库位扫描" ;
          this.stockList = this.outList; 
        };   
    });
   
  },
  methods: {
    handleClick(tab, event) {  // tabs 切换  第一个
        if(Object.is(this.activeName,"first")){
            this.$refs.tabTag.innerHTML = "出库库位扫描" ;
            this.stockList = this.outList; 
            this.sub = false         
        };
        if(Object.is(this.activeName,"second")){  // tabs 切换  第二个
            this.$refs.tabTag.innerHTML = "入库库位扫描" 
            this.stockList = this.inList; 
            this.sub = true         
        }
    },
    handleAdd() {        // 扫描结果
    console.log(this.resultout)
        for(let i=0;i<this.stockList.length;i++){   // 赋值对应的出入库位号
          if(Object.is(this.activeName,"first")){
              this.type = "origin";              
              if(this.resultout[this.tmpInput]){ 
                  this.$router.push({
                    path: "/barcode/movement/detailout/",
                    query: { moveNumber: this.$route.query.moveNumber, stock_no: this.tmpInput }
                  }); 
                  getStock({"yiku_no":this.$route.query.moveNumber,"type":this.type,"stock_no":this.tmpInput}).then(res =>{              
                      console.log(res);                          
                  }); 
              }else{
                alert("库位码有误，请重新扫描！");
                return;
              };               
          };
         if(Object.is(this.activeName,"second")){
            this.type = "to";
            if(this.resultin[this.tmpInput]){ 
                this.$router.push({
                  path: "/barcode/movement/detailin/",
                  query: { moveNumber: this.$route.query.moveNumber, stock_no: this.tmpInput }
                });
                getStock({"yiku_no":this.$route.query.moveNumber,"type":this.type,"stock_no":this.tmpInput}).then(res =>{              
                      console.log(res);                          
                  }); 
            }else{
              alert("库位码有误，请重新扫描！");
              return;
            };
         };                                
        };           
                         
    },
    handleBack() {  //返回
      this.$router.push({
        path: "/barcode/movement/"
      });
    },
    handleyiku() {  // 提交 
      this.disabled = true; 
      submit({"yiku_no":this.$route.query.moveNumber}).then(res => {
        if(Object.is(res.code,200)){
           this.$message.success("扫描已完成");
           this.$router.push({
             path: "/barcode/movement/"
           });
        };
      }).catch(res =>{
        this.disabled = false;
      });             
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