<template>
	<div class="grid-container">
		<div class="search-form">
			<el-form :inline="true" :model="query">
				<el-form-item label="供应商">
					<el-input v-model="query.VendorCode" placeholder="供应商"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="doSeach">查询</el-button>
					<el-button @click="doClear">清空</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" stripe height="800" style="width: 100%">
				<el-table-column prop="VendorCode" label="供应商" min-width="100">
				</el-table-column>
				<el-table-column prop="AccountGroup" label="账户组" min-width="80">
				</el-table-column>
				<el-table-column prop="Country" label="国家" min-width="80">
				</el-table-column>
				<el-table-column prop="Name1" label="名称1" min-width="150">
				</el-table-column>
				<el-table-column prop="Name2" label="名称2" min-width="100">
				</el-table-column>
				<el-table-column prop="City" label="城市" min-width="80">
				</el-table-column>
				<el-table-column prop="PostalCode" label="邮政编码" min-width="100">
				</el-table-column>
				<el-table-column prop="Region" label="地区" min-width="80">
				</el-table-column>
				<el-table-column prop="Street" label="街道" min-width="150">
				</el-table-column>
				<el-table-column prop="Telephone" label="电话" min-width="100">
				</el-table-column>
				<el-table-column prop="Fax" label="传真" min-width="100">
				</el-table-column>
				<el-table-column prop="CompanyCode" label="公司代码" min-width="100">
				</el-table-column>
				<el-table-column prop="PurchasingOrganization" label="采购组织" min-width="100">
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

	import { getIndex, del }from '@/api/vendor'
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
				this.query.VendorCode = ''
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
