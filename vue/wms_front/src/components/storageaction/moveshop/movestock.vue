<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item>库内移动</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <div>
        <el-button type="primary" @click="toShelf">新建移货单</el-button>
        <el-button type="primary" @click="exportOut">导出</el-button>
      </div>
      <el-tabs activeName="table">
        <el-tab-pane label="商品明细" name="table">
          <el-table ref="multipleTable" :data="tableData" stripe v-loading.body="loading" height="550" style="width: 100%" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="yiku_no" label="移库任务号" min-width="170" :render-header="serachHeader" column-key="yiku_no"></el-table-column>
            <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="150" :render-header="serachHeader" column-key="PRODCHINM"></el-table-column>
            <el-table-column prop="product.NewPRODUCTCD" label="新产品代码" min-width="130" :render-header="serachHeader" column-key="NewPRODUCTCD"></el-table-column>
            <el-table-column prop="product.PRODUCTCD" label="产品代码" min-width="120" :render-header="serachHeader" column-key="PRODUCTCD"></el-table-column>
            <el-table-column prop="origin_available_time" label="效期" min-width="90" :render-header="serachHeader" column-key="available_time"></el-table-column>
            <el-table-column prop="origin_state_name" label="良品标志" min-width="115" :render-header="serachHeader" column-key="origin_stock_name"></el-table-column>
            <el-table-column prop="origin_stock_no" label="移出库位" min-width="115" :render-header="serachHeader" column-key="stock_no"></el-table-column>
            <el-table-column prop="stock_no" label="移入库位" min-width="115" :render-header="serachHeader" column-key="to_stock_no"></el-table-column>
            <el-table-column prop="number" label="移动数量" min-width="100"></el-table-column>
            <el-table-column prop="available_time" label="移入效期" min-width="115" :render-header="serachHeader" column-key="available_time"></el-table-column>
            <el-table-column prop="state_name" label="移入良品标志" min-width="145" :render-header="serachHeader" column-key="state_name"></el-table-column>
            <el-table-column prop="status" label="任务状态" min-width="115" :render-header="serachHeader" column-key="status"></el-table-column>
            <!-- <el-table-column prop="created_at" label="创建时间" min-width="115" :render-header="serachHeader" column-key="created_at"></el-table-column> -->
            <el-table-column :render-header="serachTimeHeader" column-key="created_at" prop="created_at" label="创建时间" width="200"></el-table-column>
            <el-table-column :render-header="serachTimeHeader" column-key="starttime" prop="starttime" label="开始时间" width="200"></el-table-column>
            <el-table-column :render-header="serachTimeHeader" column-key="endtime" prop="endtime" label="结束时间" width="200"></el-table-column>
            <!-- <el-table-column prop="starttime" label="开始时间" min-width="115" :render-header="serachHeader" column-key="status"></el-table-column> -->
            <!-- <el-table-column prop="endtime" label="结束时间" min-width="115" :render-header="serachHeader" column-key="status"></el-table-column> -->
            <el-table-column prop="create_user.username" label="创建人ID" min-width="120" :render-header="serachHeader" column-key="create_user.username"></el-table-column>
            <el-table-column prop="deal_user.username" label="执行人ID" min-width="120" :render-header="serachHeader" column-key="deal_user.username"></el-table-column>
            <!-- <el-table-column fixed="right" label="操作" width="120">
              <template slot-scope="scope">
                <el-button @click.native.prevent="deleteRow(scope.$index, scope.row)" type="text" size="small">删除 </el-button>
              </template>
            </el-table-column> -->
          </el-table>
          <el-pagination
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
            :current-page.sync="query.page"
            :page-sizes="[10, 20, 50, 100]"
            :page-size="query.limit"
            layout="total,->, prev, pager, next, jumper, sizes"
            :total="total"
          ></el-pagination>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
