<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="numbers">单号：{{this.$route.params.id}}</div>

    <div class="show-group">
      <el-table :data="pageCodes" stripe style="width: 100%;" max-height="150">
        <el-table-column class="button-item" prop="box_code" label="箱号" width="50"></el-table-column>
        <el-table-column class="button-item" prop="type_code" label="型号码" min-width="100"></el-table-column>
        <el-table-column class="button-item" prop="code" label="防串货代码" width="100"></el-table-column>
        <el-table-column class="button-item" label="操作" width="75">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="delCode(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="query.page" :page-size="query.limit" layout="prev, pager, next" :total="codes.length"></el-pagination>
    </div>

    <div class="detail_content">
      <div class="head">
        <span
          style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
        >防串货</span>
        <el-input
          style="width:260px;position: absolute;right:0"
          v-model="tmpInput"
          class="button-item"
          placeholder="请扫描"
          id="codeInput"
          @keyup.enter.native="handleAdd"
        ></el-input>
      </div>
      <br>
      <span style="margin-left:10px;">
        箱码:
        <span>{{ form.box_code }}</span>
      </span>
      <span style="margin-left:10px;">
        型号码:
        <span>{{ form.type_code }}</span>
      </span>
      <span slot="footer" class="dialog-footer">
        <el-button class="back" type="primary" @click="handleBack">返 回</el-button>
        <el-button class="next" type="primary" :disabled="buttonDisabled" @click="doCreate">提 交</el-button>
      </span>

      <div class="show-group">
        <el-table :data="codeGoods" stripe style="width: 100%" max-height="300" v-if="status == 0">
          <el-table-column class="button-item" prop="NewProductCd" label="新产品代码" min-width="100"></el-table-column>
          <el-table-column class="button-item" prop="number" label="出库数量" min-width="50"></el-table-column>
          <el-table-column class="button-item" label="扫描数量" min-width="150">
            <template slot-scope="scope">{{ scanNum(scope.row.NewProductCd) }}</template>
          </el-table-column>
        </el-table>

        <el-table
          :data="failedCodes"
          stripe
          style="width: 100%"
          max-height="250"
          v-if="status == 1"
        >
          <el-table-column class="button-item" prop="box_code" label="箱号"></el-table-column>
          <el-table-column class="button-item" prop="type_code" label="型号码"></el-table-column>
          <el-table-column class="button-item" prop="QRCODE" label="防串货代码"></el-table-column>
          <el-table-column class="button-item" prop="error" label="报错原因"></el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getByOrderNo, submit,setRedis } from "@/api/fangchuanhuo";
