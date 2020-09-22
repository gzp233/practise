<template>
  <!-- 入库内容 -->
  <div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane label="待入库" name="detail">
          <el-select
            v-model="checkedWarehouseIndex"
            placeholder="请选择仓库"
            @change="handleWarehouseChange"
            class="warehouse-select"
          >
            <el-option
              v-for="(warehouse,index) in wareHouseList"
              :key="warehouse.id"
              :label="warehouse.warehouse_name"
              :value="index"
            ></el-option>
          </el-select>

          <el-radio-group v-show="locationShow" v-model="locationStatus" @change="handleStatusChange">
            <el-radio :label="1">空库位</el-radio>
            <el-radio :label="2">非空库位</el-radio>
            <el-radio :label="3">所有</el-radio>
          </el-radio-group>

          <el-button size="nromal" type="primary" class="warehouse-select" @click="handleAdd">新增</el-button>

          <el-button size="nromal" type="primary" @click="submitStock" class="warehouse-select">确认入库</el-button>
          <el-table
            :data="parentList.goodsList"
            @selection-change="handleSelectionChange"
            stripe
            ref="multipleTable"
            style="width: 100%"
          >
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="150"></el-table-column>
            <el-table-column prop="NewPRODUCTCD" label="新产品代码" column-key="NewPRODUCTCD" :render-header="serachHeader" width="150"></el-table-column>
            <el-table-column prop="PRODUCTCD" label="产品代码" column-key="PRODUCTCD" :render-header="serachHeader" width="150"></el-table-column>
            <el-table-column v-if="!goodsData.params || !goodsData.params.is_states || goodsData.params.is_states !== 1" prop="stock_no" label="库位" min-width="150"></el-table-column>
            <el-table-column v-if="!goodsData.params || !goodsData.params.is_states || goodsData.params.is_states !== 1" prop="state_name" label="状态" min-width="150"></el-table-column>
            <el-table-column v-if="!goodsData.params || !goodsData.params.is_states || goodsData.params.is_states !== 1" prop="available_time" label="有效期" min-width="150"></el-table-column>

            <el-table-column prop="todoNumber" label="未入库数量" width="150"></el-table-column>
            <el-table-column label="可分配数量" min-width="100">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <span>{{leaves(scope.row.id)}}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="存放数量" min-width="100">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-input
                    v-model="scope.row.act_number"
                    @keyup.native.prevent="inputKeyup(scope.row)"
                    size="mini"
                    placeholder="（需填写数值）"
                  ></el-input>
                </div>
              </template>
            </el-table-column>
            <el-table-column
              label="状态"
              min-width="150"
              v-if="!goodsData.params || !goodsData.params.is_move || goodsData.params.is_move !== 1"
            >
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-select v-model="scope.row.act_state_name" filterable placeholder="请选择状态">
                    <el-option
                      v-for="attribute in attributesList"
                      :key="attribute.id"
                      :label="attribute.state_name"
                      :value="attribute.state_name"
                    ></el-option>
                  </el-select>
                </div>
              </template>
            </el-table-column>
            <el-table-column
              label="批次号"
              min-width="150"
              v-if="!goodsData.params || !goodsData.params.is_instorage || goodsData.params.is_instorage !== 1"
            >
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-input v-model.trim="scope.row.act_CHARG" size="mini" placeholder="批次号"></el-input>
                </div>
              </template>
            </el-table-column>
            <el-table-column
              label="有效期"
              min-width="150"
              v-if="!goodsData.params || !goodsData.params.is_instorage || goodsData.params.is_instorage !== 1"
            >
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-date-picker
                    v-model="scope.row.act_available_time"
                    @change="getSTime($event, scope.row)"
                    type="month"
                    format="yyyy-MM"
                    placeholder="选择月"
                  ></el-date-picker>
                </div>
              </template>
            </el-table-column>
          </el-table>
          <el-pagination
            @size-change="handleQSizeChange"
            @current-change="handleQCurrentChange"
            :current-page.sync="q.page"
            :page-sizes="[10, 20, 50, 100]"
            :page-size="q.limit"
            layout="total,->, prev, pager, next, jumper, sizes"
            :total="goodsData.goodsList.filter(item => {
          return checkedIds.indexOf(item.id) < 0
        }).length"
          ></el-pagination>
        </el-tab-pane>
      </el-tabs>
    </div>

    <div class="detail-content">
      <el-tabs activeName="table">
        <el-tab-pane label="入库明细" name="table">
          <el-table :data="showList" stripe style="width: 100%">
            <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="150"></el-table-column>
            <el-table-column prop="detailNewPRODUCTCD" label="新产品代码" column-key="detailNewPRODUCTCD" :render-header="serachHeaderDetail" min-width="150"></el-table-column>
            <el-table-column prop="detailPRODUCTCD" label="产品代码" column-key="detailPRODUCTCD" :render-header="serachHeaderDetail" min-width="150"></el-table-column>
            <el-table-column label="库位" min-width="150">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-input @blur="handleStockChange($event, scope.row)" v-model.trim="scope.row.act_stock_no" placeholder="库位号"></el-input>
                  <!-- <el-select
                    v-model="scope.row.act_stock_no"
                    @change="handleStockChange($event, scope.row)"
                    filterable
                    remote
                    reserve-keyword
                    size="small"
                    :remote-method="loadLocations"
                    :loading="loading"
                    placeholder="请选择库位"
                  >
                    <el-option
                      v-for="stock in locationList"
                      :key="stock.id"
                      :label="stock.stock_no"
                      :value="stock.stock_no"
                    ></el-option>
                  </el-select> -->
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="act_number" label="存放数量" min-width="100"></el-table-column>
            <el-table-column
              label="状态"
              min-width="100"
              v-if="!goodsData.params || !goodsData.params.is_move || goodsData.params.is_move !== 1"
            >
              <template slot-scope="scope">
                <span>{{scope.row.act_state_name}}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="act_CHARG"
              label="批次号"
              v-if="!goodsData.params || !goodsData.params.is_instorage || goodsData.params.is_instorage !== 1"
              min-width="100"
            ></el-table-column>
            <el-table-column
              prop="act_available_time"
              label="有效期"
              v-if="!goodsData.params || !goodsData.params.is_instorage || goodsData.params.is_instorage !== 1"
              min-width="150"
            ></el-table-column>
            <el-table-column label="操作" width="100">
              <template slot-scope="scope">
                <div class="action-column">
                  <el-button type="text" size="small" @click.native.prevent="delItem(scope.row)">删除</el-button>
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
            :total="storageInDetail.length"
          ></el-pagination>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
