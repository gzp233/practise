<template>
  <div class="grid-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <!-- <el-breadcrumb-item :to="{ path: '/storage_action/move_stock' }">移动区</el-breadcrumb-item> -->
        <el-breadcrumb-item>库内移动</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="detail-content">
      <div>
        <el-button type="primary" @click="taskBack">返回</el-button>
        <el-button type="primary" @click="createTask">创建任务</el-button>
        <el-button type="primary" @click="del">删除</el-button>
      </div>
      <el-tabs activeName="table">
        <el-tab-pane label="商品明细" name="table">         
          <el-table :data="showList" stripe v-loading.body="loading" style="width: 100%" @selection-change="handleSelectionChange">
            <el-table-column type="selection" width="55" :selectable="selectable"></el-table-column> 
            <el-table-column prop="PRODCHINM" label="产品中文名称" min-width="170" :render-header="serachHeader" column-key="PRODCHINM"></el-table-column>
            <el-table-column prop="NewPRODUCTCD" label="新产品代码" min-width="140" :render-header="serachHeader" column-key="NewPRODUCTCD"></el-table-column>
            <el-table-column prop="PRODUCTCD" label="产品代码" min-width="115" :render-header="serachHeader" column-key="PRODUCTCD"></el-table-column>
            <el-table-column prop="available_time" label="效期" min-width="90" :render-header="serachHeader" column-key="available_time"></el-table-column>
            <el-table-column prop="state_name" label="良品标志" min-width="115" :render-header="serachHeader" column-key="origin_stock_name"></el-table-column>
            <el-table-column label="可用数量" min-width="100">
              <template slot-scope="scope">
                <div class="table-form-item">
                  <span>{{leaves(scope.row.ids)}}</span>
                </div>
              </template>
            </el-table-column>
            <el-table-column prop="stock_no" label="移出库位" min-width="115" :render-header="serachHeader" column-key="stock_no"></el-table-column>
            <el-table-column label="移入库位" min-width="125" :render-header="serachHeader" column-key="to_stock_no">             
              <template slot-scope="scope">                
                  <el-select
                    v-model="scope.row.to_stock_no"
                    filterable
                    remote
                    reserve-keyword
                    size="small"                    
                    :remote-method="loadLocations"
                    :loading="loading2"
                    placeholder="请选择库位">
                    <el-option
                      v-for="stock in locationList"
                      :key="stock.id"
                      :label="stock.stock_no"
                      :value="stock.stock_no"
                    ></el-option>
                  </el-select>                
              </template>
            </el-table-column>
            <el-table-column label="移入数量" min-width="115">
              <template slot-scope="scope">
                <el-input v-model.number="scope.row.to_number"  @keyup.native.prevent="LimitTimer(scope.row)" placeholder="请输入内容"></el-input>
              </template>
            </el-table-column>
            <el-table-column label="移入效期" min-width="230" :render-header="serachHeader" column-key="to_available_time">
              <template slot-scope="scope">
                <el-date-picker v-model="scope.row.to_available_time" type="month" value-format="yyyy-MM-dd" format="yyyy-MM" @change="selectMonth($event, scope.row)" placeholder="选择日期"></el-date-picker>
              </template>
            </el-table-column>
            <el-table-column label="移入良品标志" min-width="150" :render-header="serachHeader" column-key="origin_stock_name">
              <template slot-scope="scope">
                <el-select v-model="scope.row.to_state_name"  filterable placeholder="请选择">
                  <el-option
                    v-for="item in tagOptions"
                    :key="item.id"
                    :label="item.state_name"
                    :value="item.state_name">
                  </el-option>
                </el-select>
              </template>             
            </el-table-column>            
            <el-table-column fixed="right" label="操作" width="120">
              <template slot-scope="scope">
                <el-button @click.native.prevent="deleteRow(scope.$index, scope.row)" type="text" size="small">删除 </el-button>
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
        </el-tab-pane>
      </el-tabs>
    </div>
    <el-dialog title="收货地址" :visible.sync="dialogVisible" :modal-append-to-body='false'>
      <el-select v-model="value" placeholder="请选择处理人账号" @change="changeLocationValue">
          <el-option
          v-for="item in options"
          :key="item.username"
          :label="item.username"
          :value="item.deal">
          </el-option>
      </el-select>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="confirm">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getGoodsByIds, stockIn } from "@/api/move_stock";
import { delCart, cartList, submitCart, getList} from "@/api/out";
import { fetchList } from "@/api/goods";
import { getAll as getAllStates } from "@/api/attributes";
import { getLocations } from "@/api/location";

