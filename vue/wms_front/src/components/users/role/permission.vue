<template>
  <div class="form-container">
    <div class="page-bread">
      <el-breadcrumb separator="/">
        <el-breadcrumb-item :to="{ path: '/users/role' }">角色管理</el-breadcrumb-item>
        <el-breadcrumb-item>分配权限</el-breadcrumb-item>
      </el-breadcrumb>
    </div>
    <div class="form-content">

      <el-form :model="ruleForm" ref="ruleForm" label-width="120px" class="form-wraper">
        <el-form-item>
          <div class="role-name">{{ ruleForm.role_name }}</div>
        </el-form-item>

        <el-form-item>
          <el-card class="box-card">
            <el-tree
              :data="selectData"
              show-checkbox
              node-key="id"
              ref="tree"
              :check-strictly="true"
              :default-checked-keys="stateName"
              highlight-current
              :props="defaultProps"
              @check-change="handleCheckChange"></el-tree>
          </el-card>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="submitForm('ruleForm')">保存</el-button>
          <el-button @click="cancel()">取消</el-button>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
  import {getPermissions, changePermission} from '@/api/role';
  import {getTree} from '@/api/permission';

  export default {
    data() {
      return {
        stateName: [],
        ruleForm: {
          id: '',
          role_name: '',
          permissions: []
        },
        unchecked: [],
        mineStatus: [],
        mineStatusValue: [],
        selectData: [],
        defaultProps: {
          children: "children",
          label: "permission_name"
        }
      }
    },
    created() {
      this.loadData(this.$route.params.roleid)
    },
    methods: {
      handleCheckChange(data, checked, node) {
        if (data.children && data.children.length > 0) {
          for (let i=0;i<data.children.length;i++) {
            this.$refs.tree.setChecked(data.children[i].id, checked)
          }
        }
      },
      getPermissions() {
        getTree().then(res => {
          this.selectData = res.data;
        })
      },
      loadData(id) {
        getPermissions({id: id}).then(res => {
          Object.assign(this.ruleForm, res.data);
          this.stateName = res.data.permissions;
          this.getPermissions();
        })
      },
      submitForm(formName) {
        let res = this.$refs.tree.getCheckedNodes();
        let arr = [];
        res.forEach(item => {
          arr.push(item.id);
        });
        this.ruleForm.permissions = arr;
        changePermission(this.ruleForm).then(response => {
          if (response) {
            this.$notify({
              title: '成功',
              message: '修改成功',
              type: 'success',
              duration: 2000
            })
            this.$router.push({path: '/users/role'})
          }
        })
      },
      cancel() {
        this.$router.push({path: '/users/role'})
      }
    }
  }
</script>
<style>
  .el-select-dropdown__list {
    padding: 0 !important
  }

  ;
  .el-select-dropdown__item {
    padding: 0 !important
  }

  .el-form-item__content {
    margin-left: 0 !important;
  }

  .form-container .form-content .form-wraper {
    width: 100% !important;
  }
</style>
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
        width: 1000px;

        .line {
          text-align: center;
        }
      }

      .role-name {
        font-size: 24px;
      }

      .el-tag {
        margin: 5px 10px;
      }
    }
  }
</style>
