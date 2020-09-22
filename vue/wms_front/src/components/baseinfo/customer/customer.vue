<template>
	<div class="grid-container">
		<div class="search-form">
			<el-form :inline="true" :model="query">
				<el-form-item label="店招名">
					<el-input v-model="query.ShopSignNM" placeholder="店招名"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="doSeach">查询</el-button>
					<el-button @click="doClear">清空</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" stripe height="800" style="width: 100%">
				<el-table-column prop="CompanyCd" label="公司代码" min-width="80">
				</el-table-column>
				<el-table-column prop="CUSTOMERCD" label="客户代码" min-width="100">
				</el-table-column>
				<el-table-column prop="ShopSignNM" label="店招名" min-width="150">
				</el-table-column>
				<el-table-column prop="DeliverAddCD" label="送货地代码" min-width="100">
				</el-table-column>
				<el-table-column prop="ShopAdr" label="店铺地址" min-width="250">
				</el-table-column>
				<el-table-column prop="ShopPostCD" label="店铺邮编" min-width="100">
				</el-table-column>
				<el-table-column prop="ShopTel" label="店铺电话" min-width="100">
				</el-table-column>
				<el-table-column prop="ShopFax" label="店铺传真" min-width="100">
				</el-table-column>
				<el-table-column prop="DELFLG" label="状态" min-width="80">
				</el-table-column>
				<el-table-column prop="DataYmd" label="更新时间" min-width="100">
				</el-table-column>
				<el-table-column label="创建日期" min-width="150">
					<template slot-scope="scope">
						<span class="color-gred">{{scope.row.created_at}}</span>
					</template>
				</el-table-column>
			</el-table>
		</div>
		<div class="grid-page">
			<el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" :current-page.sync="query.page" :page-sizes="[10, 20, 50, 100]" :page-size="query.limit" layout="total,->, prev, pager, next, jumper, sizes" :total="total">
			</el-pagination>
		</div>

	</div>
</template>

<script>

	import { getIndex, del }from '@/api/customer'
	export default {
		name: 'location',
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
				detail: {}
			}
		},
		mounted: function() {
			this.loadData()
		},


		methods: {
			loadData: function() {
				this.loading = true
				getIndex(this.query).then(response => {
					this.total = response.data.total
				this.tableData = response.data.data
			})

			setTimeout(() => {
				this.loading = false
			}, 2000)
			},


			doClear: function() {
				this.query.ShopSignNM = ''
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
		width: 50px;
	}
</style>