export default {  
  data() {
    return {
      goodsList: [],
      outLocation:"",
      inLocation:"",
      inNumber:"",
      dialogVisible: false,
      selectVal:"",
      value: '',
      options: [],
      query: {
        limit: 10,
        page: 1
      },
      multipleSelection:[],
      selectName:"",
      tagOptions:[],
      loading:true,
      loading2:false,
      locationList: [],
      list2: [],
      states: [],
      area_ids:["1","2","3"],
      search: ''
    };
  },
  created() {
    this.loadData();
    this.list();
    getAllStates().then(res =>{
      for (const key in res.data) {
        this.tagOptions[key] = res.data[key]
      }
    })
  },
  mounted() {
    // getLocations({area_ids: this.area_ids}).then(res =>{
    //   console.log(res);
    //   for(let i=0;i<res.data.length;i++){
    //      this.states.push(res.data[i].stock_no)
    //   };
    //   this.list2 = this.states.map(item => {
    //     return { value: item, label: item };
    //   });
    // });    
  },
  computed: {
    showList() {
      let offset = this.query.limit * (this.query.page - 1);
      return this.goodsList.slice(offset, offset + this.query.limit);
    },
    leaves() {
      return ids => {
        let total = 0;
        for (let k = 0; k < this.goodsList.length; k++) {
          if (this.goodsList[k].ids == ids) {
            total = this.goodsList[k].number;
          }
        }        
        return total;
      };
    },
  },
  methods: { 
    loadLocations(query){
        this.loading = true;
      this.locationList = [];
      if (query === "") {
        this.loading = false;
        return
      }
        getLocations({area_ids: this.area_ids,"stock_no":query}).then(res =>{
          for(let i=0;i<res.data.length;i++) {
            res.data[i].value = res.data[i].stock_no
          }
          this.locationList = res.data
          this.loading = false
        
        });  
        
    },
    LimitTimer(row){
      let leaves = this.leaves(row.ids);
      if (leaves < row.to_number) {
        this.$message({
          message: "商品数量不能超过可分配数量",
          type: "warning"
        });
        row.to_number = leaves;
      }
    },
    taskBack(){
      this.$router.push({
        path:"/storage_action/moveshop/goodslist"
      })
    },    
    list(){
      getList().then(res =>{
        let account = res.data;        
        for(let i=0;i<account.length;i++){
           let accountObj = {};
           accountObj.deal = account[i].id
           accountObj.username = account[i].username
           this.options.push(accountObj)
        };    
      })
    },   
    selectable(row, index){
      //console.log(row, index)
      if(row.to_number == ""){
        return true
      }else{
        return true
      }
    },
    deleteRow(index, row) {  //删除
        let arr = [];        
        //arr.push(row.id); 
        arr.push(row.product_id +'_'+row.stock_no +'_'+row.state_name+'_'+row.available_time.replace('-', '')); 
        this.$confirm('此操作将从购物车删除该商品, 是否继续?', '提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.$message({
                type: 'success',
                message: '商品已删除!'
              });
              delCart({"keys":arr}).then(res =>{
                this.loadData();
              });
            }).catch(() => {
              this.$message({
                type: 'info',
                message: '已取消删除'
              });          
            }); 
    },     
    selectMonth($event, row){  
      row.to_available_time = $event;
    },
    del(){  //删除
      let arr = []; 
      if(this.multipleSelection.length == 0){
         alert("请选择删除的商品！");
         return;
      }else{
        for(let i=0;i<this.multipleSelection.length;i++){
          arr.push(this.multipleSelection[i].product_id +'_'+this.multipleSelection[i].stock_no +'_'+this.multipleSelection[i].state_name+'_'+this.multipleSelection[i].available_time.replace('-', ''));
        };
        this.$confirm('此操作将从购物车删除该商品, 是否继续?', '提示', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              this.$message({
                type: 'success',
                message: '删除成功!'
              });
              delCart({"keys":arr}).then(res =>{
                this.loadData();
              })
            }).catch(() => {
              this.$message({
                type: 'info',
                message: '已取消删除'
              });          
            });        
      };
    },    
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
       this.goodsList = this.goodsList.filter(data => !search || data[label].toLowerCase().includes(search.toLowerCase()))          
       this.loading = true;
       setTimeout(() => {
        this.loading = false;
       }, 1000);               
    },
    handleSelectionChange(val) {  // 勾选
      this.multipleSelection = val;     
    },
    changeLocationValue(val){
       this.selectVal = val;
    },    
    confirm(){  //创建任务  确定
      this.dialogVisible = false;
      let result = {};
      result.deal_user = this.selectVal;
      result.params = this.multipleSelection;      
      submitCart(result).then(res => { //创建任务 数据提交
          if(Object.is(res.code,200)){
             this.$message("任务成功指派！");
             this.loadData();
          };          
      })
    },
    createTask(){  //创建任务   
       if (this.multipleSelection.length === 0) {
        this.$message({ 
          message: "请选择商品！",
          type: "warning"
        });
        return;
      };   
      for (let i = 0; i < this.multipleSelection.length; i++) {
        if(this.multipleSelection[i].to_stock_no == ""){
           alert("请输入移入库位！");
           return false;           
        }else{
          if(this.multipleSelection[i].to_number == "" || this.multipleSelection[i].to_number == 0 || this.multipleSelection[i].to_number < 0 || Number(this.multipleSelection[i].to_number) > Number(this.multipleSelection[i].number)){
            alert("请输入正确的移入数量");
            return false;           
          }else{
            continue;
          };  
          continue;
        };              
      }; 
      this.dialogVisible = true;
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
    },
    loadData() {
      cartList(this.query).then(res => {
          this.goodsList = res.data;          
          for (let i = 0; i < this.goodsList.length; i++) {
            this.goodsList[i].old_number = this.goodsList[i].number;
            this.goodsList[i].available_time=this.goodsList[i].available_time.substr(0,4)+'-'+this.goodsList[i].available_time.substr(4,5)            
            this.$set(this.goodsList[i], 'to_state_name', this.goodsList[i].state_name)
            this.$set(this.goodsList[i], 'to_number', "")
            this.$set(this.goodsList[i], 'to_stock_no', "")
            this.$set(this.goodsList[i], 'to_available_time', this.goodsList[i].available_time)
          }
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
    }
  }
};
</script>
<style lang="less">
.el-select .el-input__inner{padding-right:0;}
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