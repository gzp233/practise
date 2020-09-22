<template>
	<div class="grid-container">
		<div class="search-form">			
            <el-button v-permissions="['41DB8024CE8DD292521222DBEB97D704']" type="primary" style="margin:0 0 20px 0" @click="doCreate">修改库存</el-button>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" stripe style="width: 100%">
				<el-table-column prop="user.username" label="修改人" win-width="100"></el-table-column>
				<el-table-column prop="origin_number" label="修改前数量" win-width="100"></el-table-column>
				<el-table-column prop="number" label="修改数量" win-width="100"></el-table-column>
				<el-table-column prop="stock_no" label="库位号" column-key="stock_no" :render-header="serachHeader" win-width="200" ></el-table-column>
				<el-table-column prop="NewPRODUCTCD" label="新产品代码" column-key="NewPRODUCTCD" :render-header="serachHeader" win-width="200"></el-table-column>
				<el-table-column prop="product.PRODUCTCD" label="产品代码" win-width="200"></el-table-column>
				<el-table-column prop="product.PRODCHINM" label="中文名" win-width="200"></el-table-column>
				<el-table-column prop="state_name" label="商品状态" column-key="state_name" :render-header="serachHeader" win-width="80"></el-table-column>
				<el-table-column prop="available_time" label="商品有效期" win-width="150"></el-table-column>
                <el-table-column prop="comment" label="描述" win-width="150"></el-table-column>
                <el-table-column prop="available_time" label="商品有效期" win-width="150"></el-table-column>				
                <el-table-column 
				prop="created_at" 
				label="修改时间" 
				column-key="created_at" 				
				:render-header="serachHeader2" 
				win-width="350"></el-table-column>				
			</el-table>
		</div>        
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
	import { modifyList }from '@/api/management'
	import request from '@/utils/request'
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
				detail: {}, 
				value: [],
				key: "" , 
				array: [],         
			}
		},
		mounted: function() {
			this.loadData();
		},
		methods: { 			
			serachHeader(h, { column, $index }, index) {
				return (
					<div class="header-custom-stype">
					<el-input
						v-model={this.query[column.columnKey]}
						placeholder={column.label}
						nativeOn-keyup={arg => arg.keyCode === 13 && this.search(column.columnKey,this.query[column.columnKey])}
						prefix-icon="el-icon-search"
					/>
					</div>
				);
			},
			handleTime(val){
				let key = this.key;
				this.search(key,val,"2")
				localStorage.setItem("val",val.toString().split(" - ")[0])
				localStorage.setItem("val2",val.toString().split(" - ")[1])
			},
			serachHeader2(h, { column, $index }, index) {	
				this.key = column.columnKey;			
				return (
					<div class="header-custom-stype">
					 <el-date-picker
						v-model={this.query[column.columnKey]}
						type="datetimerange"
						format="yyyy-MM-dd HH:mm:ss"
						value-format="yyyy-MM-dd HH:mm:ss"
						onChange={this.handleTime}
						nativeOn-keyup={arg => arg.keyCode === 13}
					    placeholder="选择日期">
					 </el-date-picker>					
					</div>
				);
			},    
			search(key,val,tag){
				this.loadData(key,val,tag)
			},                
			loadData: function(key,val,tag) {
				this.loading = true;
				console.log(key,val,tag)
				this.array = [];
				if(tag === "2"){       
				  this.array.push(val.split(" - ")[0],val.split(" - ")[1]);
				  request({
					methods: 'get',
					url: '/goods/modifyList',
					params: {
						page: this.query.page,
						limit: this.query.limit,
						[key]: this.array
					}
				  }).then(response => {
					this.total = response.data.total;
					this.tableData = response.data.data;
					for(let i=0;i<this.tableData.length;i++){
						this.$set(this.tableData[i],"NewPRODUCTCD",this.tableData[i].product.NewPRODUCTCD);
					};
				  });  
				}else{
					request({
						methods: 'get',
						url: '/goods/modifyList',
						params: {
							page: this.query.page,
							limit: this.query.limit,
							[key]: val
						}
					}).then(response => {
						this.total = response.data.total;
						this.tableData = response.data.data;
						for(let i=0;i<this.tableData.length;i++){
							this.$set(this.tableData[i],"NewPRODUCTCD",this.tableData[i].product.NewPRODUCTCD);
						};
					}); 
				};						
				// modifyList(this.query).then(response => {
				// 	this.total = response.data.total
				//     this.tableData = response.data.data
				// });						
                setTimeout(() => {
                    this.loading = false
                }, 2000)
            },
            doCreate(){
               this.$router.push({
                   path: "/management/management"
               })          
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