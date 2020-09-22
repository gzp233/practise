<template>
	<div class="form-container">
		<div class="page-bread">
			<el-breadcrumb separator="/">
				<el-breadcrumb-item :to="{ path: '/basic/location' }">库位管理</el-breadcrumb-item>
				<el-breadcrumb-item>{{this.$route.params.id?'库位编辑':'新增库位'}}</el-breadcrumb-item>
			</el-breadcrumb>
		</div>
		<div class="form-content">
			<el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
				<el-form-item label="库位ID" v-if="this.$route.params.id">
					<span>{{this.$route.params.id}}</span>
				</el-form-item>
                
				<el-form-item label="库位编号" prop="stock_no">
					<el-input v-model.trim="ruleForm.stock_no"></el-input>
				</el-form-item>
				<el-form-item label="所属仓库" prop="warehouse_id">
                    <el-select v-model="ruleForm.warehouse_id" placeholder="请选择" @change="handleWarehouseChange">
                        <el-option v-for="item in warehourse" :key="item.id" :label="item.warehouse_name" :value="item.id">{{ item.warehouse_name }}</el-option>
                    </el-select>
				</el-form-item>
        <el-form-item label="库区" prop="area_id">
          <el-select v-model="ruleForm.area_id" placeholder="请选择">
            <el-option v-for="item in area" :key="item.id" :label="item.area_name" :value="item.id">{{ item.area_name }}</el-option>
          </el-select>
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
import { save, getById,getIndex } from '@/api/location'
import { getAll } from '@/api/warehourse'
import { indexedDBFn,deleteDB } from "@/utils/indexedDB";
export default {
  data() {
    return {
      ruleForm: {
        id: '',
        stock_no: '',
        warehouse_id: '',
        area_id: ''
      },
      warehourse: [],
      area: [],
      rules: {
        stock_no: [
          { required: true, message: '请输入库位名称', trigger: 'blur' },
          { min: 2, max: 32, message: '长度在 2 到 32 个字符', trigger: 'blur' }
        ]
      },
    }
  },
  mounted: function() {
    this.resetForm('ruleForm')
    this.ruleForm.id =  this.$route.params.id
    getAll().then(response => {
        this.warehourse = response.data
    })
    if (this.ruleForm.id) {
      this.loadData(this.ruleForm.id)
    }    
  },
  methods: {
    locationALLFn (){
        // 获取所有库位
        getIndex({
          page: 1,
          limit: 100000
        }).then(response => {
          let array = [];
          if(Array.isArray(response.data.data) && response.data.data.length !== 0){
            for(let i=0,len=response.data.data.length;i<len;i++){
                let parameters = {};
                parameters["stock_no"] = response.data.data[i]["stock_no"];
                array.push(parameters);
            };
            indexedDBFn(array);
          };
        });  
    },
    loadData(id) {
      getById({id: id}).then(sponse => {
        const data = sponse.data
        this.id = data.id
        this.ruleForm.stock_no = data.stock_no
        this.ruleForm.area_id = data.area_id
        this.ruleForm.warehouse_id = data.area.warehouse_id
      })
    },
    handleWarehouseChange(val) {
      for (let i=0; i<this.warehourse.length;i++) {
        if (this.warehourse[i].id === val) this.area = this.warehourse[i].areas
      }
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
              });
              deleteDB("myDatabase")
              this.locationALLFn();
			        this.$router.push({ path: '/basic/location' })
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