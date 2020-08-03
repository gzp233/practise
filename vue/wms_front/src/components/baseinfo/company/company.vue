<template>
	<div class="grid-container">
		<div class="search-form">
			<el-form :inline="true" :model="query">
				<el-form-item label="公司名称">
					<el-input v-model="query.CompanyNm" placeholder="公司名称"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="doSeach">查询</el-button>
					<el-button @click="doClear">清空</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" stripe height="800" style="width: 100%">
				<el-table-column prop="CompanyCd" label="公司代码" win-width="100">
				</el-table-column>
				<el-table-column prop="CompanyNm" label="公司名称" win-width="300">
				</el-table-column>
				<el-table-column prop="CompanyShortNm" label="公司简称" win-width="100">
				</el-table-column>
				<el-table-column prop="FaxNum" label="传真" win-width="100">
				</el-table-column>
				<el-table-column prop="OfficeAdd" label="公司地址" win-width="250">
				</el-table-column>
				<el-table-column prop="POSTCD" label="邮编" win-width="100">
				</el-table-column>
				<el-table-column prop="TelephoneNum" label="电话" win-width="100">
				</el-table-column>
				<el-table-column prop="DataYmd" label="更新时间" win-width="150">
				</el-table-column>
				<el-table-column label="创建日期" win-width="200">
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

	import { getIndex, del }from '@/api/company'
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
				this.query.CompanyNm = ''
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
		width: 600px;
	}
</style>