import { getGoodsByIds, stockIn } from "@/api/move_stock";
import instock from "@/components/share/instock";
import { delCart, cartList, submitCart, getList, out} from "@/api/out";
import { fetchList } from "@/api/goods";
import { getToken } from "@/utils/auth";
export default {
  components: {
    instock
  },
  data() {
    return {
      goodsList: [],
      outLocation:"",
      inLocation:"",
      inNumber:"",
      selectVal:"",
      value: '',
      query: {
        limit: 10,
        page: 1
      },
      multipleSelection:[],
      loading:true,
      total:0,
      tableData:[]
    };
  },
  mounted() {
    this.loadData();
  },
  created() {
    this.handleCurrentChange(this.query.page);
  },
  computed: {
    showList() {
      let offset = this.query.limit * (this.query.page - 1);
      return this.goodsList.slice(offset, offset + this.query.limit);
    }
  },
  methods: {
    serachTimeHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-date-picker
            label="开始时间"
            v-model={this.query[column.columnKey]}
            type="datetimerange"
            range-separator=" 至 "
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            placeholder={column.label}
            on-Change={this.loadData}
          />
        </div>
      );
    },
    serachHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.query[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.loadData()}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    handleSelectionChange(val) {  // 勾选
      this.multipleSelection = val;
      console.log(this.multipleSelection)
    },
    changeLocationValue(val){
       this.selectVal = val;
    },
    confirm(){  //创建任务  确定
      this.dialogVisible = false;
      let result = {};
      result.deal_user = this.selectVal;
      result.params = this.multipleSelection;
      console.log(result);
      submitCart(result).then(res => { //创建任务 数据提交
          Console.log(res)
      })
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    loadData() {
      out(this.query).then(res => {
          this.tableData = res.data.data;
          this.total = res.data.total;
        }).catch(error => {
          this.$router.push({
            path: "/storage_action/move_stock/goodslist"
          });
        });
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
        }, 1000);
    },
     toShelf() {
      this.$router.push({
        path: "/storage_action/moveshop/goodslist"
      });
    },

    childByValue: function(childValue) {
      // childValue就是子组件传过来的值
      stockIn(childValue).then(res => {
        this.$message({
          message: "移动成功！",
          type: "success"
        });
        this.$router.push({
          path: "/storage_action/move_stock"
        });
      });
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      delete this.query.page;
      delete this.query.limit;
      var goodsIdsList = [];
      for (let i = 0; i < this.multipleSelection.length; i++) {
        goodsIdsList.push(this.multipleSelection[i].yiku_no);
      }
      let tableDataList = [];
      for (let i = 0; i < this.tableData.length; i++) {
        tableDataList.push(this.tableData[i].id);
      };  
      if(Array.isArray(goodsIdsList) && goodsIdsList.length !== 0){  // 勾选商品
          window.open(
            "/api/yiku/export?id=" + goodsIdsList + "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
            "_blank"
          );
      }else{  // 没有勾选商品
         if(!this.query.yiku_no && !this.query.PRODCHINM && !this.query.NewPRODUCTCD && !this.query.PRODUCTCD
          && !this.query.available_time && !this.query.origin_stock_name && !this.query.stock_no  && !this.query.to_stock_no
          && !this.query.state_name && !this.query.status && !this.query.created_at && !this.query.starttime && 
          !this.query.endtime && !this.query['create_user.username'] && !this.query['deal_user.username']){ 
            if(this.multipleSelection.length === 0){
                this.$message({
                  message: "请选择商品或搜索商品！",
                  type: "warning"
                });
                return;
            };
         }else{
            if(goodsIdsList){
                window.open(
                  "/api/yiku/export?query=" + JSON.stringify(this.query) + "&token=" + token[1],
                  "_blank"
                );
            }else{
                window.open(
                  "/api/yiku/export?id=" + tableDataList + "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
                  "_blank"
                );
            };               
         }
      }; 
      localStorage.sellOut_query = JSON.stringify(this.query);
      //清空所有的勾选状态
      this.$refs.multipleTable.clearSelection()
      this.loadData();
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
