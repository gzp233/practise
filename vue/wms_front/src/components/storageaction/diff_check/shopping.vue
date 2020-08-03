<template>
    <div class="grid-container">
        <div class="grid-toolbar">
            <button class="tool-btn btn-add" @click="toShelf">返回</button>
            <button class="tool-btn btn-add" @click="batches">分批</button>
            <button class="tool-btn btn-add" @click="Shelf">详情页</button>
            <button class="tool-btn btn-add" @click="del">删除</button>
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
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column
                        prop="stock_no"
                        :render-header="serachHeader"
                        column-key="stock_no"
                        label="库位"
                        width="150"
                ></el-table-column>
                <el-table-column prop="number" label="异动次数" width="120"></el-table-column>
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
    import { shoppingList, getGoodsByProductId, getByNo, relieve ,separate ,shopping,del} from "@/api/diff_check";
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
                shoppingList: [],
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
                return this.shoppingList.slice(offset, offset + this.q.limit);
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
                shoppingList(this.query).then(res => {
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
            showDetail(row) {
                this.query.id = row.id;
                shoppingList(this.query).then(res => {
                    this.shoppingList = res.data;
                this.dialogVisible = true;
            });
            },
            handleSelectionChange(val) {
                this.checked = val;
            },
            toShelf() {
                this.$router.push({
                    path: "/storage_action/diff_check/detail/"+this.$route.params.id
                });
            },

            Shelf() {
                this.$router.push({
                    path: "/storage_action/diff_check/list/"+this.$route.params.id
                });
                this.loadData(this.$route.params.id);
            },
            car(){
                this.$router.push({
                    path: "/storage_action/diff_check/shopping/"
                });
                this.loadData(this.$route.params.id);
            },
            shopping(){
                var goodsIdsList = [];
                if (this.checked.length === 0) {
                    this.$message({
                        message: "请选择商品！",
                        type: "warning"
                    });
                    return;
                }
                for (let i = 0; i < this.checked.length; i++) {
                    goodsIdsList.push(this.checked[i].id);
                }
                shopping(goodsIdsList).then(res => {
                    goodsIdsList = res.data;
                localStorage.goodsStock_query = JSON.stringify(this.query)
                this.loadData(this.$route.params.id);
            });
                this.$router.push({
                    query: { goodsIdsList: goodsIdsList }
                });
            },
            batches() {
                var goodsIdsList = [];
//                if (this.checked.length === 0) {
//                    this.$message({
//                        message: "请选择商品！",
//                        type: "warning"
//                    });
//                    return;
//                }
                for (let i = 0; i < this.checked.length; i++) {
                    goodsIdsList.push(this.checked[i].id);
                }
                separate({id:this.$route.params.id}).then(res => {
                    goodsIdsList = res.data;
                localStorage.goodsStock_query = JSON.stringify(this.query)
                this.loadData(this.$route.params.id);
            });
                this.$router.push({
                    query: { goodsIdsList: goodsIdsList }
                });
            },
            del() {
                var goodsIdsList = [];
                if (this.checked.length === 0) {
                    this.$message({
                        message: "请选择商品！",
                        type: "warning"
                    });
                    return;
                }
                for (let i = 0; i < this.checked.length; i++) {
                    goodsIdsList.push(this.checked[i].id);
                }
                del(goodsIdsList).then(res => {
                    goodsIdsList = res.data;
                localStorage.goodsStock_query = JSON.stringify(this.query)
                this.loadData(this.$route.params.id);
            });
                this.$router.push({
                    query: { goodsIdsList: goodsIdsList }
                });
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