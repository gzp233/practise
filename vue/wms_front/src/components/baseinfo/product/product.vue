<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" v-permission="'4B86E52A58C73EB692F993D7E0884E00'" @click="handleImport">导入</button>
    </div>
    <div class="grid-content">
      <el-table
        :data="tableData"
        v-loading.body="loading"
        stripe
        height="800"
        style="width: 100%"
      >
        <el-table-column
          prop="PRODCHINM"
          :render-header="serachHeader"
          column-key="PRODCHINM"
          label="产品中文名称"
          width="200"
        ></el-table-column>
        <el-table-column
          prop="NewPRODUCTCD"
          :render-header="serachHeader"
          column-key="NewPRODUCTCD"
          label="新产品代码"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="PRODUCTCD"
          :render-header="serachHeader"
          column-key="PRODUCTCD"
          label="产品代码"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="prodflg.PRODFLGNM"
          label="产品区分"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="brand.BRANDNM"
          label="品牌"
          min-width="60"
        ></el-table-column>
        <el-table-column
          prop="series.SERIESNM"
          label="系列"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="validity"
          label="有效期"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="barCode"
          label="支码"
          min-width="100"
        ></el-table-column>
        <el-table-column
          prop="is_need_code"
          label="防串货"
          min-width="100"
        ></el-table-column>
        <el-table-column
          fixed="right"
          label="操作"
          width="200"
          class-name="action-column"
        >
          <template slot-scope="scope">
            <div class="action-column">
              <el-button
                type="text"
                size="small"
                @click.native.prevent="edit(scope.row)"
                >编辑</el-button
              >
              <el-button
                type="text"
                size="small"
                @click.native.prevent="showDetail(scope.row)"
                >单位</el-button
              >
              <el-button
                type="text"
                size="small"
                @click.native.prevent="toggleCode(scope.row)"
                >防串货切换</el-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="grid-page">
      <el-pagination
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
        :current-page.sync="query.page"
        :page-sizes="[10, 20, 50, 100]"
        :page-size="query.limit"
        layout="total,->, prev, pager, next, jumper, sizes"
        :total="total"
      ></el-pagination>
    </div>

    <el-dialog
      title="库位"
      :visible.sync="dialogVisible"
      width="50%"
      :modal-append-to-body="false"
    >
      <el-table :data="units" stripe style="width: 100%">
        <el-table-column
          prop="unit_name"
          label="单位名称"
          min-width="150"
        ></el-table-column>
        <el-table-column
          prop="number"
          label="对应数量"
          min-width="150"
        ></el-table-column>
        <el-table-column label="操作" width="100" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button
                type="text"
                size="small"
                @click.native.prevent="changeUnit(scope.row)"
                >修改</el-button
              >
              <el-button
                type="text"
                size="small"
                @click.native.prevent="delUnit(scope.row)"
                >删除</el-button
              >
            </div>
          </template>
        </el-table-column>
      </el-table>

      <div class="form-content">
        <el-form
          :model="postForm"
          :rules="rules"
          ref="postForm"
          label-width="120px"
          class="form-wraper"
        >
          <el-form-item label="单位ID" v-if="this.postForm.id">
            <span>{{ this.postForm.id }}</span>
          </el-form-item>
          <el-form-item label="单位名" prop="unit_name">
            <el-input v-model.trim="postForm.unit_name"></el-input>
          </el-form-item>
          <el-form-item label="单位数量" prop="number">
            <el-input v-model.number="postForm.number"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="submitForm('postForm')">{{
              this.postForm.id ? "编辑" : "添加"
            }}</el-button>
            <el-button @click="resetForm">重置</el-button>
          </el-form-item>
        </el-form>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="dialogVisible = false"
          >确 定</el-button
        >
      </span>
    </el-dialog>
    <el-dialog
      title="编辑"
      :visible.sync="dialogEditVisible"
      width="50%"
      :modal-append-to-body="false"
    >
      <div class="form-content">
        <el-form
          :model="postV"
          :rules="ruleV"
          ref="postV"
          label-width="120px"
          class="form-wraper"
        >
          <el-form-item label="商品名" v-if="postV.id">
            <span>{{ postV.product_name }}</span>
          </el-form-item>
          <el-form-item label="有效期" prop="validity">
            <el-input v-model.number="postV.validity"></el-input>
          </el-form-item>
          <el-form-item label="支码" prop="barCode">
            <el-input v-model.trim="postV.barCode"></el-input>
          </el-form-item>
        </el-form>
      </div>

      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="submitFormV">确 定</el-button>
        <el-button type="primary" @click="dialogEditVisible = false"
          >取 消</el-button
        >
      </span>
    </el-dialog>

    <!-- 导入 -->
    <el-dialog
      title="导入"
      :visible.sync="dialogImportVisible"
      width="50%"
      :modal-append-to-body="false"
    >
      <div class="tpl-div">
        <el-button type="primary" @click="downloadTpl">下载模板</el-button>
      </div>
      <div class="form-content">
        <el-upload
          class="upload-demo"
          ref="upload"
          :multiple="false"
          limit="1"
          action=""
          accept=".xls,.xlsx"
          :file-list="fileList"
          :before-upload="beforeUpload"
          :auto-upload="false"
        >
          <el-button slot="trigger" size="small" type="primary"
            >选取文件</el-button
          >
          <el-button style="margin-left: 10px;" size="small" type="success" @click="submitUpload">上传</el-button>
          <div slot="tip" class="el-upload__tip">
            只能上传xlsx/xls文件
          </div>
        </el-upload>

        <div v-if="errorVisible">
            {{ message }}
          </div>
        <el-table
        :data="errors"
        v-if="errorVisible"
        stripe
        style="width: 100%"
      >
        <el-table-column
          prop="line"
          label="行数"
          width="100"
        ></el-table-column>
        <el-table-column
          prop="message"
          label="报错信息"
          min-width="300"
        ></el-table-column>
        </el-table>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="doImport">确 定</el-button>
        <el-button type="default" @click="dialogImportVisible = false"
          >取 消</el-button
        >
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { getIndex, toggleCode, editProduct, upload } from "@/api/product";
import { create, update, del } from "@/api/unit";
import { getToken } from "@/utils/auth";

