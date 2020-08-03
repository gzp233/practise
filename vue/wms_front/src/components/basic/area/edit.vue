<template>
	<div class="form-container">
		<div class="page-bread">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/basic/area' }">库区管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{this.$route.params.id?'库区编辑':'新增库区'}}</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="form-content">
			<el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
				<el-form-item label="库区ID" v-if="this.$route.params.id">
					<span>{{this.$route.params.id}}</span>
				</el-form-item>
                
				<el-form-item label="库区编号" prop="area_name">
					<el-input v-model.trim="ruleForm.area_name"></el-input>
				</el-form-item>
				<el-form-item label="所属仓库" prop="warehouse_id">
                    <el-select v-model="ruleForm.warehouse_id" placeholder="请选择">
                        <el-option v-for="item in warehourse" :key="item.id" :label="item.warehouse_name" :value="item.id">{{ item.warehouse_name }}</el-option>
                    </el-select>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="submitForm('ruleForm')">立即{{this.$route.params.id?'更新':'创建'}}</el-button>
					<el-button @click="resetForm('ruleForm')">重置</el-button>
				</el-form-item>
			</el-form>
		</div>
	</div>
</template>

<script>
import { save, getById } from '@/api/area'
import { getAll2 } from '@/api/warehourse'
export default {
  data() {
    return {
      ruleForm: {
        id: '',
        area_name: '',
        warehouse_id: ''
      },
      warehourse: [],
      attributes: [],
      rules: {
        area_name: [
          { required: true, message: '请输入库区名称', trigger: 'blur' },
          { min: 1, max: 32, message: '长度在 1 到 32 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  mounted: function() {
    this.resetForm('ruleForm')
    this.ruleForm.id =  this.$route.params.id
    getAll2().then(response => {
        this.warehourse = response.data
    })
    if (this.ruleForm.id) {
      this.loadData(this.ruleForm.id)
    }
  },
  methods: {
    loadData(id) {
      getById({id:id}).then(res => {
        this.ruleForm = res.data
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
			        this.$router.push({ path: '/basic/area' })
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