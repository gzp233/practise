<template>
  <div class="grid-container">
    <div class="grid-toolbar">
      <button class="tool-btn btn-add" @click="exportOut">导出</button>
    </div>
    <div class="grid-content">
      <el-table
        :data="tableData"
        v-loading.body="loading"
        border
        @selection-change="handleSelectionChange"
        stripe
        height="800"
        style="width: 100%"
      >
        <el-table-column type="selection" width="55"></el-table-column>
        <el-table-column
          prop="SHIPMENTID"
          :render-header="serachHeader"
          column-key="SHIPMENTID"
          label="出库编号"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="CUSTOMER"
          :render-header="serachHeader"
          column-key="CUSTOMER"
          label="客户代码"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="PRODUCTCODE"
          :render-header="serachHeader"
          column-key="PRODUCTCODE"
          label="新产品代码"
          width="150"
        ></el-table-column>
        <el-table-column
          prop="QRCODE"
          :render-header="serachHeader"
          column-key="QRCODE"
          label="防串货码"
          width="200"
        ></el-table-column>
        <el-table-column
          prop="UNIT"
          :render-header="serachHeader"
          column-key="UNIT"
          label="单位"
          width="100"
        ></el-table-column>
        <el-table-column
          prop="res"
          :render-header="serachStatusHeader"
          column-key="res"
          label="状态"
          width="120"
        ></el-table-column>
        <el-table-column
          prop="deal_user.username"
          :render-header="serachHeader"
          column-key="username"
          label="处理人"
          width="100"
        ></el-table-column>
        <el-table-column
          :render-header="serachTimeHeader"
          column-key="created_at"
          prop="created_at"
          label="创建时间"
          width="200"
        ></el-table-column>
        <el-table-column
          :render-header="serachTimeHeader"
          column-key="starttime"
          prop="starttime"
          label="开始时间"
          width="200"
        ></el-table-column>
        <el-table-column
          :render-header="serachTimeHeader"
          column-key="endtime"
          prop="endtime"
          label="结束时间"
          width="200"
        ></el-table-column>
        <el-table-column label="操作" width="150" class-name="action-column">
          <template slot-scope="scope">
            <div class="action-column">
              <el-button
                type="text"
                size="small"
                v-if="scope.row.error && scope.row.status==1"
                @click.native.prevent="reSend(scope.row)"
              >重新发送</el-button>
              <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">详情</el-button>
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
      :title="isEdit ? '重新发送' : '详情'"
      :visible.sync="dialogVisible"
      width="50%"
      :modal-append-to-body="false"
    >
      <div class="customer-detail">
        <div class="detail-item">
          <span class="item-title">授权码:</span>
          <div class="item-content">{{tempRow.AUTHCODE}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">出库编号:</span>
          <div class="item-content">{{tempRow.SHIPMENTID}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">客户代码:</span>
          <div class="item-content">{{tempRow.CUSTOMER}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">客户名称:</span>
          <div class="item-content">{{tempRow.CUSTOMERNAME}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">产品名称:</span>
          <div class="item-content">{{tempRow.PRODUCTNAME}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">新产品代码:</span>
          <div class="item-content">{{tempRow.PRODUCTCODE}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">防串货码:</span>
          <div class="item-content">{{tempRow.QRCODE}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">单位:</span>
          <div class="item-content">{{tempRow.UNIT}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">数量:</span>
          <div class="item-content">{{tempRow.NUM}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">发出方:</span>
          <div class="item-content">{{tempRow.FROM}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">操作员:</span>
          <div class="item-content">{{tempRow.EMPLOYEE}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">箱码:</span>
          <div class="item-content">{{tempRow.box_code}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">型号码:</span>
          <div class="item-content">{{tempRow.type_code}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">是否发送:</span>
          <div class="item-content">{{tempRow.status == 0 ? '未发送' : '已发送'}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">状态:</span>
          <div class="item-content">{{tempRow.status == 0 ? '无' : (tempRow.error ? '失败' : '成功')}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">报错信息:</span>
          <div class="item-content">{{tempRow.error}}</div>
        </div>
        <div class="detail-item">
          <span class="item-title">配送时间:</span>
          <div class="item-content">{{tempRow.SHIPTIME}}</div>
        </div>
      </div>
      <div class="code-div" v-if="isEdit">
        <el-input v-model.trim="tmpData.tmpInput" placeholder="请输入代码" @keyup.enter.native="handleAdd"></el-input>
        <span style="margin-left:10px;">
          箱码:
          <span>{{ form.box_code }}</span>
        </span>
        <span style="margin-left:10px;">
          型号码:
          <span>{{ form.type_code }}</span>
        </span>
      </div>
      <el-table :data="codes" stripe style="width: 100%" v-if="isEdit">
        <el-table-column prop="box_code" label="箱号" min-width="50"></el-table-column>
        <el-table-column prop="type_code" label="型号码" min-width="100"></el-table-column>
        <el-table-column prop="code" label="防串货代码" min-width="100"></el-table-column>
        <el-table-column label="操作" min-width="100">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="delCode(scope.row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" v-if="!isEdit" @click="dialogVisible = false">确 定</el-button>
        <el-button type="primary" v-else :disabled="buttonDisabled" @click="doSend">发 送</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, sendCode } from "@/api/anticode";
import { getToken } from "@/utils/auth";

export default {
  name: "rolelist",
  data: function() {
    return {
      query: {
        page: 1,
        limit: 20
      },
      loading: true,
      tableData: [],
      total: 0,
      isEdit: false,
      tempRow: {},
      checked: [],
      form: {
        box_code: "",
        type_code: ""
      },
      tmpData: {tmpInput: ""},
      codes: [],
      buttonDisabled: false,
      dialogVisible: false,
      stus: ["成功", "失败", "未发送"]
    };
  },
  mounted: function() {
    this.loadData();
  },
  created() {
    if (localStorage.anticode_query) {
      this.query = JSON.parse(localStorage.anticode_query);
      delete localStorage.anticode_query;
    }
    this.handleCurrentChange(this.query.page);
  },
  methods: {
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
    serachTimeHeader(h, { column, $index }, index) {
      return (
        <div class="header-custom-stype">
          <el-date-picker
            v-model={this.query[column.columnKey]}
            type="datetimerange"
            range-separator=" 至 "
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            on-Change={this.loadData}
          />
        </div>
      );
    },
    serachStatusHeader(h, { column, $index }, index) {
      return (<div class="header-custom-stype">
      <el-select
            v-model={this.query[column.columnKey]}
            on-Change={this.loadData}
            placeholder={column.label}
          >
            <el-option  value="所有" label="所有" />
            <el-option  value="成功" label="成功" />
            <el-option value="失败" label="失败" />
            <el-option  value="未发送" label="未发送" />
          </el-select>
        </div>);
    },
    loadData: function() {
      if (localStorage.anticode_query) {
        this.query = JSON.parse(localStorage.anticode_query);
        delete localStorage.anticode_query;
      }
      fetchList(this.query).then(res => {
        this.tableData = res.data.data;
        this.total = res.data.total;
        this.buttonDisabled = false;
      });
      this.loading = true;
      setTimeout(() => {
        this.loading = false;
      }, 100);
    },
    handleSizeChange: function(val) {
      this.query.limit = val;
      this.loadData();
    },
    handleCurrentChange: function(val) {
      this.query.page = val;
      this.loadData();
    },
    showDetail: function(detail) {
      this.tempRow = detail;
      this.isEdit = false;
      this.dialogVisible = true;
    },
    handleSelectionChange(val) {
      this.checked = val;
    },
    reSend: function(detail) {
      this.tempRow = detail;
      this.isEdit = true;
      this.codes = [];
      this.dialogVisible = true;
    },
    handleAdd() {
      let tit = this.tmpData.tmpInput
      setTimeout(() => {
        this.tmpData.tmpInput = ''
    }, 50)
      if (!this.form.box_code) {
        if (tit.length > 5) {
          alert("箱码不超过5位！");
          return;
        } else {
          this.form.box_code = tit;
        }
      } else if (!this.form.type_code) {
        if (tit.length != 11 && tit.length != 23) {
          alert("型号码11位或23位！");
          return;
        } else {
          this.form.type_code = tit;
        }
      } else {
        if (tit.length != 16) {
          alert("16位防串货码错误！");
          return;
        } else {
          for (let i = 0; i < this.codes.length; i++) {
            if (tit === this.codes[i].code) {
              alert("防串货码不能重复！");
              return;
            }
          }
          this.codes.push({
            type_code: this.form.type_code,
            box_code: this.form.box_code,
            code: tit
          });
          this.form.type_code = this.form.box_code = tit = "";
        }
      }
    },
    delCode(row) {
      this.codes.splice(this.codes.indexOf(row, 1));
    },
    doSend() {
      this.buttonDisabled = true;
      if (this.codes.length === 0) {
        this.$message({
          message: "防串货码不能为空！",
          type: "error"
        });
        this.buttonDisabled = false;
        return;
      }
      sendCode({ id: this.tempRow.id, codes: this.codes })
        .then(res => {
          this.$message({
            message: "修改成功，正在发送...",
            type: "success"
          });
          localStorage.anticode_query = JSON.stringify(this.query);
          this.loadData();
          this.dialogVisible = false;
        })
        .catch(err => {
          this.buttonDisabled = false;
        });
    },
    exportOut() {
      let token = getToken();
      token = token.split(" ", 2);
      var ids = [];
      for (let i = 0; i < this.checked.length; i++) {
        ids.push(this.checked[i].id);
      }
      window.open(
        "/api/anticode/export?id=" + ids + "&query=" + JSON.stringify(this.query) + "&token=" + token[1],
        "_blank"
      );
    }
  }
};
</script>
<style lang="less" scoped>
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
.customer-detail {
  height: auto;
  overflow: hidden;
  .detail-item {
    display: inline-block;
    height: auto;
    width: 49%;
    overflow: hidden;
    line-height: 25px;
    padding-bottom: 15px;
    .item-title {
      display: inline-block;
      width: 100px;
      font-size: 14px;
      float: left;
      color: #333;
      text-align: right;
    }
    .item-content {
      margin-left: 110px;
      color: #999;
    }
  }
}
.code-div {
  display: block;
  margin: 10px 0;
  .el-input {
    display: inline-block;
    width: 200px;
  }
}
</style>
