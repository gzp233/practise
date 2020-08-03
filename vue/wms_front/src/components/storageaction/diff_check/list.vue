<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="toShelf">返回</button>
      <!--<button class="tool-btn btn-add" @click="batches">分批</button>-->
      <button class="tool-btn btn-add" @click="Shelf">差异报告</button>
      <button class="tool-btn btn-add" @click="difference">生成差异盘点单</button>
    </div>

    <div class="grid-content">
      <el-table
              :data="tableData"
              v-loading.body="loading"
              @selection-change="handleSelectionChange"
              stripe
              border
              style="width: 100%"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column
                prop="batches"
                :render-header="serachHeader"
                column-key="batches"
                label="批号"
                width="200"
        ></el-table-column>
        <el-table-column prop="is_diff" label="状态" width="200" column-key="is_diff" :render-header="handleStatus"></el-table-column>
        <el-table-column prop="created_at" label="创建时间" width="200"></el-table-column>
        <el-table-column prop="pd_start" label="开始时间" width="200"></el-table-column>
        <el-table-column prop="pd_end" label="结束时间" width="200"></el-table-column>
        <el-table-column label="操作" width="150" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">详情</el-button>
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-dialog
              title="移动盘点"
              :visible.sync="dialogVisible"
              width="50%"
              :modal-append-to-body="false">
        <div class="selected-title">
        <span style="margin-left:10px;">
          盘点单号:<span>{{box_code}}</span>
        </span>
        </div>
        <div class="code-div">
          <el-input v-model.trim="tmpInput" placeholder="备注" ></el-input>
        </div>
        <div>
          <el-radio v-model="radio" label="0">实盘</el-radio>
          <el-radio v-model="radio" label="1">盲盘</el-radio>
        </div>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">取 消</el-button>
          <el-button :disabled="buttonDisabled" type="primary" @click="doCreate">确 定</el-button>
        </span>
      </el-dialog>
    <div class="grid-page">
      <el-pagination
              @size-change="handleSizeChange"
              @current-change="handleCurrentChange"
              :current-page.sync="query.page"
              :page-sizes="[10, 20, 50, 100]"
              :page-size="query.limit"
              layout="total,->, prev, pager, next, jumper, sizes"
              :total="total"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
  import { getGoodsByProductId, getByNo, relieve ,verify,batches} from "@/api/diff_check";
  import { getToken } from "@/utils/auth";

  export default {
    name: "rolelist",
    data() {
      return {
        query: {
          page: 1,
          limit: 20,
          status: 2
        },
        loading: true,
        tableData: [],
        wareHouseList: [],
        total: 0,
        goodsList: [],
        checked: [],
        check: [],
        dialogVisible: false,
        tmpInput:"",
        radio:"0",
        buttonDisabled:false,
        postGood:[],
        box_code: "",
        q: {
          limit: 10,
          page: 1
        },
        old_val:""
      };
    },
    created() {
      this.loadData(this.$route.params.id);
    },
    computed: {
      leaves() {
        return id => {
          var total = 0;
          for (let k = 0; k < this.tableData.length; k++) {
            if (this.tableData[k].id == id) {
              total = this.tableData[k].number;
            }
          }
          return total;
        };
      },
      stockedList() {
        let offset = this.q.limit * (this.q.page - 1);
        return this.goodsList.slice(offset, offset + this.q.limit);
      }
    },
    methods: {
      handleStatus(h, { column, $index }, index){
         return (
                <div class="header-custom-stype">
                  <el-select v-model={this.query[column.columnKey]} on-Change={this.loadData(this.$route.params.id)} placeholder={column.label}>
                     <el-option value="未录入" label="未录入" />
                     <el-option value="有差异" label="有差异" />
                     <el-option value="无差异" label="无差异" />
                  </el-select>
                </div>
        );      
      },
      doCreate() {
        this.postGood = [];
        this.postGood.push({
          box_code:this.box_code,
          code: this.tmpInput,          
          rad: this.radio,
          old_code:this.old_val
        });
       batches(this.postGood).then(res => {
             if(Object.is(res.code,200)){
                this.$message({
                  message: "创建成功",
                  type: "success"
                });
                this.dialogVisible = false;
                this.$router.push({
                  path:"/storage_action/diff_check/"
                })
              }
         });
      },
      handleCreate() {
        this.box_code = Math.round(new Date() / 100000)+Math.floor(Math.random()*100);
        this.tmpInput = "";
        this.old_val = "";
        for(let i of this.checked){
            this.tmpInput += i.batches + "," ;
            this.old_val += i.batches + "," ;
        }; 
        this.tmpInput = this.tmpInput.substring(0,this.tmpInput.length-1);
        this.old_val = this.old_val.substring(0,this.old_val.length-1);
        this.tmpInput += "的差异盘点单";
        this.dialogVisible = true;
      },
      difference(){
        let token = getToken();
        token = token.split(" ", 2);
        var goodsIdsList = [];
        if (this.checked.length === 0) {
          this.$message({
            message: "请选择商品！",
            type: "warning"
          });
          return;
        }
        for (let i = 0; i < this.checked.length; i++) {
          goodsIdsList.push(this.checked[i].batches);
        }
        this.handleCreate();
        // this.$router.push({
        //   query: { goodsIdsList: goodsIdsList }
        // });
      },
      serachHeader(h, { column, $index }, index) {
        return (
                <div class="header-custom-stype">
                  <el-input
                          v-model={this.query[column.columnKey]}
                          placeholder={column.label}
                          nativeOn-keyup={arg => arg.keyCode === 13 && this.loadData(this.$route.params.id)}
                          prefix-icon="el-icon-search"
                  />
                </div>
        );
      },
      inputKeyup(row) {
        let leaves = this.leaves(row.id);
        if (leaves < row.valnumber) {
          this.$message({
            message: "商品数量不能超过可分配数量",
            type: "warning"
          });
          row.valnumber = leaves;
        }
      },
      loadData(id) {
        if (localStorage.goodsStock_query) {
          this.query = JSON.parse(localStorage.goodsStock_query)
          delete localStorage.goodsStock_query
        }
        this.query.id = id
        getByNo(this.query).then(res => {
          this.tableData = res.data.data;
        this.total = res.data.total;
      });
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
      }, 1000);
      },
      handleQSizeChange: function(val) {
        this.q.limit = val;
      },
      handleQCurrentChange: function(val) {
        this.q.page = val;
      },
      handleSizeChange(val) {
        this.query.limit = val;
        this.loadData(this.$route.params.id);
      },
      handleCurrentChange(val) {
        this.query.page = val;
        this.loadData(this.$route.params.id);
      },
      showDetail: function(detail) {
        localStorage.in_adjust_query = JSON.stringify(this.query);
        this.$router.push({
          path: "/storage_action/diff_check/particulars/"+detail.batches
        });
      },
      handleSelectionChange(val) {
        this.checked = val;
      },
      toShelf() {
        this.$router.push({
          path: "/storage_action/diff_check/detail/"+this.$route.params.id
        });
      },

      Shelf() {
        let token = getToken();
        token = token.split(" ", 2);
        var goodsIdsList = [];
        if (this.checked.length === 0) {
          this.$message({
            message: "请选择商品！",
            type: "warning"
          });
          return;
        }
        for (let i = 0; i < this.checked.length; i++) {
          goodsIdsList.push(this.checked[i].batches);
        }
        verify(goodsIdsList).then(res => {
          if (res.data) {
          window.open(
                  "/api/diff_check/getExport?name=" + res.data + "&token=" + token[1],
                  "_blank"
          );
        }

        localStorage.goodsStock_query = JSON.stringify(this.query)
        this.loadData(this.$route.params.id);
      });

        this.$router.push({
          query: { goodsIdsList: goodsIdsList }
        });
      },
      batches() {
        var goodsIdsList = [];
        if (this.checked.length === 0) {
          this.$message({
            message: "请选择商品！",
            type: "warning"
          });
          return;
        }
        for (let i = 0; i < this.checked.length; i++) {
          goodsIdsList.push(this.checked[i].id);
        }
        relieve(goodsIdsList).then(res => {
          goodsIdsList = res.data;
        localStorage.goodsStock_query = JSON.stringify(this.query)
        this.loadData(this.$route.params.id);
      });
        this.$router.push({
          query: { goodsIdsList: goodsIdsList }
        });
      }
    }
  };
</script>
<style lang="less">
.selected-title {
    margin: 15px 0;
    font-size: 16px;
  }
  .code-div {
    display: block;
    margin: 10px 0;
  .el-input {
    display: inline-block;
    width: 300px;
  }
  }
  .grid-container {
    height: auto;
    overflow: hidden;
  .action-column {
    text-align: right;
  }
  .color-gred {
    color: #999;
  }
  }
  .el-dialog--dl-small {
    width: 600px;
  }
  .warehouse-select {
    margin-right: 20px;
    margin-bottom: 20px;
  }
  .stock-item {
    display: block;
    margin: 10px 0;
  .stock-item-title {
    display: inline-block;
    width: 100px;
  }
  .el-input {
    width: 200px;
  }
  }
  .el-pagination {
    margin: 10px 0;
  }
</style>