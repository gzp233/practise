<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="head">
      <span
        style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
        >集货拣货</span
      >
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
      <el-button type="primary" class="button-item" @click="doCreate"
        >返 回</el-button
      >
    </span>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getByNo } from "@/api/jihuo";
export default {
  name: "scan",
  data() {
    return {
      tmpInput: "",
      navData: []
    };
  },
  components: {
    "v-header": appHeader
  },
  methods: {
    handleAdd() {
      if (this.tmpInput.length != 11) {
        alert("集货编号必须是十一位！");
        return;
      }
      getByNo({ id: this.tmpInput }).then(res => {
        this.$router.push({
          path: "/barcode/jihuo/stockList/" + this.tmpInput
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

<style lang="less" scoped>
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
</style>