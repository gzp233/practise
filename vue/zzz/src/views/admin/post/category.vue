<template>
  <div class="app-container">
    <el-row>
      <el-card class="box-card">
        <div style="margin-bottom:50px;">
          <el-col :span="4" class="text-center">
            <el-button type="primary" @click="handleAdd">新增分类</el-button>
          </el-col>
        </div>
      </el-card>
    </el-row>

    <el-table v-loading="loading" :data="list" border fit highlight-current-row style="width: 100%">
      <el-table-column min-width="150px" align="center" label="名称">
        <template slot-scope="scope">
          <span>{{ scope.row.category_name }}</span>
        </template>
      </el-table-column>

      <el-table-column width="120px" align="center" label="创建者">
        <template slot-scope="scope">
          <span>{{ scope.row.user ? scope.row.user.username : '未知' }}</span>
        </template>
      </el-table-column>

      <el-table-column width="120px" align="center" label="文章数">
        <template slot-scope="scope">
          <span>{{ scope.row.posts_count }}</span>
        </template>
      </el-table-column>

      <el-table-column min-width="200px" align="center" label="描述">
        <template slot-scope="scope">
          <span>{{ scope.row.desc }}</span>
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

    <el-pagination
      v-if="query.total > query.limit"
      background
      :page-size="query.limit"
      layout="prev, pager, next"
      @current-change="handleCurrentChange"
      :total="query.total"
    ></el-pagination>

    <el-dialog
      :title="(isEdit ? '编辑' : '新增') + '分类'"
      :visible.sync="dialogVisible"
      width="30%"
      center
    >
      <el-form
        ref="categoryForm"
        label-position="right"
        label-width="80px"
        :model="categoryForm"
        :rules="rules"
      >
        <el-form-item label="名称" prop="category_name" style="width: 70%;">
          <el-input v-model="categoryForm.category_name"></el-input>
        </el-form-item>
        <el-form-item label="描述" prop="desc">
          <el-input v-model="categoryForm.desc" type="textarea"></el-input>
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
import { getList, create, destroy, update } from "@/api/admin/postCategory";

export default {
  name: "Category",
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
      categoryForm: {
        id: "",
        category_name: "",
        desc: ""
      },
      rules: {
        category_name: [
          { required: true, message: "请输入分类名称", trigger: "blur" },
          { min: 2, max: 32, message: "长度在 2 到 32 个字符", trigger: "blur" }
        ],
        desc: [
          { required: true, message: "请填写分类描述", trigger: "blur" },
          { max: 140, message: "长度最多140个字符", trigger: "blur" }
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
      this.categoryForm = {
        id: 0,
        category_name: "",
        desc: ""
      };
    },
    handleSubmit() {
      this.$refs.categoryForm.validate(valid => {
        if (valid) {
          if (this.categoryForm.id) {
            update(this.categoryForm).then(response => {
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
            create(this.categoryForm).then(response => {
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
      this.$confirm("此操作将永久删除该分类, 是否继续?", "提示", {
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
      this.categoryForm.id = row.id;
      this.categoryForm.category_name = row.category_name;
      this.categoryForm.desc = row.desc;
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
