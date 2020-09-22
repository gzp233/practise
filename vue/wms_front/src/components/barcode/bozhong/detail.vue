<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="numbers">单号：{{this.$route.query.id}}</div>
    <div class="location">新产品代码：{{this.$route.query.stock_no}}</div>
    <div class="detail_content">
      <div class="head">
        <span
          style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
        >请扫描单号</span>
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
        <el-table :data="records"  stripe style="width: 100%;" max-height="250" >
           <el-table-column
            class="button-item"
            prop="odd"
            label="单号"            
            min-width="70"
          ></el-table-column>
          <el-table-column class="button-item" prop="scanNumber" label="扫描单号" min-width="50"></el-table-column>
           <el-table-column
                  class="button-item"
                  prop="available_time"
                  label="有效期"
                  min-width="50"
          ></el-table-column>
          <el-table-column
                  class="button-item"
                  label="系统数量"
                  min-width="60">
          <template slot-scope="scope">
                <div class="table-form-item">
                  <span>{{leaves(scope.row.id)}}</span>
                </div>
              </template>
              </el-table-column>
          <el-table-column
                  class="button-item"
                  label="已播种"
                  min-width="60">
                  <template slot-scope="scope">
                     <el-input type="text" @keyup.native.prevent="inputKeyup(scope.row)" v-model="scope.row.WriteNumber" placeholder="请输入数量"></el-input>
                  </template>
          </el-table-column>          
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
import { doBozhongStock, getbozhongStock} from "@/api/bozhong";
export default {
  name: "scan",
  data() {
    return {
      navData: [],
      records: [],
      tmpData: { tmpInput: "" },
      form: {
        zhima: ""
      }
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    getbozhongStock(this.$route.query).then(res => {
      this.records = res.data;     
    });
  },
  computed: {
    leaves() {
      return id => {
        let total = 0;
        for (let k = 0; k < this.records.length; k++) {
          if (this.records[k].id == id) {
            total = this.records[k].number;
          }
        }        
        return total;
      };
    }    
  },
  methods: {
    foo ({row, column, rowIndex, columnIndex}){
      console.log(row, column, rowIndex, columnIndex)
       if(columnIndex === 1) {
         return {
           width: '45px',
           margin: '0 0 0 11px',
           lineHeight: '17px'
         }
       }
    },    
    inputKeyup(row) {
      row.WriteNumber=row.WriteNumber.replace(/[^\d]/g,'')
      let leaves = this.leaves(row.id);      
      if (leaves < row.WriteNumber) {
        this.$message({
          message: "数量不能超过系统数量",
          type: "warning"
        });
        row.WriteNumber = leaves;
      }
    },
    handleAdd() {
      let tit = this.tmpData.tmpInput;
      setTimeout(() => {
        this.$set(this.tmpData, "tmpInput", "");
      }, 100);
      if (!this.form.zhima) {
        if (tit.length != 10) {
          alert("单号只能是10位");
          return;
        }
//        console.log(this.records)
        var isthis = 0;
        for(let i=0;i<this.records.length;i++){
          if(this.tmpData.tmpInput == this.records[i].odd){
              isthis = 1
              this.records[i].scanNumber = this.tmpData.tmpInput
          }
        }
        if(isthis == 0){
          alert("单号不存在");
        }
      }
    },
    doCreate() {
      doBozhongStock(this.records).then(res => {
        this.$message({
          message: "拣货成功",
          type: "success"
        });
        this.$router.push({
          path: "/barcode/bozhong/stockList/" + this.$route.query.id
        });
      });
    },
    handleBack() {
      this.$router.push({
        path: "/barcode/bozhong/stockList/" + this.$route.query.id
      });
    }
  }
};
</script>
<style>
.el-table .cell{line-height: 17px!important}
.el-table .el-table__body-wrapper .el-table_1_column_2 .cell{width: 45px;margin: 0 0 0 11px;line-height:17px}
</style>

<style lang="less" scoped>
.numbers,.location{margin: 70px 0 0 0;font-size: 18px}
.location{margin: 20px 0 0 0;}
.next,.back{width: 30%!important;}
.next{margin: 0 0 0 38%!important}
.col{width: 45px;margin: 0 0 0 11px;line-height:17px}

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
