<template>
	<div class="grid-container">
		<div class="search-form">
			<!-- <el-form :inline="true" :model="query">
				<el-form-item label="品牌名称">
					<el-input v-model="query.BRANDNM" placeholder="品牌名称"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="doSeach">查询</el-button>
					<el-button @click="doClear">清空</el-button>
				</el-form-item>
			</el-form> -->
            <el-button type="primary" style="margin:0 0 20px 0" @click="doBack">返回</el-button>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%">
				<!-- <el-table-column prop="product_id" label="商品ID" win-width="100"></el-table-column> -->
				<!-- <el-table-column prop="user_id" label="修改人" win-width="100"></el-table-column> -->
				<el-table-column prop="stock_no" label="库位号" win-width="200" column-key="stock_no" :render-header="serachHeader"></el-table-column>
				<el-table-column prop="NewPRODUCTCD" label="新产品代码" win-width="200" column-key="NewPRODUCTCD" :render-header="serachHeader"></el-table-column>
				<el-table-column prop="PRODCHINM" label="中文名" win-width="200"></el-table-column>
				<el-table-column prop="available_time" label="有效期" win-width="200"></el-table-column>
				<el-table-column prop="frozen_number" label="冻结数量" win-width="200"></el-table-column>
				<el-table-column prop="number" label="库存数量" win-width="200"></el-table-column>
				<el-table-column prop="state_name" label="商品状态" win-width="200" column-key="state_name" :render-header="serachHeader"></el-table-column>
				<!-- <el-table-column prop="CHARG" label="批次号" win-width="150"></el-table-column>
                <el-table-column prop="number" label="修改数量" win-width="150"></el-table-column>
                <el-table-column prop="available_time" label="商品有效期" win-width="150"></el-table-column> -->
				<el-table-column label="操作" win-width="200">
					<template slot-scope="scope">
						<el-button type="text" @click="editor(scope.row)">编辑</el-button>
					</template>
				</el-table-column>
			</el-table>
		</div>
        <el-dialog 
        title="编辑" 
        :visible.sync="dialogFormVisible"
        :modal-append-to-body="false"
        width="30%">
        <el-form :model="form" :rules="rules" ref="form" label-width="100px">
            <el-form-item label="修改后数量" prop="number" :label-width="formLabelWidth">
                <el-input v-model="form.number" autocomplete="off" placeholder="请输入数量"></el-input>
            </el-form-item>
            <el-form-item label="描述" prop="comment" :label-width="formLabelWidth">
                <el-input
                type="textarea"
                :autosize="{ minRows: 2, maxRows: 4}"
                placeholder="请输入内容"
                v-model="form.comment">
                </el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm('form')">提交</el-button>
                <el-button @click="resetForm('form')">重置</el-button>
            </el-form-item>
        </el-form>
        </el-dialog>
		<div class="grid-page">
			<el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="query.page" :page-sizes="[10, 20, 50, 100]" :page-size="query.limit" layout="total,->, prev, pager, next, jumper, sizes" :total="total">
			</el-pagination>
		</div>
		<el-dialog title="品牌详情" :visible.sync="dialogVisible" size="dl-small" :modal-append-to-body="false" :close-on-press-escape="false">
			<customer-detail :detail="detail"></customer-detail>
		</el-dialog>

	</div>
</template>

<script>
	import { goodsList, modify }from '@/api/management'
	export default {
		name: 'management',
		data: function() {
			return {
				query: {
					page: 1,
					limit: 20,
				},
				total: 0,
				loading: true,
				tableData: [],
				dialogVisible: false,
				dialogFormVisible: false,
                detail: {},
                id: "",
                form: {
                    number: '',
                    comment: ''
                },
                formLabelWidth: '120px',
                rules: {
                    number: [
                        { required: true, message: '请输入数量', trigger: 'blur' },
                    ],
                    comment: [
                        { required: true, message: '请填写描述', trigger: 'blur' },
                        //{ min: 5, max: 100, message: '长度在 5 到 100 个字符', trigger: 'blur' }
                    ],
                }
			}
		},
		mounted: function() {
			this.loadData()
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
             submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.form.id = this.id;
                        modify(this.form).then(res =>{
                           if(Object.is(res.code,200)){
                             this.dialogFormVisible = false;
                             this.$router.push({
                                 path: "/management/goodslist"
                             });
                             this.loadData();
                           };
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },            
			loadData: function() {
				this.loading = true;
				goodsList(this.query).then(response => {
					this.total = response.data.total
				    this.tableData = response.data.data
                });
                setTimeout(() => {
                    this.loading = false
                }, 2000)
            },
            editor(row){
              this.dialogFormVisible = true;
              this.id = row.id;
            },
			doClear: function() {
				this.query.BRANDNM = ''
			},
			doSeach: function() {
				this.query.page = 1
				this.loadData()
			},
			handleSizeChange: function(val) {
				this.query.limit = val
				this.loadData()
			},
			handleCurrentChange: function(val) {
				this.query.page = val
				this.loadData()
			},
			showDetail: function(detail) {
				this.detail = detail
				this.dialogVisible = true
            },
            doBack(){
                 this.$router.push({
                    path: "/management/goodslist"
                 });
            }
		}
	}
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
</style>