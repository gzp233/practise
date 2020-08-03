<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="numbers">单号：{{this.$route.query.moveNumber}}</div>
    <div class="location">库位号：{{this.$route.query.stock_no}}</div>
    <div class="detail_content">
      <div class="head">
        <span
          style="text-align:center;position: absolute;top:32px;left:0"
        >移出产品及效期:</span>
        <el-input
          style="width:210px;position: absolute;right:0"
          v-model="tmpData.tmpInput"
          class="button-item"
          placeholder="请扫描"
          id="codeInput"
          @keyup.enter.native="handleAdd"
        ></el-input>
      </div>
      <br>
      <span>支码：{{ form.zhima }}</span>
      <div class="show-group">
        <el-table :data="records" stripe style="width: 100%;" max-height="350">
          <el-table-column class="button-item" prop="NewPRODUCTCD" label="商品代码" min-width="80"></el-table-column>
          <el-table-column class="button-item" prop="available_time" label="效期" min-width="60"></el-table-column>
          <el-table-column class="button-item" prop="number" label="数量" min-width="50"></el-table-column>
          <el-table-column class="button-item" prop="scanNumber" label="已扫" min-width="50">
             <template slot-scope="scope">
                <el-input v-if="scope.row.COUNT == 0" :disabled="true" @keyup.native="handleChange(scope.row)" v-model="scope.row.scanNumber" placeholder="请输入数量"></el-input>
                <el-input v-if="scope.row.COUNT == 1" :disabled="false" @keyup.native="handleChange(scope.row)" v-model="scope.row.scanNumber" placeholder="请输入数量"></el-input>
            </template>
          </el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
          <el-button class="back" type="primary" @click="handleBack">返 回</el-button>
          <el-button class="next" type="primary" :disabled="disabled" @click="doCreate">提 交</el-button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { doJianhuoStock, getJianhuoStock } from "@/api/barcode";
