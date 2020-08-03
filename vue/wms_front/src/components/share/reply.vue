<template>
  <!-- 确认内容 -->
  <div>
    <div class="detail-content">
      <el-tabs activeName="detail">
        <el-tab-pane label="待确认" name="detail">
          <el-button size="nromal" type="primary" class="warehouse-select" @click="handleAdd">新增</el-button>
          <el-button size="nromal" type="primary" @click="submitStock" class="warehouse-select">确认回传</el-button>
          <el-table
            :data="parentList"
            @selection-change="handleSelectionChange"
            stripe
            v-loading.body="loading"
            ref="totalTable"
            style="width: 100%">
            <el-table-column type="selection" width="55"></el-table-column>
            <el-table-column prop="PRODCHINM" label="产品中文名称"  :render-header="serachHeader" column-key="PRODCHINM" min-width="300"></el-table-column>
            <el-table-column prop="NewPRODUCTCD" label="新产品代码" :render-header="serachHeader" column-key="NewPRODUCTCD" min-width="150"></el-table-column>
            <el-table-column prop="PRODUCTCD" label="产品代码" :render-header="serachHeader" column-key="PRODUCTCD" min-width="150"></el-table-column>
            <el-table-column prop="available_time" label="效期" min-width="150"></el-table-column>
            <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
            <el-table-column prop="number" label="可回传数量" min-width="150"></el-table-column>
            <el-table-column label="可分配数量" min-width="150">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <span>{{leaves(scope.row.id)}}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="确认数量" min-width="150">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <el-input v-model.number="scope.row.act_number" @keyup.native.prevent="inputKeyup(scope.row)" size="mini" placeholder="（需填写数值）"></el-input>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="状态" min-width="150">
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
          </el-table>
          <el-pagination
            @size-change="handleQSizeChange"
            @current-change="handleQCurrentChange"
            :current-page.sync="q.page"
            :page-sizes="[10, 20, 50, 100]"
            :page-size="q.limit"
            layout="total,->, prev, pager, next, jumper, sizes"
            :total="foo.filter(item => {
        return checkedIds.indexOf(item.id) < 0
      }).length"
          ></el-pagination>
        </el-tab-pane>
      </el-tabs>
    </div>
    <div class="detail-content">
      <el-tabs activeName="table">
        <el-tab-pane label="确认明细" name="table">
          <el-table :data="showList" v-loading.body="loadingDetail" stripe style="width: 100%">
            <el-table-column prop="PRODCHINM" label="产品中文名称" min-width="300"></el-table-column>
            <el-table-column prop="NewPRODUCTCD" label="新产品代码" column-key="NewPRODUCTCD" :render-header="serachHeaderDetail"  min-width="150"></el-table-column>
            <el-table-column prop="PRODUCTCD" label="产品代码" column-key="PRODUCTCD" :render-header="serachHeaderDetail" min-width="150"></el-table-column>
            <el-table-column prop="available_time" label="效期" min-width="150"></el-table-column>
            <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
            <el-table-column prop="act_number" label="确认数量" min-width="150"></el-table-column>
            <el-table-column label="状态" min-width="150">
              <template slot-scope="scope">
                <span>{{scope.row.act_state_name}}</span>
              </template>
            </el-table-column>
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
import { getAll as getAllStates } from "@/api/attributes";
import { getByNo } from "@/api/procurement_storage";
import { setTimeout } from 'timers';
export default {
  props: ["goodsData"],
  data() {
    return {
      checked: [],
      attributesList: [],
      postGoodsList: [],
      currentPage: 1,
      loading:false,
      loadingDetail:false,
      query: {
        limit: 10,
        page: 1,
        id:this.$route.params.id
      },
      q: {
        limit: 10,
        page: 1
      },
      search: '',
      foo: [],
      queryLocation: {},
      storageInDetail: [],
    };
  },
  created() {
    this.loadAttributes();
  },
  watch:{
    'goodsData.goodsList':{
        handler(newV,oldV){
          [...this.foo] = newV;
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
            total = this.goodsData.goodsList[k].number;
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
          map[this.storageInDetail[i].id] = {act:0,todo:this.storageInDetail[i].number}
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
      return this.foo.filter(item => {
        return ids.indexOf(item.id) < 0
      }).slice(offset, offset + this.q.limit);
    }
  },
  methods: {
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
					this.loadingDetail = true;
					setTimeout(() => {
						this.loadingDetail = false;
					}, 1000);
      }else{
         this.storageInDetail = this.postGoodsList.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase()))

         this.loadingDetail = true;
         setTimeout(() => {
          this.loadingDetail = false;
         }, 1000);
      };
    },
    //自定义表头
    serachHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.query[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.searchCol(this.query[column.columnKey],column.columnKey)}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    searchCol(search,label){
      console.log(search,label)
      if(!search){
          this.$parent.parentReceive();
					this.loading = true;
					setTimeout(() => {
						this.loading = false;
					}, 1000);
      }else{
         this.foo = this.foo.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase()))
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
      console.log(this.parentList)
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
            message: "请填写确认数量",
            type: "warning"
          });
          return;
        }
        if (!this.checked[i].act_state_name) {
          this.$message({
            message: "请选择状态",
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
        if (leaves == parseInt(this.checked[i].act_number)) {
          toDel.push(this.checked[i]);
        }
        let d = new Date();
        tmp.index =
          d.getTime().toString() + Math.floor(Math.random() * 10000).toString();
        this.postGoodsList.push(tmp);
      }
      // 删除为0的
      if (toDel.length > 0) {
        for (let a = 0; a < toDel.length; a++) {
          this.goodsData.goodsList.splice(
            this.goodsData.goodsList.indexOf(toDel[a]),
            1
          );
        }
      }
      // 数组用来记住第一个的下标
      var notUniq = {};
      var arr = [];
      for (let i = 0; i < this.postGoodsList.length; i++) {
        let tmp = this.postGoodsList[i];
        let str = tmp.id + "-A-" + tmp.act_state_name;
        if (notUniq[str]) {
          notUniq[str].act_number += tmp.act_number;
        } else {
          notUniq[str] = tmp;
        }
      }
      for (let i in notUniq) {
        arr.push(notUniq[i]);
      }
      this.postGoodsList = arr;
      this.$message({
        message: "添加成功",
        type: "success"
      });
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
          message: "请选择确认的商品",
          type: "warning"
        });
        return;
      }
      this.$emit("childByValue", this.postGoodsList);
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
