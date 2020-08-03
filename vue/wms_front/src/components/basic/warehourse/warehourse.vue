<template>
    <div class="grid-container">
        <!-- <div class="search-form">
            <el-form :inline="true" :model="formInline">
                <el-form-item label="客户类型">
                    <el-select v-model="formInline.type" placeholder="客户类型">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="名称">
                    <el-input v-model="formInline.name" placeholder="名称"></el-input>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="formInline.state" placeholder="状态">
                        <el-option label="区域一" value="shanghai"></el-option>
                        <el-option label="区域二" value="beijing"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="doSeach">查询</el-button>
                    <el-button @click="doClear">清空</el-button>
                </el-form-item>
            </el-form>
        </div> -->
        <div class="grid-toolbar">
            <button class="tool-btn btn-add" @click="add">新增仓库</button>
        </div>
        <div class="grid-content">
            <el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%">
                <el-table-column prop="id" label="仓库ID" min-width="50">
                </el-table-column>
                <el-table-column prop="warehouse_name" label="仓库名称" min-width="100">
                </el-table-column>
                <el-table-column prop="desc" label="仓库描述" min-width="150">
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
                            <el-button @click.native.prevent="deleteRow(scope.row.id)" type="text" size="small">
                                删除
                            </el-button>
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

import { getIndex, del }from '@/api/warehourse' 
export default {
  name: 'warehourse',
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20,
      },
      total: 0,
      loading: true,
      tableData: [],
      dialogVisible: false,
      detail: {}
    }
  },
  mounted: function() {
    this.loadData()
  },
  methods: {
    loadData: function() {
      this.loading = true
      getIndex(this.query).then(response => {
        this.total = response.data.total
        this.tableData = response.data.data
      })
      setTimeout(() => {
        this.loading = false
      }, 2000)
    },
    deleteRow: function(id) {
      this.$confirm('确定删除该该条记录吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          del({id: id}).then(sponse => {
              if (sponse.code == 200){
                this.$message({
                type: 'success',
                message: '删除成功!'
              })
            } 
            this.loadData()
          })
          
        })
        .catch(() => {
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
      this.$router.push({ path: '/basic/warehourse/add' })
    },
    edite: function(row) {
      this.$router.push({
        path: '/basic/warehourse/edite/' + row.id
      })
    }
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