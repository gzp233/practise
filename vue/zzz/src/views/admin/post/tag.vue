<template>
  <div class="app-container">
    <el-row>
      <el-card class="box-card">
        <div style="margin-bottom:50px;">
          <el-col :span="4" class="text-center">
            <el-button type="primary" @click="handleAdd">新增标签</el-button>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column min-width="150px" align="center" label="名称">
        <template slot-scope="scope">
          <span>{{ scope.row.tag_name }}</span>
        </template>
      </el-table-column>

      <el-table-column width="120px" align="center" label="文章数">
        <template slot-scope="scope">
          <span>{{ scope.row.posts_count }}</span>
        </template>
      </el-table-column>

      <el-table-column width="180px" align="center" label="创建时间">
        <template slot-scope="scope">
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>

      <el-table-column align="center" label="操作" width="200px">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" icon="el-icon-edit" @click="handleEdit(row)">编辑</el-button>
          <el-button type="danger" size="mini" icon="el-icon-delete" @click="handleDel(row.id)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination v-if="query.total > query.limit" background :page-size="query.limit" layout="prev, pager, next" @current-change="handleCurrentChange" :total="query.total"></el-pagination>

    <el-dialog :title="(isEdit ? '编辑' : '新增') + '标签'" :visible.sync="dialogVisible" width="30%" center>
      <el-form ref="tagForm" label-position="right" label-width="80px" :model="tagForm" :rules="rules">
        <el-form-item label="名称" prop="tag_name" style="width: 70%;">
          <el-input v-model="tagForm.tag_name"></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleSubmit">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getList, create, destroy, update } from "@/api/admin/postTag";

export default {
  name: "Tag",
  data() {
    return {
      list: null,
      loading: true,
      dialogVisible: false,
      isEdit: false,
      query: {
        limit: 20,
        page: 1,
        total: 0
      },
      tagForm: {
        id: "",
        tag_name: ""
      },
      rules: {
        tag_name: [
          { required: true, message: "请输入分类名称", trigger: "blur" },
          { min: 2, max: 32, message: "长度在 2 到 32 个字符", trigger: "blur" }
        ]
      }
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.loading = true;
      getList(this.query).then(response => {
        this.list = response.data;
        this.query.total = response.total;
        this.loading = false;
      });
    },
    handleCurrentChange(page) {
      this.query.page = page;
      this.fetchData();
    },
    resetForm() {
      this.tagForm = {
        id: 0,
        tag_name: ""
      };
    },
    handleSubmit() {
      this.$refs.tagForm.validate(valid => {
        if (valid) {
          if (this.tagForm.id) {
            update(this.tagForm).then(response => {
              this.$notify({
                title: "成功",
                message: "修改成功",
                type: "success",
                duration: 3000
              });
              this.fetchData();
              this.dialogVisible = false;
            });
          } else {
            create(this.tagForm).then(response => {
              this.$notify({
                title: "成功",
                message: "创建成功",
                type: "success",
                duration: 3000
              });
              this.fetchData();
              this.dialogVisible = false;
            });
          }
        }
      });
    },
    handleDel(id) {
      this.$confirm("此操作将永久删除该标签, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          destroy(id).then(response => {
            this.$notify({
              title: "成功",
              message: "删除成功",
              type: "success",
              duration: 3000
            });
            this.fetchData();
          });
        })
        .catch(() => {
          this.$message({
            type: "info",
            message: "已取消删除"
          });
        });
    },
    handleAdd() {
      this.resetForm();
      this.isEdit = false;
      this.dialogVisible = true;
    },
    handleEdit(row) {
      this.isEdit = true;
      this.tagForm.id = row.id;
      this.tagForm.tag_name = row.tag_name;
      this.dialogVisible = true;
    }
  }
};
</script>

<style lang="scss" scoped>
.el-pagination {
  margin-top: 20px;
}
</style>
