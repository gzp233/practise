<template>
  <div class="grid-container">
    <div class="search-form">
      <el-form :inline="true" :model="query">
        <el-form-item label="名称">
          <el-input v-model="query.role_name" placeholder="角色名称"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="doSeach">查询</el-button>
          <el-button @click="doClear">清空</el-button>
        </el-form-item>
      </el-form>
    </div>
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="add">新增角色</button>
    </div>
    <div class="grid-content">
      <el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%">
        <el-table-column type="index" label="编号" width="100">
        </el-table-column>
        <el-table-column prop="role_name" label="角色名称" min-width="250">
        </el-table-column>
        <el-table-column prop="desc" label="角色描述" min-width="300">
        </el-table-column>
        <el-table-column label="创建日期" min-width="200">
          <template slot-scope="scope">
            <span class="color-gred">{{scope.row.created_at}}</span>
          </template>
        </el-table-column>
        <el-table-column fixed="right" label="操作" min-width="200" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button type="text" size="small" @click.native.prevent="handlePermission(scope.row)">
                权限
              </el-button>
              <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">
                详情
              </el-button>
              <el-button type="text" size="small" @click.native.prevent="edit(scope.row)">
                编辑
              </el-button>
              <el-button @click.native.prevent="handleDelete(scope.row)" type="text" size="small">
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

    <!-- 详情弹框 -->
    <el-dialog title="角色详情" :visible.sync="dialogVisible" size="dl-small" :modal-append-to-body="false" :close-on-press-escape="false">
      <customer-detail :detail="detail"></customer-detail>
    </el-dialog>
  </div>
</template>

<script>
import CustomerDetail from '@/components/users/role/roleDetail'
import { fetchList, deleteRole } from '@/api/role'

export default {
  name: 'rolelist',
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20,
        role_name: '',
        sort: 'id'
      },
      loading: true,
      tableData: [],
      total: 0,
      dialogVisible: false,
      detail: {}
    }
  },
  mounted: function() {
    this.loadData()
  },
  components: {
    'customer-detail': CustomerDetail
  },
  methods: {
    loadData: function() {
      fetchList(this.query).then(res => {
        this.tableData = res.data.data
        this.total = res.data.total
      })
      this.loading = true
      setTimeout(() => {
        this.loading = false
      }, 1000)
    },
    doSeach: function() {
      this.query.page = 1
      this.loadData()
    },
    doClear: function() {
      this.query.role_name = ''
    },
    handleDelete: function(row) {
      this.$confirm('确定删除该角色吗?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          deleteRole({ id: row.id }).then(res => {
            if (res) {
              this.$message({
                type: 'success',
                message: '删除成功!'
              })
              this.loadData()
            }
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
      this.$router.push({ path: '/users/role/add' })
    },
    edit: function(row) {
      this.$router.push({
        path: '/users/role/edit/' + row.id
      })
    },
    handlePermission: function(row) {
      this.$router.push({
        path: '/users/role/permission/' + row.id
      })
    },
    showDetail: function(detail) {
      this.detail = detail
      this.dialogVisible = true
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
