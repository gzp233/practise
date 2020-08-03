<template>
    <div class="grid-container">
        <div class="search-form">
            <el-form :inline="true" :model="query">
                <el-form-item label="库位编号">
                  <el-input v-model="query.stock_no" placeholder="库位编号"></el-input>
                </el-form-item>
                <el-form-item label="所属仓库">
                  <el-select v-model="query.warehouse_id" placeholder="所属仓库">
                    <el-option v-for="item in warehourse" :key="item.id" :label="item.warehouse_name" :value="item.id">{{ item.warehouse_name }}</el-option>
                  </el-select>
                </el-form-item>
                <el-form-item label="所属库区">
                  <el-select v-model="query.area_id" placeholder="所属库区">
                    <el-option v-for="item in area" :key="item.id" :label="item.area_name" :value="item.id">{{ item.area_name }}</el-option>
                  </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="doSeach">查询</el-button>
                    <el-button @click="doClear">清空</el-button>
                </el-form-item>
            </el-form>
        </div>
        <div class="grid-toolbar">
            <button class="tool-btn btn-add" @click="add">新增库位</button>
        </div>
        <div class="grid-content">
            <el-table :data="tableData" v-loading.body="loading" stripe height="800" style="width: 100%">
                <el-table-column prop="id" label="库位ID" min-width="50">
                </el-table-column>
                <el-table-column prop="stock_no" label="库位编号" min-width="100">
                </el-table-column>
                <el-table-column prop="area.warehourse.warehouse_name" label="所属仓库" min-width="100">
                </el-table-column>
                <el-table-column prop="area.area_name" label="所属库区" min-width="100">
                </el-table-column>
                <el-table-column label="创建日期" min-width="100">
                    <template slot-scope="scope">
                        <span class="color-gred">{{scope.row.created_at}}</span>
                    </template>
                </el-table-column>
                <el-table-column fixed="right" label="操作" min-width="50" class-name="action-column">
                    <template slot-scope="scope">
                        <div class="action-column">
                            <el-button type="text" size="small" @click.native.prevent="edite(scope.row)">
                                编辑
                            </el-button>
                            <!--<el-button @click.native.prevent="deleteRow(scope.row.id)" type="text" size="small">-->
                                <!--删除-->
                            <!--</el-button>-->
                            <el-button
                                    type="text"
                                    size="small"
                                    @click.native.prevent="doChange(scope.row)"
                                    v-if="scope.row.status == 0"
                            >冻结</el-button>
                            <el-button
                                    type="text"
                                    size="small"
                                    @click.native.prevent="doChange(scope.row)"
                                    v-if="scope.row.status == 1"
                            >解冻</el-button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div class="grid-page">
            <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="query.page" :page-sizes="[10, 20, 50, 100]" :page-size="query.limit" layout="total,->, prev, pager, next, jumper, sizes" :total="total">
            </el-pagination>
        </div>

    </div>
</template>

<script>
import { getIndex, del ,stockOut}from '@/api/location'
import { getAll }from '@/api/area'
import { getIndex as getList} from '@/api/warehourse'
import { getIndex as getAttributes} from '@/api/attributes'
export default {
  name: 'location',
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20,
        stock_no: '',
        warehouse_id: '',
        area_id: ''
      },
      warehourse: [],
      attributes: [],
      area:[],
      total: 0,
      loading: true,
      tableData: [],
      dialogVisible: false,
      detail: {},
    }
  },
  computed: {
      buttonName() {
          if (this.status === "0") return "冻结";
          if (this.status === "1") return "解冻";
      },
      showList() {
          let offset = this.query.limit * (this.query.page - 1);
          return this.codes.slice(offset, offset + this.query.limit);
      }
  },
  mounted: function() {    
    this.loadData() 
    getList().then(response => {
        this.warehourse = response.data.data
    })
    getAttributes().then(response => {
        this.attributes = response.data.data
    })
    getAll().then(res => {
      this.area = res.data
    });
  },
  methods: {    
    loadData: function() {
      this.loading = true
      getIndex(this.query).then(response => {
        this.total = response.data.total
        this.tableData = response.data.data
      });          
      setTimeout(() => {
        this.loading = false
      }, 2000)
    },
    deleteRow: function(id) {
      this.$confirm('确定删除该该条记录吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
          del({id: id}).then(sponse => {
              if (sponse.code == 200){
                this.$message({
                type: 'success',
                message: '删除成功!'
              })
            }
            this.loadData()
          })
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          })
        })
    },
    handleSizeChange: function(val) {
      this.query.limit = val
      this.loadData()
    },
    handleCurrentChange: function(val) {
      this.query.page = val
      this.loadData()
    },
    add: function() {
      this.$router.push({ path: '/basic/location/add' })
    },
    edite: function(row) {
      this.$router.push({
        path: '/basic/location/edit/' + row.id
      })
    },
    doSeach: function () {
      this.query.page = 1
      this.loadData()
    },
    doClear: function() {
      this.query.stock_no = ''
      this.query.warehouse_id = ''
      this.query.area_id = ''
    },
    doChange(row) {
        stockOut(row).then(response => {
         });
        this.$router.push({
            query: { row: row }
        });
      },
  }
}
</script>
<style lang="less">
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
</style>
