<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/instorage_process/list' }">加工区</el-breadcrumb-item>
        <el-breadcrumb-item>加工上架</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <el-tabs activeName="table">
        <el-tab-pane label="商品明细" name="table">
          <div class="statistic-content">
            <p>
              统计：
              <span>产品种类：{{ goodsList.length }}</span>
            </p>
          </div>
          <el-table :data="showList" stripe style="width: 100%">
            <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="300"></el-table-column>
            <el-table-column prop="stock_no" label="库位" min-width="100"></el-table-column>
            <el-table-column prop="available_time" label="有效期" min-width="100"></el-table-column>
            <el-table-column prop="number" label="上架数量" min-width="150"></el-table-column>
          </el-table>
          <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page.sync="query.page"
            :page-sizes="[10, 20, 50, 100]"
            :page-size="query.limit"
            layout="total,->, prev, pager, next, jumper, sizes"
            :total="goodsList.length"
          ></el-pagination>
        </el-tab-pane>
      </el-tabs>
    </div>
    <instock
      :goodsData="{goodsList:propList, params:{is_instorage: 1, no_stock_names:['加工区', '复核区', '移库区'],state_names:['加工完成']}}"
      v-on:childByValue="childByValue"
    />
  </div>
</template>

<script>
import { getGoodsByIds, stockIn } from "@/api/instorage_process";
import instock from "@/components/share/instock";

export default {
  components: {
    instock
  },
  data() {
    return {
      goodsList: [],
      query: {
        limit: 10,
        page: 1
      }
    };
  },
  created() {
    this.loadData(this.$route.query.goodsIdsList);
  },
  computed: {
    showList() {
      let offset = this.query.limit * (this.query.page - 1);
      return this.goodsList.slice(offset, offset + this.query.limit);
    },
    propList() {
      let arr = [];
      for (let k = 0; k < this.goodsList.length; k++) {
        if (this.goodsList[k].todoNumber != 0) {
          arr.push(this.goodsList[k]);
        }
      }
      return arr;
    }
  },
  methods: {
    handleSizeChange: function(val) {
      this.query.limit = val;
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
    },
    loadData(goodsIdsList) {
      getGoodsByIds(goodsIdsList)
        .then(res => {
          this.goodsList = res.data;
          for (let i = 0; i < this.goodsList.length; i++) {
            this.goodsList[i].todoNumber = this.goodsList[i].number;
            this.goodsList[i].NewPRODUCTCD = this.goodsList[i].product.NewPRODUCTCD;
            this.goodsList[i].PRODUCTCD = this.goodsList[i].product.PRODUCTCD;
            this.goodsList[i].detailNewPRODUCTCD = this.goodsList[i].product.NewPRODUCTCD;
            this.goodsList[i].detailPRODUCTCD = this.goodsList[i].product.PRODUCTCD;
          }
        })
        .catch(error => {
          this.$router.push({
            path: "/instorage_process/list"
          });
        });
    },
    childByValue: function(childValue) {
      // childValue就是子组件传过来的值
      stockIn(childValue).then(res => {
        this.$message({
          message: "上架成功！",
          type: "success"
        });
        this.$router.push({
          path: "/instorage_action/instorage_process"
        });
      });
    }
  }
};
</script>
<style lang="less">
.grid-container {
  height: auto;
  overflow: hidden;
  .detail-content {
    position: relative;
    height: auto;
    overflow: hidden;
    padding: 22px 15px;
    background: #fff;
    .detail-item {
      height: auto;
      overflow: hidden;
      line-height: 30px;
      font-size: 14px;
      padding-left: 30px;
      > span {
        display: inline-block;
        width: 150px;
        float: left;
        color: #333;
      }
      > div {
        margin-left: 160px;
        color: #999;
      }
    }
    .btn-box {
      position: absolute;
      top: 25px;
      right: 15px;
      z-index: 10;
    }
  }
  .action-column {
    text-align: right;
  }
  .color-gred {
    color: #999;
  }
}
.el-pagination {
  margin: 10px 0;
}
</style>