import { getAll as getWarehouseList } from "@/api/warehourse";
import { getAll as getAllStates } from "@/api/attributes";
import { getLocations } from "@/api/location";

export default {
  props: ["goodsData"],
  data() {
    return {
      locationStatus: 1,
      loading: false,
      area_ids: [],
      wareHouseList: [],
      locationList: [],
      checked: [],
      checkedWarehouseIndex: 0,
      attributesList: [],
      postGoodsList: [],
      currentPage: 1,
      query: {
        limit: 10,
        page: 1
      },
      q: {
        limit: 10,
        page: 1
      },
      queryLocation: {},
      storageIn: [],
      storageInDetail: [],
      locationShow: true,
      multipleSelection: [],
      locationRE: /^[A-Z]{1}\d{2}(\-)\d{2}(\-)\d{2}$/,
      goodsDataPage: 0
    };
  },
  created() {
    this.loadWarehouse();
    this.loadAttributes();
    if(this.$route.path.includes("instorage_action")){
       this.locationShow = false;
    } else {
       this.locationShow = true;
    }
  },
  watch:{
    'parentList.goodsList':{
          handler(newV,oldV){
            this.goodsDataPage = newV.length;
          },
          deep:true
    },
    'postGoodsList':{
        handler(newV,oldV){
          [...this.storageInDetail] = newV;
        },
        deep:true
    },
  },
  computed: {
    leaves() {
      return id => {
        let total = 0;
        for (let k = 0; k < this.goodsData.goodsList.length; k++) {
          if (this.goodsData.goodsList[k].id == id) {
            total = this.goodsData.goodsList[k].todoNumber;
          }
        }

        for (let i = 0; i < this.postGoodsList.length; i++) {
          if (this.postGoodsList[i].id == id) {
            total -= this.postGoodsList[i]["act_number"];
          }
        }
        return total;
      };
    },
    checkedIds(){
      let ids = []
      let map = []
      for (let i = 0; i< this.storageInDetail.length;i++) {
        if (!map[this.storageInDetail[i].id]) {
          map[this.storageInDetail[i].id] = {act:0,todo:this.storageInDetail[i].todoNumber}
        }
        map[this.storageInDetail[i].id].act += parseInt(this.storageInDetail[i].act_number)
        // if (ids.indexOf(this.storageInDetail[i].))
      }
      map.forEach((item, index) => {
        if (item.act == item.todo) {
          ids.push(index)
        }
      })
      return ids
    },
    showList() {
      let offset = this.query.limit * (this.query.page - 1);
      //return this.postGoodsList.slice(offset, offset + this.query.limit);
      return this.storageInDetail.slice(offset, offset + this.query.limit)
    },
    parentList() {
      let offset = this.q.limit * (this.q.page - 1);
      const ids = this.checkedIds
      return {
        goodsList: this.goodsData.goodsList.filter(item => {
          return ids.indexOf(item.id) < 0
        }).slice(offset, offset + this.q.limit)
      };
      //return this.storageIn.slice(offset, offset + this.q.limit)
    }
  },
  methods: {
    //自定义表头
    serachHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.queryLocation[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.searchCol(this.queryLocation[column.columnKey],column.columnKey)}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    searchCol(search,label){
      if(!search){
          this.$parent.loadOrder(this.$route.params.id);
					this.loading = true;
					setTimeout(() => {
						this.loading = false;
					}, 1000);
      }else{
         this.$forceUpdate();
         this.$set(this.parentList,"goodsList",this.goodsData.goodsList.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase())))
         //this.storageIn = this.goodsData.goodsList.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase()))
         this.goodsDataPage = this.parentList.goodsList.length;
         this.loading = true;
         setTimeout(() => {
          this.loading = false;
         }, 1000);
      };
    },

    //自定义表头
    serachHeaderDetail(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.queryLocation[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.searchColDetail(this.queryLocation[column.columnKey],column.columnKey)}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    searchColDetail(search,label){
      if(!search){
          this.storageInDetail = [];
          this.storageInDetail = this.postGoodsList;
					this.loading = true;
					setTimeout(() => {
						this.loading = false;
					}, 1000);
      }else{
         this.storageInDetail = this.postGoodsList.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase()))

         this.loading = true;
         setTimeout(() => {
          this.loading = false;
         }, 1000);
      };
    },
    inputKeyup(row) {
      let leaves = this.leaves(row.id);
      if (leaves < row.act_number) {
        this.$message({
          message: "商品数量不能超过可分配数量",
          type: "warning"
        });
        row.act_number = leaves;
      }
    },
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
    loadAttributes() {
      getAllStates().then(res => {
        let tmplist = res.data;
        if (
          this.goodsData.params &&
          this.goodsData.params.state_names &&
          this.goodsData.params.state_names.length > 0
        ) {
          for (let i = 0; i < tmplist.length; i++) {
            if (
              this.goodsData.params.state_names.indexOf(
                tmplist[i].state_name
              ) >= 0
            ) {
              this.attributesList.push(tmplist[i]);
            }
          }
        } else if (
          this.goodsData.params &&
          this.goodsData.params.no_state_names &&
          this.goodsData.params.no_state_names.length > 0
        ) {
          for (let i = 0; i < tmplist.length; i++) {
            if (
              this.goodsData.params.no_state_names.indexOf(
                tmplist[i].state_name
              ) < 0
            ) {
              this.attributesList.push(tmplist[i]);
            }
          }
        } else {
          this.attributesList = tmplist;
        }
      });
    },
    loadWarehouse() {
      getWarehouseList().then(res => {
        this.wareHouseList = res.data;
        this.handleWarehouseChange();
      });
    },
    handleWarehouseChange() {
      this.area_ids = [];
      for (
        let i = 0;
        i < this.wareHouseList[this.checkedWarehouseIndex].areas.length;
        i++
      ) {
        this.area_ids.push(
          this.wareHouseList[this.checkedWarehouseIndex].areas[i].id
        );
      }
    },
    handleStatusChange(val) {
      this.locationStatus = val;
    },
    loadLocations(query) {
      this.loading = true;
      this.locationList = [];
      if (query === "") {
        this.loading = false;
        return
      }
      getLocations({
        area_ids: this.area_ids,
        status: this.locationStatus,
        stock_no: query
      }).then(res => {
        this.loading = false
        let tmplist = res.data;
        if (
          this.goodsData.params &&
          this.goodsData.params.stock_names &&
          this.goodsData.params.stock_names.length > 0
        ) {
          for (let i = 0; i < tmplist.length; i++) {
            if (
              this.goodsData.params.stock_names.indexOf(tmplist[i].stock_no) >=
              0
            ) {
              tmplist[i].value = tmplist[i].stock_no;
              this.locationList.push(tmplist[i]);
            }
          }
        } else if (
          this.goodsData.params &&
          this.goodsData.params.no_stock_names &&
          this.goodsData.params.no_stock_names.length > 0
        ) {
          for (let i = 0; i < tmplist.length; i++) {
            if (
              this.goodsData.params.no_stock_names.indexOf(
                tmplist[i].stock_no
              ) < 0
            ) {
              tmplist[i].value = tmplist[i].stock_no;
              this.locationList.push(tmplist[i]);
            }
          }
        } else {
          for (let i = 0; i < tmplist.length; i++) {
            tmplist[i].value = tmplist[i].stock_no;
            this.locationList = tmplist;
          }
        }
      });
    },
    handleSelectionChange(selection) {
      this.checked = selection;
    },
    handleAdd() {
      // 校验
      if (this.checked.length === 0) {
        this.$message({
          message: "请选择添加的商品",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.checked.length; i++) {
        if (!this.checked[i].act_number || this.checked[i].act_number <= 0) {
          this.$message({
            message: "请填写存放数量",
            type: "warning"
          });
          return;
        }
        if (!this.goodsData.params || !this.goodsData.params.is_move || this.goodsData.params.is_move !== 1) {
          if (!this.checked[i].act_state_name) {
            this.$message({
              message: "请选择状态",
              type: "warning"
            });
            return;
          }
        }
        if (
          (!this.goodsData.params ||
            !this.goodsData.params.is_instorage ||
            this.goodsData.params.is_instorage !== 1) &&
          !this.checked[i].act_available_time
        ) {
          this.$message({
            message: "请填写有效期",
            type: "warning"
          });
          return;
        }
      }
      // 待删除
      var toDel = [];
      for (let i = 0; i < this.checked.length; i++) {
        let leaves = this.leaves(this.checked[i].id);
        if (leaves - this.checked[i].act_number < 0) {
          this.$message({
            message: "超过可分配数量，请重新填写",
            type: "warning"
          });
          return;
        }
        let tmp = {};
        Object.assign(tmp, this.checked[i]);
        if (leaves <= parseInt(this.checked[i].act_number)) {
          toDel.push(this.checked[i]);
        }
        let d = new Date();
        tmp.index = d.getTime().toString() + Math.floor(Math.random() * 10000).toString();
        this.postGoodsList.push(tmp);
      }
      // 清除勾选
      this.toggleSelection();
      // 删除为0的
      if (toDel.length > 0) {
        for (let a = 0; a < toDel.length; a++) {
          this.goodsData.goodsList.splice(
            this.goodsData.goodsList.indexOf(toDel[a]),
            1
          );
        }
      }
      let offset = this.q.limit * (this.q.page - 1);
      this.$set(
        this.parentList,
        "goodsList",
        this.goodsData.goodsList.slice(offset, offset + this.q.limit)
      );
      this.$message({
        message: "添加成功",
        type: "success"
      });
    },
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.multipleTable.toggleRowSelection(row);
        });
      } else {
        this.$refs.multipleTable.clearSelection();
      }
    },
    getSTime($event, row) {
      row.act_available_time = $event;
    },
    getMTime(val) {
      this.multi.available_time = val;
    },
    delItem(row) {
      var flag = 1;
      for (let k = 0; k < this.goodsData.goodsList.length; k++) {
        if (this.goodsData.goodsList[k].id == row.id) {
          flag = 0;
        }
      }
      if (flag === 1) this.goodsData.goodsList.push(row);
      this.postGoodsList.splice(this.postGoodsList.indexOf(row), 1);
    },
    submitStock() {
      // 校验
      if (this.postGoodsList.length === 0) {
        this.$message({
          message: "请选择入库的商品",
          type: "warning"
        });
        return;
      }
      for (let i = 0; i < this.postGoodsList.length; i++) {
          if (!this.postGoodsList[i].act_stock_no) {
              this.$message({
                message: "库位必选",
                type: "warning"
              });
              return;
          }
          if(!this.locationRE.test(this.postGoodsList[i].act_stock_no)){
              this.$alert(`第 ${i+1} 行库位 ${this.postGoodsList[i].act_stock_no} 格式有误，请重新填写`,"提示",{
                confirmButtonText: '确定',
              });
              return;
          }
      };
      let that = this;
      let postGoodsList = this.postGoodsList;
      let db;
      let request = window.indexedDB.open('myDatabase', 3);
      request.onsuccess = function(event) {
          db = event.target.result;
          let transaction = db.transaction(['newUsers'], 'readwrite');
          let objStore = transaction.objectStore('newUsers');
          // 使用流标 openCursor
          objStore.openCursor().onsuccess = function(e) {
              let cursor = e.target.result;
              if(!cursor){ // 初始化 添加所有库位
                  console.log('初始化')
              }else{       // 初始化以后如果再次获取所有库位则更新
                  console.log('不是初始化')
                  let getmessAll = objStore.getAll();
                  getmessAll.onsuccess = function(e) {
                        let message = e.target.result;//这是数据
                        for (let i = 0; i < postGoodsList.length; i++) {
                            let stockNo = message.some(item =>{
                              return item.stock_no === postGoodsList[i].act_stock_no;
                            });
                            if(!stockNo){
                                that.$set(postGoodsList[i],"flag", 0);
                                that.$alert(`第 ${i+1} 行的库位号${postGoodsList[i].act_stock_no}不存在`,"提示",{
                                  confirmButtonText: '确定',
                                });
                                return;
                            }else{
                                that.$set(postGoodsList[i],"flag", 1);
                                let flagStockNo = postGoodsList.every(item =>{
                                    return item.flag === 1;
                                });
                                if(flagStockNo){
                                  that.$emit("childByValue", postGoodsList);
                                }
                            };
                        };
                  };
              };
          };
        };
    },
    handleStockChange(stock_no, row) {
      // 数组用来记住第一个的下标
      var notUniq = {};
      var arr = [];
      for (let i = 0; i < this.postGoodsList.length; i++) {
        let tmp = this.postGoodsList[i];
        let str =
          tmp.id +
          "-A-" +
          tmp.act_state_name +
          "-B-" +
          tmp.act_CHARG +
          "-C-" +
          tmp.act_stock_no +
          "-D-" +
          tmp.act_available_time;
        if (tmp.act_stock_no && notUniq[str]) {
          notUniq[str].act_number += tmp.act_number;
        } else if (tmp.act_stock_no) {
          notUniq[str] = tmp;
        } else {
          notUniq[tmp.index] = tmp;
        }
      }

      for (let i in notUniq) {
        arr.push(notUniq[i]);
      }
      this.postGoodsList = arr;
    }
  }
};
</script>

<style lang="less">
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
.table-form-item {
  position: relative;
  height: 100%;
  width: 100%;
  .error-text {
    font-size: 12px;
    color: #f97042;
  }
}
.stock-item {
  display: inline-block;
  margin: 10px;
  .stock-item-title {
    display: inline-block;
    width: 75px;
  }
  .el-input {
    width: 200px;
  }
}
.warehouse-select {
  margin-right: 20px;
  margin-bottom: 20px;
}
.el-pagination {
  margin: 10px 0;
}
.multi-format {
  display: block;
  margin: 10px 0;
  .el-input {
    display: inline-block;
    width: 200px;
  }
}
</style>
