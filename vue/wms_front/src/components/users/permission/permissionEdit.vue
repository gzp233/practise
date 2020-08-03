<template>
  <div class="form-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/users/permission' }">权限管理</el-breadcrumb-item>
        <el-breadcrumb-item>{{this.$route.params.permissionid?'编辑权限':'新增权限'}}</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="form-content">
      <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="120px" class="form-wraper">
        <el-form-item label="权限ID" v-if="this.$route.params.permissionid">
          <span>{{this.$route.params.permissionid}}</span>
        </el-form-item>
        <el-form-item label="父id:" prop="parent_id">
          <el-select v-model="parent_id" placeholder="请选择" collapse-tags>
              <el-option :value="mineStatusValue" style="height: 100%">
                <el-tree 
                :data="data" 
                node-key="id" 
                ref="tree"               
                highlight-current 
                show-checkbox
                check-strictly
                :props="defaultProps" 
                @node-click="handleNodeClick" 
                @check-change="handleCheckChange">                
                </el-tree>
            </el-option>
          </el-select>
        </el-form-item>
        <!-- 新增 -->
        <!-- <el-form-item label="父id:" prop="parent_id" v-else-if="!this.$route.params.permissionid">
          <el-select v-model="parent_id" placeholder="请选择" collapse-tags>
              <el-option :value="mineStatusValue" style="height: 100%">
                <el-tree 
                :data="data" 
                node-key="id" 
                ref="tree"               
                highlight-current 
                show-checkbox
                check-strictly
                :props="defaultProps" 
                @node-click="handleNodeClick" 
                @check-change="handleCheckChange">                
                </el-tree>
            </el-option>
          </el-select>
        </el-form-item>          -->
        <el-form-item label="权限名称" prop="permission_name">
          <el-input v-model.trim="ruleForm.permission_name"></el-input>
        </el-form-item>
        <el-form-item label="权限描述" prop="desc">
          <el-input type="textarea" v-model.trim="ruleForm.desc"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">{{this.$route.params.permissionid?'提交':'立即创建'}}</el-button>
          <el-button @click="resetForm('ruleForm')">重置</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
import { createPermission, updatePermission, getById,getTree } from '@/api/permission'
export default {
  data() {
    return {
      parent_id: "",
      mineStatusValue: "",
      checkedId: '',
      parent_arr: [],
      data: [],
      stateName: "",
      defaultProps: {
        children: "children",
        label: "permission_name"
      },
      ruleForm: {
        parent_id: '',
        permission_name: '',
        desc: ''
      },
      rules: {
        permission_name: [
          { required: true, message: '请输入权限名称', trigger: 'blur' },
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
    getTree().then(res =>{
        this.data = res.data;
        for(let i=0;i<this.data.length;i++){
           if(this.stateName === this.data[i].id){
               this.parent_id = this.data[i].permission_name  
           };
        };
    });
    if (this.$route.params.permissionid) this.loadData(this.$route.params.permissionid)
  },
  methods: {    
    handleNodeClick(data) {
      this.ruleForm.parent_id = this.checkedId = data.id;
      this.parent_id = data.permission_name;
      // this.$refs.tree.setCheckedNodes([data]);
      // let res = this.$refs.tree.getCheckedNodes(true, true);
      // let arrLabel = [];
      // let arr = [];
      // res.forEach(item => {
      //   arrLabel.push(item.permission_name);
      //   arr.push(item);
      // });
      // this.mineStatus = arrLabel;
      console.log("NodeClick",data)
    },     
    handleCheckChange(data, checked, node) {
       if(checked) {
            this.ruleForm.parent_id = this.checkedId = data.id;
            this.parent_id = data.permission_name;
            //this.mineStatusValue = data.permission_name;
            this.$refs.tree.setCheckedKeys([data.id]);
            // let res = this.$refs.tree.getCheckedNodes(true, true);
            // let arrLabel = [];
            // let arr = [];
            // res.forEach(item => {
            //   arrLabel.push(item.permission_name);
            //   arr.push(item);
            // });
            // //this.mineStatusValue = arr;
            // this.mineStatus = arrLabel;
            // console.log("CheckChange", this.$refs.tree.setCheckedKeys([data.id]),res)
        }else {
            // if (this.checkedId == data.id) {
            //     this.$refs.tree.setCheckedKeys([data.id]);
            // };            
        };        
    },
    loadData(id) {
      if(id){
        getById({ id: id }).then(res => {
          let arr = Object.assign(this.ruleForm, res.data);
          this.parent_arr.push(arr);
          this.stateName = res.data.parent_id;
          console.log(1,this.parent_arr)
        }); 
      }else{
        getById({ id: id }).then(res => {
          Object.assign(this.ruleForm, res.data);
          console.log(2)
        }); 
      }     
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.ruleForm.id && this.ruleForm.id > 0) {
            updatePermission(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '修改成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/permission' })
              }
            })
          } else {
            createPermission(this.ruleForm).then(response => {
              if (response) {
                this.$notify({
                  title: '成功',
                  message: '创建成功',
                  type: 'success',
                  duration: 2000
                })
                this.$router.push({ path: '/users/permission' })
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
.el-select-dropdown__item{padding:0}
.el-select-dropdown__item.selected{color:#48576a;}
.el-select-dropdown__list{padding:0}
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