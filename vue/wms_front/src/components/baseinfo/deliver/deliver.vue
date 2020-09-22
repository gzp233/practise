<template>
	<div class="grid-container">
		<div class="search-form">
			<el-form :inline="true" :model="query">
				<el-form-item label="送货地址">
					<el-input v-model="query.DeliverAdd" placeholder="送货地址"></el-input>
				</el-form-item>
				<el-form-item>
					<el-button type="primary" @click="doSeach">查询</el-button>
					<el-button @click="doClear">清空</el-button>
				</el-form-item>
			</el-form>
		</div>
		<div class="grid-content">
			<el-table :data="tableData" v-loading.body="loading" height="800" stripe style="width: 100%">
				<el-table-column prop="CompanyCd" label="公司代码" min-width="100">
				</el-table-column>
				<el-table-column prop="DeliverAdd" label="送货地址" min-width="250">
				</el-table-column>
				<el-table-column prop="DeliverAddCD" label="送货地代码" min-width="150">
				</el-table-column>
				<el-table-column prop="DeliverAddNM" label="送货地名称" min-width="250">
				</el-table-column>
				<el-table-column prop="FAXNO" label="送货地传真号码" min-width="150">
				</el-table-column>
				<el-table-column prop="POSTCD" label="送货地邮编" min-width="150">
				</el-table-column>
				<el-table-column prop="TELNO" label="送货地电话号码" min-width="150">
				</el-table-column>
				<el-table-column prop="DELFLG" label="状态" min-width="100">
				</el-table-column>
				<el-table-column prop="DataYmd" label="更新时间" min-width="150">
				</el-table-column>
				<el-table-column label="创建日期" min-width="200">
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

	import { getIndex, del }from '@/api/deliver'
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
				this.query.DeliverAdd = ''
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
