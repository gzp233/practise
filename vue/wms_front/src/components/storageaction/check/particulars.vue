<template>
    <div class="grid-container">
        <div class="page-bread">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/storage_action/check/list/'+order.check_no }">盘点</el-breadcrumb-item>
                <el-breadcrumb-item>盘点详情</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="detail-content">
            <el-tabs activeName="detail">
                <el-tab-pane label="盘点单" name="detail">
                    <el-row>
                        <el-col :span="10">
                            <div class="detail-item">
                                <span>单号</span>
                                <div>{{ order.batches }}</div>
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
            <div class="btn-box">
                <!--<el-button type="primary" @click="exportDiff">差异报告</el-button>-->
                <el-button type="primary" @click="exportOut">导出</el-button>
                <el-button
                        type="primary"
                        @click="handleCreate"
                >添加</el-button>
                <el-button
                        type="primary"
                        @click="rollback"
                >确定</el-button>
            </div>
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
                        <el-table-column prop="stock_no" label="库位" min-width="150"></el-table-column>
                        <el-table-column prop="available_time" label="效期" min-width="150"></el-table-column>
                        <el-table-column prop="number" label="盘点数量" min-width="150"></el-table-column>
                        <el-table-column prop="real_number" label="实盘数量" min-width="150"></el-table-column>
                        <el-table-column label="更多" width="100">
                        <template slot-scope="scope">
                        <div class="table-form-item">
                        <el-input
                        size="mini"
                        v-model="scope.row.valnumber"
                        ></el-input>
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
            <el-dialog
                    title="新建"
                    :visible.sync="dialogVisible"
                    width="50%"
                    :modal-append-to-body="false"
            >
                <div class="choose-ensure">
                    <el-input v-model.trim="codeInput" placeholder="产品代码" @keyup.enter.native="handleAdd"></el-input>
                    <el-input v-model.trim="newcodeInput" placeholder="新产品代码" @keyup.enter.native="handleAdd"></el-input>
                    <el-button type="primary" @click="getGoods">查 找</el-button>
                    <el-button type="primary" @click="addGoods">添 加</el-button>
                </div>

                <el-table
                        :data="showGoods"
                        stripe
                        style="width: 100%"
                        @selection-change="handleSelectionChange"
                >
                    <el-table-column type="selection" width="55"></el-table-column>
                    <el-table-column prop="PRODCHINM" label="商品名" min-width="150"></el-table-column>
                </el-table>
                <el-pagination
                        @size-change="handleQSizeChange"
                        @current-change="handleQCurrentChange"
                        :current-page.sync="q.page"
                        :page-sizes="[10, 20, 50, 100]"
                        :page-size="q.limit"
                        layout="total,->, prev, pager, next, jumper, sizes"
                        :total="goods.length"
                ></el-pagination>
                <div class="selected-title">
        <span style="margin-left:10px;">

              </span>
                </div>
                <el-table :data="postShowList" stripe style="width: 100%">
                    <el-table-column prop="PRODCHINM" label="商品名" min-width="90"></el-table-column>
                    <el-table-column label="库位" min-width="80">
                        <template slot-scope="scope">
                            <div class="table-form-item">
                                <el-select
                                        v-model="scope.row.act_stock_no"
                                        @change="handleStockChange($event, scope.row)"
                                        filterable
                                        remote
                                        reserve-keyword
                                        size="small"
                                        :remote-method="loadLocations"
                                        :loading="loading"
                                        placeholder="请选择库位"
                                >
                                    <el-option
                                            v-for="stock in locationList"
                                            :key="stock.id"
                                            :label="stock.stock_no"
                                            :value="stock.stock_no"
                                    ></el-option>
                                </el-select>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="状态"
                            min-width="70"
                    >
                        <template slot-scope="scope">
                            <div class="table-form-item">
                                <el-select v-model="scope.row.act_state_name" filterable placeholder="请选择状态">
                                    <el-option
                                            v-for="attribute,key in attributesList"
                                            :key="key"
                                            :label="attribute.name"
                                            :value="attribute.name"
                                    ></el-option>
                                </el-select>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="效期" min-width="110">
                        <template slot-scope="scope">
                            <div class="table-form-item">
                                <el-date-picker
                                        v-model="scope.row.act_available_time"
                                        @change="getSTime($event, scope.row)"
                                        type="month"
                                        :default-value="timeDefaultShow"
                                        format="yyyy-MM"
                                        placeholder="选择月"
                                ></el-date-picker>
                            </div>
                        </template>
                    </el-table-column>


                    <el-table-column label="数量" min-width="50">
                        <template slot-scope="scope">
                            <div class="table-form-item">
                                <el-input
                                        @keyup.native.prevent="inputKeyup(scope.row)"
                                        size="mini"
                                        v-model="scope.row.real_number"
                                ></el-input>
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column label="操作" min-width="40">
                        <template slot-scope="scope">
                            <div class="action-column">
                                <el-button type="text" size="small" @click.native.prevent="delRow(scope.row)">删除</el-button>
                            </div>
                        </template>
                    </el-table-column>
                </el-table>
                <el-pagination
                        @size-change="handleSSizeChange"
                        @current-change="handleSCurrentChange"
                        :current-page.sync="s.page"
                        :page-sizes="[10, 20, 50, 100]"
                        :page-size="s.limit"
                        layout="total,->, prev, pager, next, jumper, sizes"
                        :total="postGoods.length"
                ></el-pagination>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">取 消</el-button>
          <el-button type="primary" @click="doCreate">确 定</el-button>
        </span>
            </el-dialog>

            <!-- dialog -->
            <el-dialog
                    title="库区"
                    :visible.sync="dialogShowVisible"
                    width="50%"
                    :modal-append-to-body="false"
            >
                <el-table :data="detailList" stripe style="width: 100%">
                    <el-table-column prop="product.PRODCHINM" label="商品名" min-width="150"></el-table-column>
                    <el-table-column prop="stock_no" label="转入库区" min-width="150"></el-table-column>
                    <el-table-column prop="state_name" label="状态" min-width="150"></el-table-column>
                    <el-table-column prop="CHARG" label="批次号" min-width="150"></el-table-column>
                    <el-table-column prop="number" label="数量" min-width="150"></el-table-column>
                    <el-table-column prop="available_time" label="有效期" min-width="150"></el-table-column>
                </el-table>
                <el-pagination
                        @size-change="handleDSizeChange"
                        @current-change="handleDCurrentChange"
                        :current-page.sync="d.page"
                        :page-sizes="[10, 20, 50, 100]"
                        :page-size="d.limit"
                        layout="total,->, prev, pager, next, jumper, sizes"
                        :total="detail.length"
                ></el-pagination>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogShowVisible = false">取 消</el-button>
          <el-button type="primary" @click="dialogShowVisible = false">确 定</el-button>
        </span>
            </el-dialog>
        </div>
    </div>
