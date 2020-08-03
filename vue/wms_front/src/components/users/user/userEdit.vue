<template>
  <div class="form-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/users/user' }">用户管理</el-breadcrumb-item>
        <el-breadcrumb-item>{{this.$route.params.userid?'编辑用户':'新增用户'}}</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="form-content">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
        <el-form-item label="用户ID" v-if="this.$route.params.userid">
          <span>{{this.$route.params.userid}}</span>
        </el-form-item>
        <el-form-item label="用户名" prop="username">
          <el-input v-model.trim="ruleForm.username"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="password">
          <el-input v-model.trim="ruleForm.password"></el-input>
        </el-form-item>
        <el-form-item label="用户角色" prop="role_id">
          <el-select v-model="ruleForm.role_id" placeholder="请选择角色" style="display:block;">
            <el-option v-for="item in roles" :key="item.id" :label="item.role_name" :value="item.id"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model.trim="ruleForm.email"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="phone">
          <el-input v-model.trim="ruleForm.phone"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">{{this.$route.params.userid?'编辑用户':'立即创建'}}</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { getAll } from '@/api/role'
import { createUser, updateUser, getById } from '@/api/user'
import { validateEmail, isvalidPhone } from '@/utils/validate'
export default {
  data() {
    const validatePhone = (rule, value, callback) => {
      if (value === '') callback()
      if (!isvalidPhone(value)) {
        callback(new Error('请输入正确的手机号!'))
      } else {
        callback()
      }
    }
    const isvalidEmail = (rule, value, callback) => {
      if (value === '') callback()
      if (!validateEmail(value)) {
        callback(new Error('请输入正确的邮箱!'))
      } else {
        callback()
      }
    }
    return {
      roles: [],
      ruleForm: {
        id: '',
        username: '',
        password: '',
        email: '',
        phone: '',
        role_id: undefined
      },
      rules: {
        role_id: [
          { required: true, message: '请选择角色', trigger: 'change', type: 'number' }
        ],
        username: [
          { required: true, message: '请输入用户名', trigger: 'blur' },
          { min: 2, max: 20, message: '长度在 2 到 20 个字符', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请输入密码', trigger: 'blur' },
          { min: 6, max: 20, message: '长度在 6 到 20 个字符', trigger: 'blur' }
        ],
        phone: [{ required: false, trigger: 'blur', validator: validatePhone }],
        email: [{ required: false, trigger: 'blur', validator: isvalidEmail }],
      }
    }
  },
  created() {
    this.getRoles()
    if (this.$route.params.userid) this.loadData(this.$route.params.userid)
  },
  methods: {
    getRoles() {
      getAll().then(res => {
        this.roles = res.data
      })
    },
    loadData(id) {
      getById({ id: id }).then(res => {
        Object.assign(this.ruleForm, res.data)
      })
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.ruleForm.id && this.ruleForm.id > 0) {
            updateUser(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '修改成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/user' })
              }
            })
          } else {
            createUser(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '创建成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/user' })
              }
            })
          }
        }
      })
    },
    resetForm(formName) {
      this.$refs[formName].resetFields()
    }
  }
}
</script>

<style lang="less">
.form-container {
  height: auto;
  overflow: hidden;

  .form-content {
    height: auto;
    overflow: hidden;
    padding: 15px;
    background: #fff;
    .form-wraper {
      width: 500px;
      .line {
        text-align: center;
      }
    }
  }
}
</style>