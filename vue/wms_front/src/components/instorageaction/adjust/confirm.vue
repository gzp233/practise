<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/instorage_action/adjust' }">在库调整确认</el-breadcrumb-item>
        <el-breadcrumb-item>确认</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane label="在库调整确认" name="detail">
          <el-row>
						<el-col :span="10">
							<div class="detail-item">
								<span>调整编号</span>
								<div>{{ order.AdjustNo }}</div>
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
            <el-table-column prop="AdjustQnty" label="受注数量" min-width="150"></el-table-column>
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

    <reply
      :goodsData="{goodsList:goodsList02, params:{no_state_names:['C302', '加工完成']}}"
      v-on:childByValue="childByValue"
    />
  </div>
</template>

<script>
import { getById, confirmRe, hasStocked } from "@/api/adjust_in";
import { getByNo } from "@/api/procurement_storage";
import reply from "@/components/share/reply";

export default {
  components: {
    reply
  },
  data() {
    return {
      goodsList: [],
      goodsList02: [],
      order: {},
      dialogDataVisible: false,
      tmpGoods: [],
      query: {
        limit: 10,
        page: 1,
        id: this.$route.params.id
      },
      q: {
        limit: 10,
        page: 1
      }
    };
  },
  created() {
    this.loadOrder(this.$route.params.id);
    this.parentReceive();
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
    parentReceive(val){
       getByNo(this.query).then(res => {
          this.goodsList02 = res.data;          
        }).catch(error => {
          this.$router.push({
            path: "/instorage_action/adjust"
          });
        });
    },
    loadOrder(id) {
      getById({ id: id })
        .then(res => {
          this.goodsList = res.data;
          this.order = this.goodsList[0];
          for (let i = 0; i < this.goodsList.length; i++) {
            this.goodsList[i].number = this.goodsList[i].todoNumber;
          }
        })
        .catch(error => {
          this.$router.push({
            path: "/instorage_action/adjust"
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
      for(let i=0;i<childValue.length;i++){
          childValue[i].conversion = this.order.AdjustNo
      };
      confirmRe(childValue).then(res => {
        this.$message({
          message: "确认成功！",
          type: "success"
        });
        this.$router.push({
          path: "/instorage_action/adjust"
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