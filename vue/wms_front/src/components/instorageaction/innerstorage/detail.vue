<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item
          :to="{ path: '/instorage_action/inner_storage' }"
        >内向交货入库{{ show?'详情':'处理' }}</el-breadcrumb-item>
        <el-breadcrumb-item>{{ show?'详情':'处理' }}</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane :label="detailLabel" name="detail">
          <el-row>
            <el-col :span="10">
              <div class="detail-item">
                <span>订货方订货编号</span>
                <div>{{ order.CustomerOrderNo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>发货单编号</span>
                <div>{{ order.InvoiceNo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>入库场所编号</span>
                <div>{{ order.PlaceCd }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>订单类型</span>
                <div>{{ order.LFART }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>批次号</span>
                <div>{{ order.CHARG }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>创建时间</span>
                <div>{{ order.created_at }}</div>
              </div>
            </el-col>
          </el-row>
        </el-tab-pane>
      </el-tabs>
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
            <el-table-column prop="InQnty" label="受注确认数量" min-width="150"></el-table-column>
            <el-table-column prop="todoNumber" label="未入库数量" min-width="150"></el-table-column>
            <el-table-column prop="library" label="入库中" min-width="150"></el-table-column>
            <el-table-column prop="process" label="加工中" min-width="150"></el-table-column>
            <el-table-column prop="query" label="加工完成" min-width="150"></el-table-column>
            <el-table-column prop="confirmNum" label="已回传" min-width="150"></el-table-column>
            <el-table-column label="操作" width="100">
              <template slot-scope="scope">
                <div class="action-column">
                  <el-button
                    type="text"
                    v-if="scope.row.tag"
                    size="small"
                    @click.native.prevent="showChoosed(scope.row)"
                  >查看</el-button>
                  <span v-else>未入库</span>
                </div>
              </template>
            </el-table-column>
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

          <el-dialog
            title="库位"
            :visible.sync="dialogDataVisible"
            width="50%"
            :modal-append-to-body="false"
          >
            <el-table :data="stockedList" stripe style="width: 100%">
              <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
              <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
              <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
              <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
              <el-table-column prop="number" label="入库数量" min-width="150"></el-table-column>
            </el-table>
            <el-pagination
              @size-change="handleQSizeChange"
              @current-change="handleQCurrentChange"
              :current-page.sync="q.page"
              :page-sizes="[10, 20, 50, 100]"
              :page-size="q.limit"
              layout="total,->, prev, pager, next, jumper, sizes"
              :total="tmpGoods.length"
            ></el-pagination>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogDataVisible = false">取 消</el-button>
              <el-button type="primary" @click="dialogDataVisible = false">确 定</el-button>
            </span>
          </el-dialog>
        </el-tab-pane>
      </el-tabs>
    </div>
    <instock
      :goodsData="{goodsList:propList, params:{is_states:1,no_stock_names:['加工区', '复核区', '移库区'],state_names:['C302', '加工完成']}}"
      v-if="!show"
      v-on:childByValue="childByValue"
    />
  </div>
</template>

<script>
import { getById, stockIn, hasStocked } from "@/api/inner_storage";
import instock from "@/components/share/instock";

export default {
  components: {
    instock
  },
  data() {
    return {
      id: "",
      goodsList: [],
      show: true,
      detailLabel: "",
      order: {},
      dialogDataVisible: false,
      tmpGoods: [],
      query: {
        limit: 10,
        page: 1
      },
      q: {
        limit: 10,
        page: 1
      }
    };
  },
  created() {
    this.show = /detail/.test(this.$route.path);
    this.detailLabel = this.show ? "内向交货入库详情" : "内向交货入库处理";
    this.id = this.$route.params.id;
    this.loadOrder(this.id);
  },
  computed: {
    showList() {
      let offset = this.query.limit * (this.query.page - 1);
      return this.goodsList.slice(offset, offset + this.query.limit);
    },
    stockedList() {
      let offset = this.q.limit * (this.q.page - 1);
      if (this.tmpGoods)
        return this.tmpGoods.slice(offset, offset + this.q.limit);
      return [];
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
    handleQSizeChange: function(val) {
      this.q.limit = val;
    },
    handleQCurrentChange: function(val) {
      this.q.page = val;
    },
    loadOrder(id) {
      getById({ id: id })
        .then(res => {
          this.goodsList = res.data;
          this.order = this.goodsList[0];
          for (let i = 0; i < this.goodsList.length; i++) {
            this.goodsList[i].number = this.goodsList[i].todoNumber;
            this.goodsList[i].NewPRODUCTCD = this.goodsList[i].product.NewPRODUCTCD;
            this.goodsList[i].PRODUCTCD = this.goodsList[i].product.PRODUCTCD;
            this.goodsList[i].detailNewPRODUCTCD = this.goodsList[i].product.NewPRODUCTCD;
            this.goodsList[i].detailPRODUCTCD = this.goodsList[i].product.PRODUCTCD;
          }
        })
        .catch(error => {
          this.$router.push({
            path: "/instorage_action/inner_storage"
          });
        });
    },
    showChoosed(row) {
      hasStocked({id:row.id, product_id:row.product.id}).then(res => {
        this.tmpGoods = res.data
        this.dialogDataVisible = true;
      })
    },
    childByValue: function(childValue) {
      // childValue就是子组件传过来的值
      stockIn(childValue).then(res => {
        this.$message({
          message: "入库成功！",
          type: "success"
        });
        this.$router.push({
          path: "/instorage_action/inner_storage"
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