import { clearTimeout } from 'timers';
export default {
  name: "scan",
  data() {
    return {
      buttonDisabled: false,
      status: 0,
      failedCodes: [],
      codeGoods: [],
      navData: [],
      codes: [],
      tmpInput: "",
      boxes: [],
      otherBoxes: [],
      query: {
        limit: 10,
        page: 1
      },
      form: {
        NewProductCd: "",
        box_code: "",
        type_code: "",
        number: "",
        type: "",
        last_box_code: 0
      }
    };
  },
  components: {
    "v-header": appHeader
  },
  computed: {
      pageCodes() {
      let offset = this.query.limit * (this.query.page - 1);

      return this.codes.filter(data => !this.search || data.type_code.includes(this.search)).slice(offset, offset + this.query.limit);
    },
    scanNum(id) {
      return id => {
        // 统计每个型号的数量
        if(this.codes.length === 0){

        }else{
          var ana = {};
          for (let i = 0; i < this.codes.length; i++) {
            if (this.codes[i].NewProductCd == id) {
              if (ana[id]) {
                ana[id] += this.codes[i].number;
              } else {
                ana[id] = this.codes[i].number;
              }
            }
          }
          if (ana[id]) return ana[id];
          return 0;
        };
      };
    },
    scanNum2(id) {
      return id => {
        // 统计每个型号的数量
        if(this.codes.length === 0){

        }else{
          var ana = {};
          for (let i = 0; i < this.codes.length; i++) {
            if (this.codes[i].NewProductCd == id) {
              if (ana[id]) {
                ana[id] += this.codes[i].number;
              } else {
                ana[id] = this.codes[i].number;
              }
            }
          }
          if (ana[id]) return ana[id];
          return 0;
        };
      };
    }
  },
  mounted() {
    let query = { id: this.$route.params.id, type: this.$route.query.type };
    this.loadData(query);
  },
  methods: {
    loadData(query) {
      getByOrderNo(query)
        .then(res => {
          this.codeGoods = res.data.data;
          this.failedCodes = res.data.fails;
          this.status = res.data.status;
          this.codes = res.data.reids;
        })
        .catch(err => {
          this.$router.push({
            path: "/barcode/fangchuanhuo/"
          });
        });
    },
    handleAdd() {
      let tit = this.tmpInput;
      clearTimeout(setTime);
      let setTime = setTimeout(() => {
        this.tmpInput = "";
      }, 30);
      if (!this.form.box_code) {
        if (!this.form.last_box_code) {
          if (tit.length > 5) {
            alert("箱码不超过5位！");
            return;
          } else {
            if (this.boxes.indexOf(tit) >= 0) {
              alert("箱码已存在！");
              return;
            }
            this.form.box_code = tit;
            this.form.last_box_code = tit;
            return;
          }
        } else {
          if (tit.length <= 5) {
            if (this.boxes.indexOf(tit) >= 0) {
              alert("箱码已存在！");
              return;
            }
            this.form.box_code = tit;
            this.form.last_box_code = tit;
            return;
          }
        }
      }

      if (!this.form.type_code) {
        if (tit.length != 23 && tit.length != 12 && tit.length != 13 && tit.length != 14) {
          alert("型号码23位,单只码12位或13位或14位！");
          return;
        }

        if (tit.length == 23) {
          let NewProductCd = tit.slice(9, 20);
          let number = Number(tit.slice(20, 23));
          for (let i = 0; i < this.codeGoods.length; i++) {
            if (NewProductCd == this.codeGoods[i].NewProductCd) {
              if (number == this.codeGoods[i].x_num) {
                if (!this.form.box_code) {
                  alert("请输入箱码");
                  return;
                } else {
                  if (this.otherBoxes.indexOf(this.form.box_code) >= 0) {
                    alert("箱码已存在！");
                    return;
                  }
                  if (
                    this.scanNum(NewProductCd) + number >
                    this.codeGoods[i].number
                  ) {
                    alert("超过扫码数量");
                    return;
                  }
                  this.form.last_box_code = "";
                  this.form.type = "箱";
                }
              } else if (number == this.codeGoods[i].h_num) {
                if (!this.form.box_code) {
                  if (!this.form.last_box_code) {
                    alert("请输入箱码");
                    return;
                  } else {
                    if (
                      this.scanNum(NewProductCd) + number >
                      this.codeGoods[i].number
                    ) {
                      alert("超过扫码数量");
                      return;
                    }
                    this.form.box_code = this.form.last_box_code;
                    this.form.type = "盒";
                  }
                } else {
                  if (
                    this.scanNum(NewProductCd) + number >
                    this.codeGoods[i].number
                  ) {
                    alert("超过扫码数量");
                    return;
                  }
                  this.form.type = "盒";
                }
              } else {
                alert("箱规错误");
                return;
              }
              this.form.type_code = tit;
              this.form.number = number;
              this.form.NewProductCd = NewProductCd;
              return;
            };
          };
          alert("该产品不在该单上！");
          return;
        } else {
          for (let i = 0; i < this.codeGoods.length; i++) {
            if (tit == this.codeGoods[i].barCode) {
              if (
                this.scanNum(this.codeGoods[i].NewProductCd) + 1 >
                this.codeGoods[i].number
              ) {
                alert("超过扫码数量");
                return;
              }
              if (!this.form.box_code) {
                if (!this.form.last_box_code) {
                  alert("请输入箱码");
                  return;
                } else {
                  this.form.box_code = this.form.last_box_code;
                }
              }
              this.form.type_code = tit;
              this.form.number = 1;
              this.form.NewProductCd = this.codeGoods[i].NewProductCd;
              this.form.type = "支";
              return;
            }
          };
          this.$message.warning("型号码不存在!");
          return;
        }
      } else {
        if (tit.length != 16) {
          alert("16位防串货码错误！");
          return;
        } else {
            for (let i = 0; i < this.codes.length; i++) {
              if (tit === this.codes[i].code) {
                alert("防串货码不能重复！");
                return;
              }
            };
            this.codes.unshift({
                type_code: this.form.type_code,
                box_code: this.form.box_code,
                number: this.form.number,
                type: this.form.type,
                NewProductCd: this.form.NewProductCd,
                code: tit
            });
            setRedis({
              id: this.$route.params.id,
              data: this.codes
            }).then(res =>{

            });
            if (this.form.type == "箱") {
              this.boxes.push(this.form.box_code);
            } else {
              this.otherBoxes.push(this.form.box_code);
            }
            this.form.type_code = this.form.box_code = "";
        }
      }
    },
    handleSizeChange: function (val) {
      this.query.limit = val;
    },
     handleCurrentChange: function (val) {
      this.query.page = val;
    },
    delCode(row) {
      this.codes.splice(this.codes.indexOf(row), 1);
      setRedis({
        id: this.$route.params.id,
        data: this.codes
      }).then(res =>{
        //  if(res.code === 200){
        //    this.loadData();
        //  };
      });
      if (row.type == "箱") {
        this.boxes.splice(this.codes.indexOf(row.box_code), 1);
      } else {
        let flag = 0;
        for (let i = 0; i < this.codes.length; i++) {
          if (row.box_code == this.codes[i].box_code) {
            flag = 1;
          }
        }
        if (!flag) {
          this.otherBoxes.splice(this.codes.indexOf(row.box_code), 1);
        }
      }
    },
    doCreate() {
      this.buttonDisabled = true;
       for (let i = 0; i < this.codeGoods.length; i++) {
          if (
            this.scanNum(this.codeGoods[i].NewProductCd) !=
            this.codeGoods[i].number
          ) {
            this.buttonDisabled = false;
            alert("数量不符");
            return;
          }
        }
      submit({
        id: this.$route.params.id,
        type: this.$route.query.type,
        data: this.codes
      })
        .then(res => {
          this.$message({
            message: "扫码完成",
            type: "success"
          });
          this.$router.push({
            path: "/barcode/fangchuanhuo/"
          });
        })
        .catch(err => {
          this.buttonDisabled = false;
        });
    },
    handleBack() {
      this.$router.push({
        path: "/barcode/fangchuanhuo/"
      });
    }
  }
};
</script>

<style lang="less" scoped>
.numbers,
.location {
  margin: 0;
  font-size: 18px;
}
.location {
  margin: 20px 0 0 0;
}
.next,
.back {
  width: 30% !important;
}
.next {
  margin: 0 0 0 38% !important;
}
.detail_content {
  height: auto;
}
.layout-header {
  position: fixed;
}
.dialog-footer {
  margin: 15px 0 0 0;
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
  font-size: 12px;
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