</template>

<script>
    import { getById, relieve ,unfreeze,unfreezeIndex,getAllProducts,getGoodsByProductId} from "@/api/check";
    import { getAll as getAllStates } from "@/api/attributes";
    import instock from "@/components/share/instock";
    import { getToken } from "@/utils/auth";
    import { getLocations } from "@/api/location";



    export default {
        components: {
            instock
        },
        data() {
            return {
                locationStatus: 3,
                id: "",
                timeDefaultShow:'',
                area_ids: ['1','2','3'],
                goodsList: [],
                loading: false,
                postGoods:[],
                postGood:[],
                locationList: [],
                postGoodsList:[],
                codeInput: "",
                newcodeInput: "",
                attributesList: [{name:'C101'},{name:'C201'},{name:'C302'},{name:'C401'},{name:'C701'}],
                dialogVisible: false,
                show: true,
                detailLabel: "",
                order: {},
                goods: [],
                box_code: "",
                tmpInput: "",
                detail:[],
                dialogDataVisible: false,
                dialogShowVisible: false,
                tmpGoods: [],
                query: {
                    limit: 10,
                    page: 1
                },
                queryGoods: {
                    checkedProduct: "",
                    product_id: ""
                },
                s:{
                    limit: 10,
                    page: 1
                },
                q: {
                    limit: 10,
                    page: 1
                },
                d: {
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
            this.timeDefaultShow = new Date();
            this.timeDefaultShow.setMonth(new Date().getMonth() +36);
        },
        computed: {
            postShowList() {
                let offset = this.s.limit * (this.s.page - 1);
                return this.postGoods.slice(offset, offset + this.s.limit);
            },
            detailList() {
                let offset = this.d.limit * (this.d.page - 1);
                return this.detail.slice(offset, offset + this.d.limit);
            },
            showGoods(){
                let offset = this.q.limit * (this.q.page - 1);
                return this.goods.slice(offset, offset + this.q.limit);
            },
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
            leaves() {
                return id => {
                    var total = 0
                    for (let k = 0; k < this.postShowList.length; k++) {
                        if (this.postShowList[k].id == id) {
                            total = this.postShowList[k].number;
                        }
                    }
                    return total;
                };
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
            handleStatusChange(val) {
                this.locationStatus = val;
            },
            handleStockChange(stock_no, row) {
                // 数组用来记住第一个的下标
                var notUniq = {};
                var arr = [];
                for (let i = 0; i < this.postGoodsList.length; i++) {
                    let tmp = this.postGoodsList[i];
                    let str =
                            tmp.id +
                            "-A-" +
                            tmp.act_state_name +
                            "-B-" +
                            tmp.act_CHARG +
                            "-C-" +
                            tmp.act_stock_no +
                            "-D-" +
                            tmp.act_available_time;
                    if (tmp.act_stock_no && notUniq[str]) {
                        notUniq[str].act_number += tmp.act_number;
                    } else if (tmp.act_stock_no) {
                        notUniq[str] = tmp;
                    } else {
                        notUniq[tmp.index] = tmp;
                    }
                }

                for (let i in notUniq) {
                    arr.push(notUniq[i]);
                }
                this.postGoodsList = arr;
            },

            getSTime($event, row) {
                row.act_available_time = $event;
            },
            handleSizeChange: function(val) {
                this.query.limit = val;
            },

            handleCurrentChange: function(val) {
                this.query.page = val;
            },
            handleQSizeChange: function(val) {
                this.q.limit = val;
            },
            handleSSizeChange: function(val) {
                this.s.limit = val;
            },
            handleSCurrentChange :function(val) {
                this.s.page = val;
            },
            handleQCurrentChange: function(val) {
                this.q.page = val;
            },
            handleDSizeChange: function(val) {
                this.d.limit = val;
            },
            handleDCurrentChange: function(val) {
                this.d.page = val;
            },
            handleCreate() {
                this.box_code = new Date()-0+Math.floor(Math.random()*1);
                this.postGoods = [];
                this.goods = [];
                this.checked = [];
                this.queryGoods.product_id = "";
                this.queryGoods.checkedProduct = "";
                this.dialogVisible = true;
            },
            inputKeyup(row) {
                let leaves = this.leaves(row.id);
                row.valnumber = leaves;
            },
            loadOrder(id) {
                unfreeze({ id: id })
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
            showChoosed(row) {
                hasStocked({id:row.id, product_id:row.product.id}).then(res => {
                    this.tmpGoods = res.data
                this.dialogDataVisible = true;
            })
            },
            rollback() {
                relieve( this.goodsList ).then(res => {
                    this.$message({
                    message: "成功！",
                    type: "success"
                });
                this.loadOrder(this.$route.params.id);
                this.$router.push({
                    path: "/storage_action/check/particulars/"+this.$route.params.id
                });
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
            },
            delRow(row) {
                this.postGoods.splice(this.postGoods.indexOf(row), 1);
            },
            getGoods() {
                getAllProducts({
                    codeInput: this.codeInput,
                    newcodeInput: this.newcodeInput
                }).then(res => {
                    this.goods = res.data;
            });
            },
            loadLocations(query) {
                this.loading = true;
                this.locationList = [];
                if (query === "") {
                    this.loading = false;
                    return
                }
                getLocations({
                    area_ids: this.area_ids,
                    status: this.locationStatus,
                    stock_no: query
                }).then(res => {
                    this.loading = false
                let tmplist = res.data;
                    for (let i = 0; i < tmplist.length; i++) {
                        tmplist[i].value = tmplist[i].stock_no;
                        this.locationList = tmplist;
                    }
            });
            },
            addGoods() {
                if (this.checked.length == 0) {
                    this.$message({
                        message: "要添加的商品为空",
                        type: "warning"
                    });
                    return;
                }
                for (let i = 0; i < this.checked.length; i++) {
                    let flag = 0;
                    for (let j = 0; j < this.postGoods.length; j++) {
                        if (this.postGoods[j].id === this.checked[i].id) {
                            flag = 1;
                        }
                    }
                    this.checked[i].act_state_name = 'C101'
                    if (flag === 0) this.postGoods.push(this.checked[i]);
                }
                this.$message({
                    message: "添加成功",
                    type: "success"
                });
                this.codeInput = ''
                this.newcodeInput = ''
            },
            handleSelectionChange(val) {
                this.checked = val;
            },
            exportOut() {
                let token = getToken();
                token = token.split(" ", 2);
                var goodsIdsList = [];
                for (let i = 0; i < this.goodsList.length; i++) {
                    goodsIdsList.push(this.goodsList[i].id);
                }
                window.open(
                        "/api/check/export?id=" + goodsIdsList + "&query=" + JSON.stringify(this.query)  + "&token=" + token[1],
                        "_blank"
                );
                localStorage.out_adjust_query = JSON.stringify(this.query);
                this.loadData();
            },
            doCreate() {
                if (this.postGoods.length === 0) {
                    this.$message({
                        message: "请选择要添加的商品",
                        type: "warning"
                    });
                    return;
                }
                this.postGood = [];
                this.postGood.push({
                    code: this.$route.params.id,
                    postGoods:this.postGoods
                });
                getGoodsByProductId(this.postGood).then(res => {
                    this.$message({
                    message: "创建成功",
                    type: "success"
                });
                this.postGood = [];
                this.dialogVisible = false;
                this.loadOrder(this.$route.params.id);
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
    .selected-title {
        margin: 15px 0;
        font-size: 16px;
    }
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
    .choose-ensure {
    .el-input {
        width: 300px;
        display: inline-block;
        margin-right: 10px;
    }
    margin-bottom: 30px;
    }
    .el-dialog--dl-small {
        width: 600px;
    }
    .table-form-item {
        position: relative;
        height: 100%;
        width: 100%;
    .error-text {
        font-size: 12px;
        color: #f97042;
    }
    }
</style>