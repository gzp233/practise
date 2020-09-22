<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="detail_content">
      <div class="show-group">
        <el-table :data="tableData" stripe style="width: 100%;" max-height="800">
          <el-table-column class="button-item" prop="NewPRODUCTCD" label="漏扫产品代码" min-width="100"></el-table-column>
          <!-- <el-table-column class="button-item" prop="number" label="数量" min-width="50"></el-table-column> -->
          <el-table-column class="button-item" prop="available_time" label="有效期" min-width="60"></el-table-column>
          <el-table-column class="button-item" prop="number" label="数量" min-width="60"></el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
          <el-button @click="doCreates">返 回</el-button>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getErrors } from "@/api/barcode";

export default {
  name: "scan",
  data() {
    return {
      navData: [],
      tableData: []
    };
  },
  components: {
    "v-header": appHeader
  },
  mounted() {
    this.loadData({ id: this.$route.params.id });
  },
  methods: {
    loadData(data) {
      getErrors(data).then(res => {
        this.tableData = res.data
      });
    },
    doCreates() {
      this.$router.push({
        path: "/barcode/detail/"+this.$route.params.id
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
