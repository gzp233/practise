<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/outstorage_action/sell_out' }">受注出库</el-breadcrumb-item>
        <el-breadcrumb-item>出库单详情</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane label="受注出库单" name="detail">
          <el-row>
            <el-col :span="10">
              <div class="detail-item">
                <span>受注编号</span>
                <div>{{ order.OrderNo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>出库编号</span>
                <div>{{ order.OutStcNo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>出库场所编号</span>
                <div>{{ order.PlaceCD }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>出库库位代码</span>
                <div>{{ order.FineFlg }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>商店</span>
                <div>{{ order.customer ? order.customer.ShopSignNM : order.CustomerCd }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>送货地代码</span>
                <div>{{ order.deliver ? order.deliver.DeliverAdd : order.DeliverAddCD }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>受注日期</span>
                <div>{{ order.AdmYMD }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>销售订单类型</span>
                <div>{{ order.AUART }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>备注特殊送货地地址</span>
                <div>{{ order.DeliverAddMemo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>备注</span>
                <div>{{ order.Memo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>POS订单号</span>
                <div>{{ order.ApplyNo }}</div>
              </div>
            </el-col>
            <el-col :span="10">
              <div class="detail-item">
                <span>创建时间</span>
                <div>{{ order.created_at }}</div>
              </div>
            </el-col>
          </el-row>
          <div class="btn-box">
            <el-button type="primary" @click="getSheet">拣货单</el-button>
            <el-button type="primary" @click="getPDF">交货单</el-button>
            <el-button type="primary" @click="accept">验收单</el-button>
            <br>
            <br>
            <el-button type="primary" @click="downloadPXHZ">汇总单</el-button>
            <el-button type="primary" @click="binning">装箱汇总单</el-button>
            <el-button type="primary" @click="pinxiangList">拼箱清单</el-button>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
    <div class="detail-content">
      <el-tabs activeName="table">
        <el-tab-pane label="商品明细" name="table">
          <div class="statistic-content">
            <p>
              统计：
              <span>产品种类：{{ products.length }}</span>
            </p>
          </div>
          <el-table :data="products" v-loading.body="loading" stripe style="width: 100%">
            <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="300"></el-table-column>
            <el-table-column prop="NewProductCd" label="新产品代码" min-width="150"></el-table-column>
            <el-table-column prop="product.PRODUCTCD" label="产品代码" min-width="150"></el-table-column>
            <el-table-column prop="total" label="库存数量" min-width="150"></el-table-column>
            <el-table-column prop="AdmQnty" label="受注数量" min-width="150"></el-table-column>
            <el-table-column label="库位" min-width="100">
              <template slot-scope="scope">
                <div class="action-column" v-if="scope.row.tag">
                  <el-button
                    type="text"
                    size="small"
                    @click.native.prevent="handleChoose(scope.row)"
                  >出库库位</el-button>
                </div>
                <div v-else>未出库</div>
              </template>
            </el-table-column>
          </el-table>

          <!-- dialog -->
          <el-dialog
            title="库位"
            :visible.sync="dialogVisible"
            width="50%"
            :modal-append-to-body="false"
          >
            <el-table :data="tmpGoods.stocks" stripe style="width: 100%">
              <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
              <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
              <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
              <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
              <el-table-column prop="number" label="出库数量" min-width="150"></el-table-column>
            </el-table>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogVisible = false">取 消</el-button>
              <el-button type="primary" @click="dialogVisible = false">确 定</el-button>
            </span>
          </el-dialog>
        </el-tab-pane>
      </el-tabs>
      <el-dialog
              title="拼箱列表"
              :visible.sync="dialogPXVisible"
              width="50%"
              :modal-append-to-body="false"
      >
        <el-table :data="pxList" stripe style="width: 100%">
          <el-table-column
                  type=""
                  width="20">
          </el-table-column>
          <el-table-column label="箱号" width="100">
            <template slot-scope="scope">
              {{ scope.row }}
            </template>
          </el-table-column>
          <el-table-column label="操作" width="100">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="downloadPX(scope.row)">下载</el-button>
            </template>
          </el-table-column>
          <el-table-column
                  type=""
                  min-width="20">
          </el-table-column>
        </el-table>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogPXVisible = false">取 消</el-button>
              <el-button type="primary" @click="dialogPXVisible = false">确 定</el-button>
            </span>
      </el-dialog>
      </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
import { getBySee,pxList } from "@/api/sales_out";
import { getToken } from "@/utils/auth";

export default {
  data() {
    return {
      loading: true,
      order: {},
      dialogVisible: false,
      products: [],
      tmpGoods: {},
      dialogPXVisible: false,
      pxList: [],
    };
  },
  created() {
    this.loadOrder(this.$route.params.id);
  },
  methods: {
    downloadPXHZ(){
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
              "/api/sales_out/downloadPXHZ?id=" +
              this.order.OrderNo +
              "&token=" +
              token[1],
              "_blank"
      );
    },
    loadOrder(id) {
      this.loading = true;
      getBySee({ id: id })
        .then(res => {
          this.products = res.data;
          this.order = res.data[0];
          setTimeout(() => {
            this.loading = false;
          }, 100);
        })
        .catch(error => {
          // this.$router.push({
          //   path: '/outstorage_action/sell_out'
          // })
        });
    },
    handleChoose(row) {
      this.tmpGoods = row;
      this.dialogVisible = true;
    },
    getSheet() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/sales_out/exportDoc?id=" +
          this.order.OrderNo +
          "&token=" +
          token[1],
        "_blank"
      );
    },
    getPDF() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/sales_out/exportPDF?id=" +
          this.order.OutStcNo +
          "&token=" +
          token[1],
        "_blank"
      );
    },
    binning(){
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
              "/api/sales_out/binning?id=" +
              this.order.OrderNo +
              "&token=" +
              token[1],
              "_blank"
      );
    },
    downloadPX(row){
      console.log(row)
      let token = getToken()
      token = token.split(" ", 2);
      window.open(
              "/api/sales_out/downloadPX?vbeln=" +
              this.order.OrderNo + "&case=" + row +
              "&token=" +
              token[1],
              "_blank"
      );
    },
    pinxiangList() {
      this.pxList = []
      pxList({vbeln:this.order.OrderNo}).then(res => {
        this.pxList = res.data
      this.dialogPXVisible = true
    })
    },
    accept() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/sales_out/accept?id=" +
          this.order.OutStcNo +
          "&token=" +
          token[1],
        "_blank"
      );
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
.table-form-item {
  position: relative;
  height: 100%;
  width: 100%;
  .error-text {
    font-size: 12px;
    color: #f97042;
  }
}
.warehouse-select {
  margin-right: 20px;
  margin-bottom: 20px;
}
</style>