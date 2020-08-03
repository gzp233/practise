<template>
	<div class="form-container">
		<div class="page-bread">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/basic/warehourse' }">商品属性管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{this.$route.params.id?'编辑商品属性':'新增商品属性'}}</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="form-content">
			<el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
				<el-form-item label="商品属性ID" v-if="this.$route.params.id">
					<span>{{this.$route.params.id}}</span>
				</el-form-item>
				<el-form-item label="商品属性名称" prop="state_name">
					<el-input v-model.trim="ruleForm.state_name"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="submitForm('ruleForm')">立即创建</el-button>
					<el-button @click="resetForm('ruleForm')">重置</el-button>
				</el-form-item>
			</el-form>
		</div>
	</div>
</template>

<script>
import { save, getIndex } from '@/api/attributes'
export default {
  data() {
    return {
      ruleForm: {
        id: '',
        state_name: ''
      },
      rules: {
        state_name: [
          { required: true, message: '请输入单位名称', trigger: 'blur' },
          { min: 1, max: 16, message: '长度在 1 到 16 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  mounted: function() {
    this.resetForm('ruleForm')
    this.ruleForm.id =  this.$route.params.id
    if (this.ruleForm.id) {
      this.loadData(this.ruleForm.id)
    }
  },
  methods: {
    loadData(id) {
      getIndex({id: id}).then(sponse => {
        this.ruleForm = sponse.data.data[0]
      })
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          save(this.ruleForm).then(response => {
            if (response.code == 200) {
              this.$notify({
                title: '成功',
                message: '创建成功',
                type: 'success',
                duration: 2000
			        })
			        this.$router.push({ path: '/basic/attributes' })
            }
          })
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