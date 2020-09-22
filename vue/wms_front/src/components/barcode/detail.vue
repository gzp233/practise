<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="detail_content">
      <div class="head">
        <span style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0">出库复核</span>
        <el-input style="width:260px;position: absolute;right:0" v-model="tmpData.tmpInput" class="button-item" placeholder="请扫描" id="codeInput" @keyup.enter.native="handleAdd" ref="customerInput"></el-input>
      </div>
      <br>
      <span>箱码：{{ form.box_code }}</span>
      <span>支码：{{ form.type_code }}</span>
      <el-input v-model="search" class="button-item" placeholder="搜索"></el-input>
      <div class="show-group">
        <el-table :data="pageCodes" stripe style="width: 100%;" max-height="250">
          <el-table-column class="button-item" prop="box_code" label="箱号" min-width="50"></el-table-column>
          <el-table-column class="button-item" prop="type_code" label="新产品代码" min-width="100"></el-table-column>
          <el-table-column class="button-item" prop="number" label="数量" min-width="50"></el-table-column>
          <el-table-column class="button-item" prop="date_code" label="有效期" min-width="60"></el-table-column>
          <el-table-column class="button-item" label="操作" min-width="60">
            <template slot-scope="scope">
              <el-button type="text" size="small" :disabled="scope.row.disabled" @click.native.prevent="delCode2(scope.$index,scope.row)">后退</el-button>
            </template>
          </el-table-column>
        </el-table>

        <el-pagination background @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="query.page" :page-size="query.limit" layout="prev, pager, next" :total="codes.length"></el-pagination>

      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="doCreates">返 回</el-button>
        <el-button :disabled="buttonDisabled" type="primary" @click="doCreate" style="float:right;">确 定</el-button>
      </span>
    </div>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import {
  delCode,
  saveCode,
  getBarCode,
  stockOut,
  getFuheOrderByNo
} from "@/api/barcode";
import { clearTimeout } from 'timers';