import { getOriginByStockNo, stockOrigin } from "@/api/out";
import { getXiaoqi } from "@/utils/common";
export default {
  name: "scan",
  data() {
    return {
      navData: [],
      records: [],
      tmpData: { tmpInput: "" },
      form: {
        zhima: ""
      },
      ObjData:{},
      PRODUCT: "",
      XIAOQI: {},
      count: 0,
      disabled: false
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getOriginByStockNo({"yiku_no":this.$route.query.moveNumber,"stock_no":this.$route.query.stock_no}).then(res => {      
      this.records = Object.keys(res.data).map(key=> res.data[key]);
      console.log(res,this.records);
      for(let i=0;i<this.records.length;i++){
        this.$set(this.records[i],"COUNT",0);
      };
    });
  },
  methods: { 
    handleChange(row){
      row.scanNumber = row.scanNumber.replace(/[^0-9]/g, '');
      if(row.scanNumber == ""){
        row.scanNumber = 0;
      }
    },
    handleAdd() {
      let tit = this.tmpData.tmpInput;
      console.log(tit)
      setTimeout(() => {
        this.$set(this.tmpData, "tmpInput", "");
      }, 100);
      if (!this.form.zhima) {
        if (tit.length != 23 && tit.length != 13 && tit.length != 14 && tit.length != 12) {
          alert("型号码23位,单只码12,13位或14位！");
          return;
        }
        if (tit.length == 23) {
          let newProductCd = tit.slice(9, 20);
          let number = Number(tit.slice(20, 23));
          let xiaoqi = "";
          // 验证
          for (let i = 0; i < this.records.length; i++) {
            xiaoqi = Number(
                getXiaoqi(
                  tit.slice(3, 7),
                  this.records[i].validity
                )
              );
            if (newProductCd == this.records[i].NewPRODUCTCD && xiaoqi == this.records[i].available_time) {
              if (this.records[i].validity != 999) {                
                if (xiaoqi != this.records[i].available_time) {
                  console.log(xiaoqi,this.records[i].available_time)
                  alert("该产品效期不符！");
                  return;
                }
              }

              if (this.records[i].number <= this.records[i].scanNumber) {
                alert("该产品已扫描完成！");
                return;
              }
              if (
                this.records[i].number <
                this.records[i].scanNumber + number
              ) {
                alert("扫描数量超过出库数量");
                return;
              }
              this.records[i].scanNumber += number;
              this.records[i].COUNT = 1;
              return;
            }
          }
          alert("该产品不在该单出库库位上！");
        } else {
          if (tit.length == 12 || tit.length == 13 || tit.length == 14) {
            for (let i = 0; i < this.records.length; i++) {
              if(this.count == 1){
                 this.form.zhima = tit;
                 this.count = 2;
                 return;
              };
              if(this.count == 2){
                 if(tit == this.PRODUCT) {
                    if (this.XIAOQI.number <= this.XIAOQI.scanNumber) {
                      this.$message({
                        type: "warning",
                        message: "该产品已扫描完成！"
                      });
                      this.count = 1;
                      return;
                    };
                    this.XIAOQI.scanNumber +=1;
                    this.form.zhima = "";
                    return;
                  };  
              };
              if(tit == this.PRODUCT) {
                if (this.XIAOQI.number <= this.XIAOQI.scanNumber) {
                  this.$message({
                    type: "warning",
                    message: "该产品已扫描完成！"
                  });
                  this.count = 1;
                  return;
                };
                this.XIAOQI.scanNumber +=1;
                this.form.zhima = "";
                return;
              };              
              if (tit == this.records[i].barCode) {
                if (this.records[i].number <= this.records[i].scanNumber) {
                  this.$message({
                    type: "warning",
                    message: "该产品已扫描完成！"
                  });
                  return;
                }
                this.form.zhima = tit;                
                return;
              };
            }
            alert("该产品不在该单出库库位上！");
          }
        }
      } else {
        if (tit.length != 6) {
          alert("有效期只能是6位！");
          return;
        };
        for (let i = 0; i < this.records.length; i++) {
          if (this.form.zhima == this.records[i].barCode && tit == this.records[i].available_time) {            
           this.PRODUCT = this.records[i].barCode;
            this.XIAOQI = this.records[i];
           if (this.records[i].number <= this.records[i].scanNumber) {
              alert("该产品已扫描完成！");
              return;
            };            
            this.records[i].scanNumber += 1;
            this.records[i].COUNT = 1;
            this.form.zhima = "";
            return;
          }
        }
        alert("有效期输入错误！");
      }
    },
    doCreate() { 
      this.disabled = true;
      this.ObjData.yiku_no = this.$route.query.moveNumber;
      this.ObjData.stock_no = this.$route.query.stock_no;
      this.ObjData.params = this.records;
      stockOrigin(this.ObjData).then(res => {         
        if(Object.is(res.code,200)){          
          this.$message({
            message: "出库成功",
            type: "success"
          });
          this.$router.push({
            path: "/barcode/movement/createout",
            query:{moveNumber:this.$route.query.moveNumber}
          });
        };
      }).catch(res =>{
        this.disabled = false;
      });
    },
    handleBack() {
      this.$router.push({
        path: "/barcode/movement/createout",
        query:{moveNumber:this.$route.query.moveNumber}
      });
    }
  }
};
</script>

<style lang="less" scoped>
.numbers,.location{margin: 70px 0 0 0}
.location{margin: 20px 0 0 0;}
.next,.back{width: 30%!important;}
.next{margin: 0 0 0 38%!important}
.detail_content {
  height: auto;
}
.layout-header {
  position: fixed;
}
.dialog-footer {
  margin: 35px 0 0 0;
  display: block;
}
.head {
  width: 100%;
  position: relative;
  height: 60px;
}
.input-group {
  margin: 50px 10px 10px 10px;
  max-width: 750px;
  font-size: 16px;
  height: 100%;
}
.input-group .button-item {
  height: 30px;
  margin: 20px 0;
  font-size: 16px;
}
.input-groud /deep/ .el-input__inner[placeholder="请扫描"] {
  height: 150px !important;
}
.input-groud /deep/ .el-input__inner[placeholder="请扫描"] {
  height: 150px !important;
}
.show-group {
  margin: 30px auto;
  width: 100%;
  font-size: 16px;
}
.show-group .button-item {
  height: 30px;
  margin: 100px 0;
  font-size: 500px;
}
</style>
<style lang="less">
/*.el-input__inner{*/
/*height: 80px !important;*/
/*width: 500px !important;*/
/*}*/
.el-table th > .cell {
  font-size: 14px;
}
.el-table .cell,
.el-table th > div {
  font-size: 14px;
  padding: 0;
  text-align: center;
}
.el-table__header-wrapper {
  height: 45px !important;
}
.el-pagination__total {
  margin: 33px 0 0 0;
}
.btn-prev {
  font-size: 22px !important;
}
.el-pagination__editor {
  font-size: 14px !important;
  padding: 3px 2px;
}
.el-pagination__jump {
  font-size: 14px !important;
  margin: 0 10px;
}
.el-input/deep/ .el-input__inner {
  height: 27px !important;
}
.button-item /deep/.el-input__inner {
  height: 40px !important;
}
.el-pagination {
  padding: 10px 5px;
}
.el-pagination__sizes {
  margin: 0 0 0 20px;
}
</style>
