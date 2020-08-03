<template>
    <div class="grid-container">
        <div class="page-bread">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item
                        :to="{ path: '/storage_action/frost' }"
                >解冻{{ show?'详情':'处理' }}</el-breadcrumb-item>
                <el-breadcrumb-item>{{ show?'详情':'处理' }}</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="detail-content">
            <el-tabs activeName="detail">
                <el-tab-pane label="冻结单" name="detail">
                    <el-row>
                        <el-col :span="10">
                            <div class="detail-item">
                                <span>冻结单号</span>
                                <div>{{ order.code }}</div>
                            </div>
                        </el-col>

                        <el-col :span="10">
                            <div class="detail-item">
                                <span>冻结原因</span>
                                <div>{{ order.cause }}</div>
                            </div>
                        </el-col>
                        <el-col :span="10">
                            <div class="detail-item">
                                <span>创建时间</span>
                                <div>{{ order.created_at }}</div>
                            </div>
                        </el-col>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
        </div>

        <div class="detail-content">
            <el-tabs activeName="table">
                <el-tab-pane label="商品明细" name="table">
                    <div class="statistic-content">
                        <p>
                            统计：
                            <span>产品种类：{{ goodsList.length }}</span>
                        </p>
                    </div>
                    <el-table :data="showList" stripe style="width: 100%">
                        <el-table-column prop="product.PRODCHINM" label="产品中文名称" min-width="200"></el-table-column>
                        <el-table-column prop="product.NewPRODUCTCD" label="新产品代码" min-width="150"></el-table-column>
                        <el-table-column prop="product.PRODUCTCD" label="产品代码" min-width="100"></el-table-column>
                        <el-table-column prop="goods.stock_no" label="库位" min-width="150"></el-table-column>
                        <el-table-column prop="goods.available_time" label="效期" min-width="150"></el-table-column>
                        <el-table-column prop="number" label="冻结数量" min-width="150"></el-table-column>
                        <el-table-column prop="state" label="状态" min-width="100"></el-table-column>
                        <el-table-column prop="created_at" label="冻结时间" min-width="150"></el-table-column>
                        <el-table-column label="操作" width="150" class-name="action-column">
                            <template slot-scope="scope">
                                <div class="action-column">
                                    <el-button type="text" size="small" @click.native.prevent="unfreeze(scope.row)">解冻</el-button>
                                </div>
                            </template>
                        </el-table-column>
                    </el-table>
                    <el-pagination
                            @size-change="handleSizeChange"
                            @current-change="handleCurrentChange"
                            :current-page.sync="query.page"
                            :page-sizes="[10, 20, 50, 100]"
                            :page-size="query.limit"
                            layout="total,->, prev, pager, next, jumper, sizes"
                            :total="goodsList.length"
                    ></el-pagination>

                    <el-dialog
                            title="库位"
                            :visible.sync="dialogDataVisible"
                            width="50%"
                            :modal-append-to-body="false"
                    >
                        <el-table :data="stockedList" stripe style="width: 100%">
                            <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
                            <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
                            <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
                            <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
                            <el-table-column prop="number" label="入库数量" min-width="150"></el-table-column>

                        </el-table>
                        <el-pagination
                                @size-change="handleQSizeChange"
                                @current-change="handleQCurrentChange"
                                :current-page.sync="q.page"
                                :page-sizes="[10, 20, 50, 100]"
                                :page-size="q.limit"
                                layout="total,->, prev, pager, next, jumper, sizes"
                                :total="tmpGoods.length"
                        ></el-pagination>
            <span slot="footer" class="dialog-footer">
              <el-button @click="dialogDataVisible = false">取 消</el-button>
              <el-button type="primary" @click="dialogDataVisible = false">确 定</el-button>
            </span>
                    </el-dialog>
                </el-tab-pane>
            </el-tabs>
        </div>
        <instock
                :goodsData="{goodsList:propList, params:{no_stock_names:['加工区', '复核区', '移库区'],state_names:['C302', '加工完成']}}"
                v-if="!show"
                v-on:childByValue="childByValue"
        />
    </div>
</template>

<script>
    import { getById,unfreeze} from "@/api/goods";
    import instock from "@/components/share/instock";

    export default {
        components: {
            instock
        },
        data() {
            return {
                id: "",
                goodsList: [],
                show: true,
                detailLabel: "",
                order: {},
                dialogDataVisible: false,
                tmpGoods: [],
                query: {
                    limit: 10,
                    page: 1
                },
                q: {
                    limit: 10,
                    page: 1
                }
            };
        },
        created() {
            this.show = /detail/.test(this.$route.path);
            this.detailLabel = this.show ? "在库调整详情" : "在库调整处理";
            this.id = this.$route.params.id;
            this.loadOrder(this.id);
        },
        computed: {
            showList() {
                let offset = this.query.limit * (this.query.page - 1);
                return this.goodsList.slice(offset, offset + this.query.limit);
            },
            stockedList() {
                let offset = this.q.limit * (this.q.page - 1);
                if (this.tmpGoods)
                    return this.tmpGoods.slice(offset, offset + this.q.limit);
                return [];
            },
            propList() {
                let arr = [];
                for (let k = 0; k < this.goodsList.length; k++) {
                    if (this.goodsList[k].todoNumber != 0) {
                        arr.push(this.goodsList[k]);
                    }
                }
                return arr;
            }
        },
        methods: {
            handleSizeChange: function(val) {
                this.query.limit = val;
            },
            handleCurrentChange: function(val) {
                this.query.page = val;
            },
            handleQSizeChange: function(val) {
                this.q.limit = val;
            },
            handleQCurrentChange: function(val) {
                this.q.page = val;
            },
            loadOrder(id) {
                getById({ id: id })
                        .then(res => {
                    this.goodsList = res.data;
                    this.order = res.data[0];
            })
            .catch(error => {
                    this.$router.push({
                    path: "/instorage_action/adjust"
                });
            });
            },
            unfreeze(row){
                unfreeze({ id: row.id }).then(res => {
                    if (res) {
                        this.$notify({
                            title: "成功",
                            message: "解冻成功",
                            type: "success",
                            duration: 2000
                        });
                        this.loadOrder(this.$route.params.id);
                    }
                });
            },
            childByValue: function(childValue) {
                // childValue就是子组件传过来的值
                stockIn(childValue).then(res => {
                    this.$message({
                    message: "入库成功！",
                    type: "success"
                });
                this.$router.push({
                    path: "/instorage_action/adjust"
                });
            });
            }
        }
    };
</script>
<style lang="less">
    .grid-container {
        height: auto;
        overflow: hidden;
    .detail-content {
        position: relative;
        height: auto;
        overflow: hidden;
        padding: 22px 15px;
        background: #fff;
    .detail-item {
        height: auto;
        overflow: hidden;
        line-height: 30px;
        font-size: 14px;
        padding-left: 30px;
    > span {
          display: inline-block;
          width: 150px;
          float: left;
          color: #333;
      }
    > div {
          margin-left: 160px;
          color: #999;
      }
    }
    .btn-box {
        position: absolute;
        top: 25px;
        right: 15px;
        z-index: 10;
    }
    }
    .action-column {
        text-align: right;
    }
    .color-gred {
        color: #999;
    }
    }
    .el-pagination {
        margin: 10px 0;
    }
</style>