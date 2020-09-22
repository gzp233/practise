<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/outstorage_action/adjust' }">在库调整</el-breadcrumb-item>
        <el-breadcrumb-item>在库调整处理</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane label="受注出库单" name="detail">
          <el-row>
            <el-col :span="10">
              <div class="detail-item">
                <span>受注编号</span>
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
            <el-button :disabled="buttonDisabled" type="primary" @click="getSheet">拣货单</el-button>
            <el-button :disabled="buttonDisabled" type="primary" @click="getPDF">交货单</el-button>
            <el-button :disabled="buttonDisabled" type="primary" @click="accept">验收单</el-button>
            <br>
            <br>
            <el-button :disabled="buttonDisabled" type="primary" @click="downloadPXHZ">汇总单</el-button>
            <el-button :disabled="buttonDisabled" type="primary" @click="binning">装箱汇总单</el-button>
            <el-button :disabled="buttonDisabled" type="primary" @click="pinxiangList">拼箱清单</el-button>            
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <div class="detail-content">
      <div class="btn-box">
        <el-button
          :disabled="buttonDisabled"
          type="primary"
          v-if="order.tag && order.tag.status === '复核中' || order.tag && order.tag.status === '拣货中'"
          @click="rollback"
        >回退</el-button>
        <el-button
          :disabled="buttonDisabled"
          type="primary"
          v-if="!order.tag || order.tag.status !== '发货完成'"
          @click="doChange"
        >{{ buttonName }}</el-button>
      </div>
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
            <el-table-column prop="total" label="库存数量" min-width="150" v-if="buttonName=='预拣货'"></el-table-column>
            <el-table-column prop="frozen" label="冻结数量" min-width="150" v-if="buttonName=='预拣货'"></el-table-column>
            <el-table-column prop="FineFlg" label="状态" min-width="150" v-if="buttonName!='预拣货'"></el-table-column>
            <el-table-column prop="AdjustQnty" label="受注数量" min-width="150"></el-table-column>
            <el-table-column label="操作" min-width="100">
              <template slot-scope="scope">
                <div class="action-column">
                  <el-button
                    type="text"
                    size="small"
                    @click.native.prevent="handleChoose(scope.row)"
                  >库位</el-button>
                </div>
              </template>
            </el-table-column>
          </el-table>

          <!-- dialog -->
          <el-dialog
            title="出库库位"
            :visible.sync="dialogVisible"
            width="50%"
            :modal-append-to-body="false"
            :before-close="cancel">
            <p class="dialogVisible-title">产品代码：{{ tmpGoods.NewProductCd }}, 出库数量: {{ tmpGoods.AdjustQnty }}</p>
            <el-table :data="tmpGoods.stocks" stripe style="width: 100%">
              <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
              <el-table-column prop="available_time" label="有效期" min-width="150"></el-table-column>
              <el-table-column prop="location.stock_no" label="库位" min-width="150"></el-table-column>
              <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
              <el-table-column prop="number" label="库存数量" min-width="100"></el-table-column>
              <el-table-column prop="number" label="出库数量" min-width="100" v-if="tmpGoods.tag"></el-table-column>
              <el-table-column label="出库数量" min-width="100" v-if="!tmpGoods.tag">
                <template slot-scope="scope">
                  <el-input @blur="changelog(scope.row)" @keyup.native="handleChange(scope.row)" v-model="scope.row.actNumber"></el-input>
                </template>
              </el-table-column>
            </el-table>
            <span slot="footer" class="dialog-footer">
              <el-button @click="cancel">取 消</el-button>
              <el-button type="primary" @click="saveNumber">确 定</el-button>
            </span>
          </el-dialog>

          <el-dialog
            title="拼箱列表"
            :visible.sync="dialogPXVisible"
            width="50%"
            :modal-append-to-body="false"
          >
            <el-table :data="pxList" stripe style="width: 100%">
              <el-table-column type width="20"></el-table-column>
              <el-table-column label="箱号" width="100">
                <template slot-scope="scope">{{ scope.row }}</template>
              </el-table-column>
              <el-table-column label="操作" min-width="100">
                <template slot-scope="scope">
                  <el-button type="text" size="small" @click="downloadPX(scope.row)">下载</el-button>
                </template>
              </el-table-column>
              <el-table-column type min-width="20"></el-table-column>
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
    </div>
  </div>