export default {
  name: "location",
  data: function() {
    var checkNumber = (rule, value, callback) => {
      if (!value) {
        return callback(new Error("数量不能为空"));
      }
      setTimeout(() => {
        if (!Number.isInteger(value)) {
          callback(new Error("请输入数字值"));
        } else {
          if (value < 1) {
            callback(new Error("不能小于0"));
          } else {
            callback();
          }
        }
      }, 1000);
    };
    var checkValidity = (rule, value, callback) => {
      setTimeout(() => {
        if (!Number.isInteger(value)) {
          callback(new Error("请输入数字值"));
        } else {
          if (value >= 1000 || value <= 0) {
            callback(new Error("最多为3位数字"));
          } else {
            callback();
          }
        }
      }, 1000);
    };
    return {
      query: {
        page: 1,
        limit: 20
      },
      fileList: [],
      errors:[],
      total: 0,
      loading: true,
      tableData: [],
      units: [],
      dialogVisible: false,
      dialogEditVisible: false,
      dialogImportVisible: false,
      errorVisible: false,
      message: '',
      postForm: {
        id: "",
        product_id: "",
        unit_name: "",
        number: ""
      },
      postV: {
        id: "",
        product_name: "",
        validity: "",
        barCode: ""
      },
      rules: {
        unit_name: [
          { required: true, message: "请输入单位名称", trigger: "blur" },
          { min: 1, max: 10, message: "长度在 1 到 10 个字符", trigger: "blur" }
        ],
        number: [{ validator: checkNumber, trigger: "blur" }]
      },
      ruleV: {
        validity: [{ validator: checkValidity, trigger: "blur" }]
      }
    };
  },
  mounted: function() {
    this.loadData();
  },

  methods: {
    beforeUpload(file) {
      let param = new FormData();
      param.append("file", file);
      var that = this;
      upload(param).then( response => {
        if (response.data) {
          this.errors = response.data
          this.message = response.message
          this.errorVisible = true
        }
      })
    },
    submitUpload() {
      this.$refs.upload.submit();
    },
    serachHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-input
            v-model={this.query[column.columnKey]}
            placeholder={column.label}
            nativeOn-keyup={arg => arg.keyCode === 13 && this.loadData()}
            prefix-icon="el-icon-search"
          />
        </div>
      );
    },
    loadData: function() {
      this.loading = true;
      getIndex(this.query).then(response => {
        this.total = response.data.total;
        this.tableData = response.data.data;
      });

      setTimeout(() => {
        this.loading = false;
      }, 2000);
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    showDetail(row) {
      this.units = row.units;
      console.log(this.units);
      this.postForm.product_id = row.id;
      this.dialogVisible = true;
    },
    edit(row) {
      this.postV.validity = row.validity;
      this.postV.barCode = row.barCode;
      this.postV.id = row.id;
      this.postV.product_name = row.PRODCHINM;
      this.dialogEditVisible = true;
    },
    toggleCode(row) {
      toggleCode({ id: row.id }).then(res => {
        if (res) {
          this.$notify({
            title: "成功",
            message: "切换成功",
            type: "success",
            duration: 2000
          });
          this.loadData();
        }
      });
    },
    changeUnit(row) {
      Object.assign(this.postForm, row);
    },
    delUnit(row) {
      del({ id: row.id }).then(res => {
        if (res) {
          this.$notify({
            title: "成功",
            message: "删除成功",
            type: "success",
            duration: 2000
          });
          this.loadData();
          this.resetForm();
          this.dialogVisible = false;
        }
      });
      this.dialogVisible = true;
    },
    resetForm() {
      this.postForm.id = "";
      this.postForm.unit_name = "";
      this.postForm.number = "";
    },
    resetFormV() {
      this.postV.id = "";
      this.postV.product_name = "";
      this.postV.validity = "";
      this.postV.barCode = "";
    },
    submitV(formName) {
      console.log(formName);
    },
    submitFormV() {
      this.$refs["postV"].validate(valid => {
        if (valid) {
          editProduct(this.postV).then(response => {
            if (response) {
              this.$notify({
                title: "成功",
                message: "修改成功",
                type: "success",
                duration: 2000
              });
              this.loadData();
              this.resetFormV();
              this.dialogEditVisible = false;
            }
          });
        }
      });
    },
    submitForm(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          if (this.postForm.id && this.postForm.id > 0) {
            update(this.postForm).then(response => {
              if (response) {
                this.$notify({
                  title: "成功",
                  message: "修改成功",
                  type: "success",
                  duration: 2000
                });
                this.loadData();
                this.resetForm();
                this.dialogVisible = false;
              }
            });
          } else {
            create(this.postForm).then(response => {
              if (response) {
                this.$notify({
                  title: "成功",
                  message: "创建成功",
                  type: "success",
                  duration: 2000
                });
                this.loadData();
                this.resetForm();
                this.dialogVisible = false;
              }
            });
          }
        }
      });
    },
    downloadTpl() {
      let token = getToken();
      token = token.split(" ", 2);
      window.open(
        "/api/template/download?key=product&token=" + token[1],
        "_blank"
      );
    },
    doImport() {
      this.dialogImportVisible = false;
    },
    handleImport() {
      this.errorVisible = false
      this.dialogImportVisible = true;
    }
  }
};
</script>
<style lang="less">
.grid-container {
  height: auto;
  overflow: hidden;
  .action-column {
    text-align: right;
  }
  .color-gred {
    color: #999;
  }
}
.el-dialog--dl-small {
  width: 600px;
}
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
</style>
