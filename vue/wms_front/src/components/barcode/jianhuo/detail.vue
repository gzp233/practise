<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="numbers">单号：{{this.$route.query.id}}</div>
    <div class="location">库位号：{{this.$route.query.stock_no}}</div>
    <div class="detail_content">
      <div class="head">
        <span
          style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
        >库位拣货</span>
        <el-input
          style="width:260px;position: absolute;right:0"
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
        <el-table :data="records" stripe style="width: 100%;" max-height="250">
          <el-table-column
            class="button-item"
            prop="product.NewPRODUCTCD"
            label="新产品代码"
            min-width="80"
          ></el-table-column>
          <el-table-column class="button-item" prop="available_time" label="有效期" min-width="60"></el-table-column>
          <el-table-column class="button-item" prop="box" label="箱" min-width="20"></el-table-column>
          <el-table-column class="button-item" prop="surplus" label="零头" min-width="30"></el-table-column>
          <el-table-column class="button-item" prop="number" label="出库数量" min-width="70"></el-table-column>
          <el-table-column class="button-item" prop="scanNumber" label="扫描" min-width="50"></el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
          <el-button class="back" type="primary" @click="handleBack">返 回</el-button>
          <el-button class="next" type="primary" @click="doCreate">提 交</el-button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { doJianhuoStock, getJianhuoStock } from "@/api/barcode";
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
      PRODUCT: "",
      XIAOQI: {},
      count: 0
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getJianhuoStock(this.$route.query).then(res => {      
      this.removeDuplicatedItem(res.data);
      this.records = res.data;
      console.log(this.records)
    });
  },
  methods: {
    //数组去重
    removeDuplicatedItem(arr){
      for (var i = 0; i < arr.length - 1; i++) {
        for (var j = i + 1; j < arr.length; j++) {
          if (arr[i].product.PRODUCTCD == arr[j].product.PRODUCTCD && arr[i].available_time == arr[j].available_time) {
            arr[i].number = arr[i].number+arr[j].number
            arr.splice(j, 1);
            j--;
            console.log(arr);
          }
        }
      }
      return arr;
    },
    handleAdd() {
      let tit = this.tmpData.tmpInput;
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
            if (newProductCd == this.records[i].product.NewPRODUCTCD) {
              if (this.records[i].product.validity != 999) {
                xiaoqi = Number(
                  getXiaoqi(
                    tit.slice(3, 7),
                    this.records[i].product.validity
                  )
                );
                if (xiaoqi != this.records[i].available_time) {
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
              if (tit == this.records[i].product.barCode) {
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
        }
        for (let i = 0; i < this.records.length; i++) {
          if (this.form.zhima == this.records[i].product.barCode) {
             this.PRODUCT = this.records[i].product.barCode;
            this.XIAOQI = this.records[i];
            if (tit != this.records[i].available_time) {
              alert("有效期不符");
              return;
            };
            if (this.records[i].number <= this.records[i].scanNumber) {
              alert("该产品已扫描完成！");
              this.form.zhima = "";
              return;
            }; 
            this.records[i].scanNumber += 1;
            this.form.zhima = "";
            return;
          }
        }
        alert("有效期输入错误！");
      }
    },
    doCreate() {      
      doJianhuoStock(this.records).then(res => {
        this.$message({
          message: "拣货成功",
          type: "success"
        });
        this.$router.push({
          path: "/barcode/jianhuo/stockList/" + this.$route.query.id
        });
      });
    },
    handleBack() {
      this.$router.push({
        path: "/barcode/jianhuo/stockList/" + this.$route.query.id
      });
    }
  }
};
</script>

<style lang="less" scoped>
.numbers,.location{margin: 70px 0 0 0;font-size: 18px}
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