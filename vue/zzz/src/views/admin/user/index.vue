<template>
  <div class="app-container">
    <el-table v-loading="listLoading" :data="list" element-loading-text="Loading" border fit highlight-current-row>
      <el-table-column align="center" label="ID" width="95">
        <template slot-scope="scope">{{ scope.$index }}</template>
      </el-table-column>
      <el-table-column label="用户名" min-width="110" align="center">
        <template slot-scope="scope">{{ scope.row.username }}</template>
      </el-table-column>
      <el-table-column label="邮箱" min-width="110" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.email }}</span>
        </template>
      </el-table-column>
      <el-table-column label="最近登录IP" min-width="110" align="center">
        <template slot-scope="scope">{{ scope.row.lastLoginIp }}</template>
      </el-table-column>
      <el-table-column class-name="status-col" label="角色" min-width="110" align="center">
        <template slot-scope="scope">
          <el-tag :type="scope.row.is_admin | adminFilter">{{ scope.row.is_admin === 0 ? '普通用户' : '管理员' }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="created_at" label="创建时间" min-width="200">
        <template slot-scope="scope">
          <i class="el-icon-time" />
          <span>{{ scope.row.created_at }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="操作" width="200">
        <template slot-scope="scope">
          <div class="action-column">
            <el-button size="mini" type="warning" @click.native.prevent="handleModify(scope.row.id)">修改密码</el-button>
            <el-button size="mini" type="danger" @click.native.prevent="destroy(scope.row)">删除</el-button>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="修改密码" :visible.sync="dialogVisible" width="30%" center>
      <el-form ref="passwdForm" label-position="right" label-width="80px" :model="passwdForm" :rules="rules">
        <el-form-item label="密码" prop="password" style="width: 70%;">
          <el-input v-model="passwdForm.password"></el-input>
        </el-form-item>
        <el-form-item label="确认密码" prop="confirm_password" style="width: 70%;">
          <el-input v-model="passwdForm.confirm_password"></el-input>
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
import { getList, destroy, modifyPassword } from "@/api/admin/user";

export default {
  filters: {
    adminFilter(is_admin) {
      const roleMap = {
        1: "success",
        0: "gray"
      };
      return roleMap[is_admin];
    }
  },
  data() {
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不能小于6位"));
      } else if (value.length > 32) {
        callback(new Error("密码不能超过32位"));
      } else {
        callback();
      }
    };
    const validateConfirmPassword = (rule, value, callback) => {
      if (value !== this.passwdForm.password) {
        callback(new Error("密码和确认密码不一致"));
      } else {
        callback();
      }
    };
    return {
      list: null,
      listLoading: true,
      dialogVisible: false,
      passwdForm: {
        id: 0,
        password: '',
        confirm_password: ''
      },
      rules: {
        password: [
          { required: true, trigger: "blur", validator: validatePassword }
        ],
        confirm_password: [
          { required: true, trigger: "blur", validator: validateConfirmPassword }
        ]
      }
    };
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      this.listLoading = true;
      getList().then(response => {
        this.list = response.data;
        this.listLoading = false;
      });
    },
    destroy(row) {
      this.$confirm("此操作将永久删除该用户, 是否继续?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          destroy({ id: row.id }).then(response => {
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
          this.$message({ type: "info", message: "已取消删除" });
        });
    },
    handleModify(id) {
      this.passwdForm.id = id
      this.passwdForm.password = ''
      this.passwdForm.confirm_password = ''
      this.dialogVisible = true
    },
    handleSubmit() {
      this.$refs.passwdForm.validate(valid => {
        if (valid) {
          modifyPassword(this.passwdForm).then(response => {
            this.$notify({
              title: "成功",
              message: "修改成功",
              type: "success",
              duration: 1500
            });
            this.fetchData();
            this.dialogVisible = false;
          });
        }
      });
    }
  }
};
</script>
