<template>
    <div class="grid-container">
        <div class="grid-toolbar">
            <button class="tool-btn btn-add" @click="toShelf">返回</button>
        </div>
        <div class="grid-content">
            <el-table
                    :data="tableData"
                    v-loading.body="loading"
                    @selection-change="handleSelectionChange"
                    stripe
                    border
                    style="width: 100%"
            >
                <!--<el-table-column type="selection" width="55"></el-table-column>-->
                <el-table-column prop="code" label="集货单号" width="150"></el-table-column>
                <el-table-column prop="ShopSignNM" label="客户数" width="80"></el-table-column>
                <el-table-column prop="vbeln" label="订单数" width="80"></el-table-column>
                <el-table-column prop="res" label="库位数" width="80"></el-table-column>
                <el-table-column prop="NewProductCd" label="型号数" width="80"></el-table-column>
                <el-table-column prop="number" label="支数" width="120"></el-table-column>
                <el-table-column prop="status" label="状态" width="100"></el-table-column>
                <el-table-column prop="jh_start" label="集货开始时间" width="130"></el-table-column>
                <el-table-column prop="jh_end" label="集货结束时间" width="130"></el-table-column>
                <el-table-column prop="bz_start" label="播种开始时间" width="130"></el-table-column>
                <el-table-column prop="bz_end" label="播种结束时间" width="130"></el-table-column>
                <el-table-column label="操作" width="100" class-name="action-column">
                    <template slot-scope="scope">
                        <div class="action-column">
                            <el-button type="text" size="small" @click.native.prevent="showDetail(scope.row)">下载</el-button>
                            <el-button type="text" size="small" @click.native.prevent="del(scope.row)">删除</el-button>
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
                    :page-sizes="[10, 20, 50, 100, 500, 1000, 3000]"
                    :page-size="query.limit"
                    layout="total,->, prev, pager, next, jumper, sizes"
                    :total="total"
            ></el-pagination>
        </div>
    </div>
</template>

<script>
    import {goodsList,del} from "@/api/consolidation";
    import { getToken } from "@/utils/auth";

    export default {
        name: "rolelist",
        data() {
            return {
                query: {
                    page: 1,
                    limit: 20,
                    status: 2
                },
                loading: true,
                tableData: [],
                wareHouseList: [],
                total: 0,
                goodsList: [],
                checked: [],
                check: [],
                dialogVisible: false,
                q: {
                    limit: 10,
                    page: 1
                }
            };
        },
        created() {
            this.loadData(this.$route.params.id);
        },
        computed: {
            leaves() {
                return id => {
                    var total = 0;
                    for (let k = 0; k < this.tableData.length; k++) {
                        if (this.tableData[k].id == id) {
                            total = this.tableData[k].number;
                        }
                    }
                    return total;
                };
            },
            stockedList() {
                let offset = this.q.limit * (this.q.page - 1);
                return this.goodsList.slice(offset, offset + this.q.limit);
            }
        },
        methods: {
            serachHeader(h, { column, $index }, index) {
                return (
                        <div class="header-custom-stype">
                        <el-input
                v-model={this.query[column.columnKey]}
                placeholder={column.label}
                nativeOn-keyup={arg => arg.keyCode === 13 && this.loadData(this.$route.params.id)}
                prefix-icon="el-icon-search"
                        />
                        </div>
            );
            },
            inputKeyup(row) {
                let leaves = this.leaves(row.id);
                if (leaves < row.valnumber) {
                    this.$message({
                        message: "商品数量不能超过可分配数量",
                        type: "warning"
                    });
                    row.valnumber = leaves;
                }
            },
            loadData(id) {
                if (localStorage.goodsStock_query) {
                    this.query = JSON.parse(localStorage.goodsStock_query)
                    delete localStorage.goodsStock_query
                }
                this.query.id = id
                goodsList(this.query).then(res => {
                    this.tableData = res.data.data;
                this.total = res.data.total;
            });
                this.loading = true;
                setTimeout(() => {
                    this.loading = false;
            }, 1000);
            },
            handleQSizeChange: function(val) {
                this.q.limit = val;
            },
            handleQCurrentChange: function(val) {
                this.q.page = val;
            },
            handleSizeChange(val) {
                this.query.limit = val;
                this.loadData(this.$route.params.id);
            },
            handleCurrentChange(val) {
                this.query.page = val;
                this.loadData(this.$route.params.id);
            },
            del(row){
                console.log(row)
                del({id:row.code}).then(res => {
                    this.$message({
                    message: "删除成功",
                    type: "success"
                });
                this.loadData();
            });
            },
            showDetail(row) {
                let token = getToken();
                token = token.split(" ", 2);
                window.open(
                        "/api/consolidation/wave?id=" + row.code + "&token=" + token[1],
                        "_blank"
                );
            },
            handleSelectionChange(val) {
                this.checked = val;
            },
            toShelf() {
                this.$router.push({
                    path: "/outstorage_action/consolidation"
                });
            },

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
    .warehouse-select {
        margin-right: 20px;
        margin-bottom: 20px;
    }
    .stock-item {
        display: block;
        margin: 10px 0;
    .stock-item-title {
        display: inline-block;
        width: 100px;
    }
    .el-input {
        width: 200px;
    }
    }
    .el-pagination {
        margin: 10px 0;
    }
</style>