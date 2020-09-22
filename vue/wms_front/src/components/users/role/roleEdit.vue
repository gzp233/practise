<template>
  <div class="form-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/users/role' }">角色管理</el-breadcrumb-item>
        <el-breadcrumb-item>{{this.$route.params.roleid?'编辑角色':'新增角色'}}</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="form-content">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
        <el-form-item label="角色ID" v-if="this.$route.params.roleid">
          <span>{{this.$route.params.roleid}}</span>
        </el-form-item>
        <el-form-item label="角色名称" prop="role_name">
          <el-input v-model.trim="ruleForm.role_name"></el-input>
        </el-form-item>
        <el-form-item label="角色描述" prop="desc">
          <el-input type="textarea" v-model.trim="ruleForm.desc"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">{{this.$route.params.roleid?'编辑角色':'立即创建'}}</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { createRole, updateRole, getById } from '@/api/role'
export default {
  data() {
    return {
      ruleForm: {
        id: '',
        role_name: '',
        desc: ''
      },
      rules: {
        role_name: [
          { required: true, message: '请输入角色名称', trigger: 'blur' },
          { min: 2, max: 64, message: '长度在 2 到 64 个字符', trigger: 'blur' }
        ],
        desc: [
          {
            min: 2,
            max: 128,
            message: '长度在 2 到 128 个字符',
            trigger: 'change'
          }
        ]
      }
    }
  },
  created() {
    if (this.$route.params.roleid) this.loadData(this.$route.params.roleid)
  },
  methods: {
    loadData(id) {
      getById({ id: id }).then(res => {
        Object.assign(this.ruleForm, res.data)
      })
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.ruleForm.id && this.ruleForm.id > 0) {
            updateRole(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '修改成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/role' })
              }
            })
          } else {
            createRole(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '创建成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/role' })
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