</template>

<script>
import { getByNo, stockOut, rollback, pxList } from "@/api/adjust";
import { getByOut } from "@/api/sales_out";
import { getToken } from "@/utils/auth";

export default {
  data() {
    return {
      buttonDisabled: true,
      loading: true,
      dialogVisible: false,
      dialogPXVisible: false,
      order: {},
      products: [],
      tmpGoods: {},
      pxList: [],
      buttonStatus: "",
      staging: "",
      arr:[],
      deepArr:[],
      obj:{},
      outDeep:[],
      count: 0,
      initialize: []
    };
  },
  created() {
    this.loadOrder(this.$route.params.id);
  }, 
  computed: {
    buttonName() {
      if (!this.order.tag) {
        this.buttonStatus = "拣货";
        return "预拣货";
      }
      if (this.order.tag.status === "拣货中") {
        this.buttonStatus = "拣货中";
        return "拣货";
      }
      if (this.order.tag.status === "复核中") {
        this.buttonStatus = "复核中";
        return "复核";
      }
      if (this.order.tag.status === "待回传") {
        this.buttonStatus = "待回传";
        return "回传";
      }
      if (this.order.tag.status === "待发运") {
        this.buttonStatus = "发货中";
        return "发货";
      }
    }
  },
  methods: {
     //数组去重
    removeDuplicatedItem(arr){
      for (var i = 0; i < arr.length - 1; i++) {
        for (var j = i + 1; j < arr.length; j++) {
          if (arr[i].stock_no == arr[j].stock_no && arr[i].available_time == arr[j].available_time) {
            arr.splice(i, 1);
            i--;
          };
        }
      }
      return arr;
    },
    handleChange(row){
       row.actNumber = row.actNumber.replace(/[^\d]/g,"");      
    },
    cancel(){
      this.dialogVisible = false;
      this.tmpGoods = this.initialize;
      this.arr = [];
      this.count = 0;
    },
    changelog(row){
      this.count = 1;  
    },
    loadOrder(id) {
      this.loading = true;
      getByNo({ id: id }).then(res => {
          this.products = res.data;
          this.order = res.data[0];
          setTimeout(() => {
            this.loading = false;
            this.buttonDisabled = false;
          }, 100);
        }).catch(error => {
          this.$router.push({
            path: "/outstorage_action/adjust"
          });
        });
    },
    handleChoose(row) {
      let arr = [];
      this.initialize = JSON.parse(JSON.stringify(row));
      this.tmpGoods = row;     
      this.outDeep = JSON.parse(JSON.stringify(row.stocks));
      let old_data = row.stocks;
      console.log(row)
      for(let i=0;i<old_data.length;i++){
        let obj = {};
        if(Number(old_data[i].actNumber) != 0){                
          obj.odd = this.order.AdjustNo;
          obj.NewProductCd = this.tmpGoods.NewProductCd;
          obj.stock_no = old_data[i].location.stock_no;
          obj.status = "0";
          obj.available_time = old_data[i].available_time;
          obj.actNumber = Number(old_data[i].actNumber);
          arr.push(obj); 
          [...this.deepArr] = arr;
        };
      };
      console.log(this.arr)
      this.dialogVisible = true;
    },
    doChange() {
      this.buttonDisabled = true;
      this.$message({
        message: "数据正在处理，请稍后！",
        type: "info"
      });
      stockOut({
        orders: this.products,
        status: this.buttonStatus
      }).then(res => {
          if (
            this.order &&
            this.order.tag &&
            this.order.tag.status === "待发运"
          ) {
            this.$message({
              message: "发货完成！",
              type: "success"
            });
            this.$router.push({
              path: "/outstorage_action/adjust"
            });
            return;
          }

          if (
            this.order &&
            this.order.tag &&
            this.order.tag.status === "拣货中"
          ) {
            this.$message({
              message: "拣货成功！",
              type: "success"
            });
          }
          if (
            this.order &&
            this.order.tag &&
            this.order.tag.status === "复核中"
          ) {
            this.$message({
              message: "复核成功！",
              type: "success"
            });
          }
          if (
            this.order &&
            this.order.tag &&
            this.order.tag.status === "待回传"
          ) {
            this.$message({
              message: "回传成功！",
              type: "success"
            });
          }
          if (this.order && this.order.tag === null) {
            this.$message({
              message: "预拣货成功！",
              type: "success"
            });
            this.arr.push(this.deepArr);
            getByOut(this.arr.flat()).then(res =>{
              if(res.code === 200){
                this.dialogVisible = false;
              };
            });   
          }
          this.loadOrder(this.$route.params.id);
          this.$router.push({
            path: "/outstorage_action/adjust/out/" + this.order.OutStcNo
          });
        })
        .catch(err => {
          this.loadOrder(this.$route.params.id);
          this.buttonDisabled = false;
        });
    },
    rollback() {
      this.buttonDisabled = true;
      this.$message({
        message: "数据正在处理，请稍后！",
        type: "info"
      });
      rollback({ orders: this.products }).then(res => {
        this.$message({
          message: "回退成功！",
          type: "success"
        });
        this.loadOrder(this.$route.params.id);
        this.$router.push({
          path: "/outstorage_action/adjust/out/" + this.order.OutStcNo
        });
      });
      this.loadOrder(this.$route.params.id);
    },
    pinxiangList() {
      this.pxList = [];
      pxList({ vbeln: this.order.AdjustNo }).then(res => {
        this.pxList = res.data;
        this.dialogPXVisible = true;
      });
    },
    getSheet() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust/exportDoc?id=" +
          this.order.AdjustNo +
          "&token=" +
          token[1],
        "_blank"
      );
    },
    downloadPX(row) {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust/downloadPX?vbeln=" +
          this.order.AdjustNo +
          "&case=" +
          row +
          "&token=" +
          token[1],
        "_blank"
      );
    },
    downloadPXHZ(row){
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
              "/api/adjust/downloadPXHZ?id=" +
              this.order.AdjustNo +
              "&token=" +
              token[1],
              "_blank"
      );
    },
    getPDF() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust/exportPDF?id=" +
          this.order.OutStcNo +
          "&token=" +
          token[1],
        "_blank"
      );
    },
    binning() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust/binning?id=" + this.order.AdjustNo + "&token=" + token[1],
        "_blank"
      );
    },
    accept() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/adjust/accept?id=" + this.order.OutStcNo + "&token=" + token[1],
        "_blank"
      );
    },
    saveNumber() {
      var total = 0;
      for (let i = 0; i < this.tmpGoods.stocks.length; i++) {
        total += Number(this.tmpGoods.stocks[i].actNumber);
        if (
          Number(this.tmpGoods.stocks[i].actNumber) > Number(this.tmpGoods.stocks[i].number)
        ) {
          this.$message({
            message: "出库数量不能超过库位存放数量",
            type: "warning"
          });
          return;
        }
      };
      if (total != this.tmpGoods.AdjustQnty) {
        this.$message({
          message: "出库数量不正确",
          type: "warning"
        });
        return;
      };
      this.arr = [];
      for(let i=0;i<this.tmpGoods.stocks.length;i++){
        if(this.count === 1){
           if(Number(this.tmpGoods.stocks[i].actNumber) != 0){
            let obj = {};         
            obj.odd = this.order.AdjustNo;
            obj.NewProductCd = this.tmpGoods.NewProductCd;
            obj.stock_no = this.tmpGoods.stocks[i].location.stock_no;
            obj.status = "1";
            obj.available_time = this.tmpGoods.stocks[i].available_time;
            obj.actNumber = Number(this.tmpGoods.stocks[i].actNumber);
            this.arr.push(obj);            
          }
        }else{
          this.$message.warning("未分配数量不能提交!");
          return;
        };        
      };   
      this.dialogVisible = false;      
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
.dialogVisible-title {
  margin-bottom: 10px;
}
</style>