export default {
  name: "scan",
  data() {
    return {
      buttonDisabled: false,
      navData: [],
      tmpData: { tmpInput: "", lastBoxCode: "" },
      form: {
        box_code: "",
        type_code: "",
        date_code: "",
        number: ""
      },
      query: {
        limit: 10,
        page: 1
      },
      search: "",
      codes: [],
      boxes: [],
      otherBoxes: [],
      count: 0,
      BOXNUM: "",
      TYPENUM: "",
      DATENUM: "",
      TOTAL: {}
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
  },
  mounted() {
    this.loadData({ id: this.$route.params.id });
  },
  methods: {
    loadData(data) {
      getFuheOrderByNo(data).then(res => {
        this.codes = res.data.codes;
        this.boxes = res.data.boxes;
        this.otherBoxes = res.data.otherBoxes;
        for (let i = 0; i < this.codes.length; i++) {
          this.$set(this.codes[i], "COUNT", 0);
          this.$set(this.codes[i], "disabled", false);
        };
        console.log(this.codes)
      }).catch(error => {
        this.$message({
          message: "订单状态错误",
          type: "error"
        });
        this.$router.push({
          path: "/barcode/reivew"
        });
      });
    },
    handleSizeChange: function (val) {
      this.query.limit = val;
    },
    handleCurrentChange: function (val) {
      this.query.page = val;
    },
    newArr(arr) {
      for (var i = 0; i < arr.length; i++) {
        for (var j = i + 1; j < arr.length; j++) {
          if (arr[i].box_code == arr[j].box_code && arr[i].type_code == arr[j].type_code && arr[i].date_code == arr[j].date_code) {
            //如果第一个等于第二个，splice方法删除第二个
            arr.splice(j, 1);
            j--;
          }
        }
      };
      return arr;
    },
    handleAdd() {
      let tit = this.tmpData.tmpInput;
      clearTimeout(setTime);
      let setTime = setTimeout(() => {
        this.$set(this.tmpData, "tmpInput", "");
      }, 30);
      if (!this.form.box_code) {
        if (tit.length > 5) {
          if (!this.tmpData.lastBoxCode) {
            alert("箱码不超过5位！");
            return;
          }
        } else {
          console.log(this.codes, this.boxes, this.otherBoxes)
          for (let i = 0; i < this.codes.length; i++) {
            if (tit == this.codes[i].box_code) {
              if (this.codes[i].unit == "箱") {
                this.$message.warning("同一箱码只能扫一次");
                return;
              } else {

              };
            }
          };
          // if (this.boxes.includes(tit)) {
          //   alert("同一箱码只能扫一次");
          //   return;
          // };
          this.form.box_code = tit;
          this.tmpData.lastBoxCode = tit;
          return;
        }
      }

      if (!this.form.type_code) {
        if (tit.length != 23 && tit.length != 13 && tit.length != 14 && tit.length != 12) {
          alert("型号码23位单只码12,13位或14位！");
          return;
        }
        // 91码的情况
        if (tit.length == 23) {
          if (Number(tit.slice(1, 4)) > 366 || tit.indexOf("%") >= 0) {
            alert("91码错误");
            return;
          };
          let bc = this.form.box_code ? this.form.box_code : this.tmpData.lastBoxCode;
          saveCode({
            id: this.$route.params.id,
            str: tit,
            type: 1,
            box_code: bc
          }).then(res => {
            this.codes.unshift(res.data);
            this.newArr(this.codes);
            this.boxes.push(this.tmpData.lastBoxCode);
            if (res.data.unit == "箱") {
              this.boxes.push(res.data.box_code);
              this.tmpData.lastBoxCode = "";
            } else {
              if (!this.otherBoxes.includes(this.form.box_code)) {
                this.otherBoxes.push(this.form.box_code);
              }
            }
            this.form.box_code = "";
          });
        } else {
          // 支码的情况
          if (tit.length == 12 || tit.length == 13 || tit.length == 14) {
            this.form.box_code = this.tmpData.lastBoxCode;
            getBarCode({ id: this.$route.params.id, type_code: tit }).then(res => {
              this.form.type_code = res.data.NewPRODUCTCD;
              if (this.count == 1) {
                this.form.type_code = res.data.NewPRODUCTCD;
                this.count = 2;
                return;
              };
              if (this.count == 2) {
                if (this.form.box_code == this.BOXNUM && this.form.type_code == this.TYPENUM && this.TOTAL.COUNT == 1) {
                  saveCode({
                    id: this.$route.params.id,
                    str: this.form.type_code + "_" + this.TOTAL.date_code,
                    type: 2,
                    box_code: this.form.box_code
                  }).then(res => {
                    this.TOTAL.number += 1;
                  }).catch(res => {
                    this.count = 1;
                    this.form.box_code = this.form.type_code = this.form.date_code = "";
                    return;
                  });
                  this.form.box_code = this.form.type_code = this.form.date_code = "";
                };
              };
              if (this.form.box_code == this.BOXNUM && this.form.type_code == this.TYPENUM && this.TOTAL.COUNT == 1) {
                saveCode({
                  id: this.$route.params.id,
                  str: this.form.type_code + "_" + this.TOTAL.date_code,
                  type: 2,
                  box_code: this.form.box_code
                }).then(res => {
                  console.log(1)
                  this.TOTAL.number += 1;
                }).catch(res => {
                  console.log(2)
                  this.count = 1;
                  this.form.box_code = this.form.type_code = this.form.date_code = "";
                  return;
                });
                this.form.box_code = this.form.type_code = this.form.date_code = "";
              };
            });
          }
        }
        return;
      };
      if (tit.length != 6) {
        alert("有效期只能是6位！");
        return;
      } else {
        saveCode({
          id: this.$route.params.id,
          str: this.form.type_code + "_" + tit,
          type: 2,
          box_code: this.form.box_code
        }).then(res => {
          this.codes.unshift(res.data);
          this.newArr(this.codes);
          for (let i = 0; i < this.codes.length; i++) {
            if (this.form.box_code == this.codes[i].box_code && this.form.type_code == this.codes[i].type_code && tit == this.codes[i].date_code) {
              this.codes[i].COUNT = 1;
              this.BOXNUM = this.codes[i].box_code;
              this.TYPENUM = this.codes[i].type_code;
              this.DATENUM = this.codes[i].date_code;
              this.TOTAL = this.codes[i];
            };
          };
          this.boxes.push(this.tmpData.lastBoxCode);
          this.form.box_code = this.form.type_code = this.form.date_code = this.form.number = "";
        }).catch(res => {
          console.log(3)
          this.form.box_code = this.form.type_code = this.form.date_code = "";
          return;
        });
      };
    },
    delCode2(ele, row) {
      console.log(ele, row)
      row.disabled = true;
      let obj = "";
      obj = row.id + row.box_code + row.date_code + row.type_code + 'lock';
      localStorage.setItem("keys", obj);
      if (obj === localStorage.getItem("keys")) {
        delCode(row).then(res => {
          if (res.code === 200) {
            console.log(this.boxes)
            if (row.unit == "箱") {
              this.boxes.splice(this.boxes.indexOf(row.box_code), 1);
              // this.codes.splice(ele, 1);
            } else {
              this.boxes.splice(this.boxes.indexOf(row.box_code), 1);
              // console.log(this.codes)
              // row.number = res.data;
              // if(row.number === 0){
              // this.boxes.splice(this.boxes.indexOf(row.box_code), 1);
              // };
            };
            console.log(this.boxes)
            row.number = res.data;
            if (row.number === 0) {
              console.log(ele)
              this.codes.splice(ele, 1);
            };
            row.disabled = false;
          }
        }).catch(error => {
          this.$message.info("后退失败！");
          row.disabled = false;
          this.loadData({ id: this.$route.params.id });
          return;
        });
        localStorage.removeItem("keys");
      } else {
        return;
      };
      this.$refs.customerInput.$el.querySelector("input").focus();
    },
    doCreate() {
      this.buttonDisabled = true;
      this.$message({
        message: "数据正在处理，请稍后！",
        type: "info"
      });
      stockOut({ id: this.$route.params.id }).then(res => {
        if (res.data.length != 0) {
          this.$router.push({
            path: "/barcode/fuheErrors/" + this.$route.params.id
          });
          this.buttonDisabled = false;
        } else {
          this.$message({
            message: "复核成功",
            type: "success"
          });
          this.$router.push({
            path: "/barcode/reivew"
          });
          this.buttonDisabled = false;
        }
      });
    },
    doCreates() {
      this.$router.push({
        path: "/barcode/reivew"
      });
    }
  }
};
</script>

<style lang="less" scoped>
.detail_content {
  height: auto;
}
.layout-header {
  position: fixed;
}
// .dialog-footer {
//   position: absolute;
//   top: 340px;
//   right: 12px;
// }
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
  margin: 10px auto;
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
