<template>
    <div class="input-group">
        <v-header :navdata="navData"></v-header>
        <div class="numbers">单号：{{this.$route.query.id}}</div>
        <div class="location">库位号：{{this.$route.query.stock_no}}</div>
        <div class="detail_content">
            <div class="head">
        <span
                style="font-size:18px !important;text-align:center;position: absolute;top:32px;left:0"
        >盘点扫描</span>
                <el-input
                        style="width:260px;position: absolute;right:0"
                        v-model="tmpData.tmpInput"
                        class="button-item"
                        placeholder="请扫描"
                        id="codeInput"
                        @keyup.enter.native="handleAdd"
                ></el-input>
            </div>
            <br>
            <span>支码：{{ form.zhima }}</span>

            <div class="show-group">
                <el-table :data="records" stripe style="width: 100%;" max-height="250">
                    <el-table-column
                            class="button-item"
                            prop="product.NewPRODUCTCD"
                            label="新产品代码"
                            min-width="60"
                    ></el-table-column>
                    <el-table-column class="button-item" prop="available_time" label="有效期" min-width="40"></el-table-column>
                    <el-table-column class="button-item" prop="state" label="状态" min-width="40"></el-table-column>
                    <el-table-column class="button-item" v-if="inventory" prop="number" label="盘点数量" min-width="50"></el-table-column>
                    <el-table-column class="button-item" prop="scanNumber" label="扫描数量" min-width="50">
                        <template slot-scope="scope">
                            <el-input @keyup.native="handleChange(scope.row)" @focus="tally(scope.row)" v-model="scope.row.scanNumber" placeholder="请输入数量"></el-input>
                        </template>
                    </el-table-column>
                    <!-- <el-table-column class="button-item" label="操作" min-width="40">
                      <template slot-scope="scope">
                        <el-button type="text" size="small" @click="delCode(scope.row)"
                        >删除</el-button
                        >
                      </template>
                    </el-table-column> -->
                </el-table>
                <el-dialog
                        title="提示"
                        :visible.sync="centerDialogVisible"
                        :modal-append-to-body="false" width="100%">
                    <span>此操作不能重复提交！是否继续？</span>
          <span slot="footer" class="dialog-footer">
            <el-button @click="subCancel">取 消</el-button>
            <el-button type="primary" @click="subConfirm">确 定</el-button>
          </span>
                </el-dialog>
        <span slot="footer" class="dialog-footer">
          <el-button class="back" type="primary" @click="handleBack">返 回</el-button>
          <el-button class="next" type="primary" @click="doCreate">提 交</el-button>
        </span>
            </div>
        </div>
    </div>
</template>

<script>
    import appHeader from "@/components/common/header";
    import { getPandianStock,barCode,doPandianStock } from "@/api/pandian";
    import { getXiaoqi } from "@/utils/common";
    import { setTimeout } from 'timers';
    import { release } from 'os';
    export default {
        name: "scan",
        data() {
            return {
                navData: [],
                records: [],
                tmpData: { tmpInput: "" },
                form: {
                    zhima: ""
                },
                effective:"",
                centerDialogVisible: false,
                codeaux:"",
                code_time:"",
                inventory: true,
                zmnum:0,
                PRODUCT: "",
                XIAOQI: {},
                count: 0
            };
        },
        components: {
            "v-header": appHeader
        },
        mounted() {
            this.load()
        },
        methods: {
            handleChange(row){
                row.scanNumber = row.scanNumber.replace(/[^0-9]/g, '');
                if(row.scanNumber == ""){
                    row.scanNumber = 0;
                }
            },
            load(){
                getPandianStock(this.$route.query).then(res => {
                  if(res.message == '用户错误'){
                    this.$message({
                        type: "warning",
                        message: "用户错误"
                     });
                    this.$router.push({
                            path: "/barcode/pandian/stockList/"+this.$route.query.id
                    });
                    return;
                  }
                    console.log(res.message)
                    this.records = res.data;
                    if(Object.is(this.records[0].rad,0)){
                        this.inventory = true;
                    }else{
                        this.inventory = false;
                    };
                    for(let j=0;j<this.records.length;j++){
                        if(Object.is(this.records[j].product_id,null)){
                            this.records.splice(j,1)
                        };
                        this.$set(this.records[j],"add",0)
                    };
                });
            },
            tally(row){
                row.add++;
                if(row.add == 1){
                    row.scanNumber = 0;
                }
            },
            handleAdd() {
                let tit = this.tmpData.tmpInput;
                setTimeout(() => {
                    this.$set(this.tmpData, "tmpInput", "");
            }, 100);
                if (!this.form.zhima) {
                    if (tit.length != 23 && tit.length != 13 && tit.length != 14 && tit.length != 12) {
                        alert("型号码23位,单只码13位或14位！");
                        return;
                    }
                    if (tit.length == 23) {
                        let newProductCd = tit.slice(9, 20);
                        let number = Number(tit.slice(20, 23));
                        var xiaoqi = "";
                        barCode({"code":newProductCd}).then(res =>{
                            this.effective = Number(getXiaoqi(tit.slice(3, 7),res.data[0]));
                        localStorage.setItem("effective",this.effective)
                    });
                        // 验证
                        for (let i = 0; i < this.records.length; i++) {
                            if (newProductCd == this.records[i].product.NewPRODUCTCD) {
                                this.records[i].scanNumber += number;
                                return;
                            }
                        };
                        //商品不存在
                        let obj = {
                            product:{},
                            "goods_id":"0",
                            "stock_no":this.$route.query.stock_no,
                            "batches":this.$route.query.id
                        };
                        obj.product["NewPRODUCTCD"] = newProductCd;
                        obj["available_time"] = localStorage.getItem("effective");
                        obj["state"] = "";
                        obj["number"] = Number(0);
                        obj["scanNumber"] = number;
                        this.records.unshift(obj);
                    } else {
                        if (tit.length == 12 || tit.length == 13 || tit.length == 14) {
                            for (let i = 0; i < this.records.length; i++) {
                                if(this.count == 1){
                                    this.form.zhima = tit;
                                    this.count = 2;
                                    return;
                                };
                                if(this.count == 2){
                                    if(tit == this.PRODUCT) {
                                        if (this.XIAOQI.number <= this.XIAOQI.scanNumber) {
                                        this.$message({
                                            type: "warning",
                                            message: "该产品已扫描完成！"
                                        });
                                        this.count = 1;
                                        return;
                                        };
                                        this.XIAOQI.scanNumber +=1;
                                        this.form.zhima = "";
                                        return;
                                    };
                                };
                                if(tit == this.PRODUCT) {
                                    if (this.XIAOQI.number <= this.XIAOQI.scanNumber) {
                                    this.$message({
                                        type: "warning",
                                        message: "该产品已扫描完成！"
                                    });
                                    this.count = 1;
                                    return;
                                    };
                                    this.XIAOQI.scanNumber +=1;
                                    this.form.zhima = "";
                                    return;
                                };
                                if (tit == this.records[i].product.barCode) {
                                    if (this.records[i].number <= this.records[i].scanNumber) {
                                    this.$message({
                                        type: "warning",
                                        message: "该产品已扫描完成！"
                                    });
                                    return;
                                    }
                                    this.form.zhima = tit;
                                    return;
                                };
                            }
                            // 支码不存在
                            barCode({"code":tit}).then(res =>{
                                this.codeaux = res.data[0];
                            console.log(res);
                        });
                            this.form.zhima = tit;
                            //alert("该产品不在该单出库库位上！");
                        }
                    }
                } else {
                    if (tit.length != 6) {
                        alert("有效期只能是6位！");
                        return;
                    };
                    for(let k=0;k<this.records.length;k++){
                        if(this.records[k].product.barCode == this.form.zhima && this.records[k].available_time == tit){
                            this.PRODUCT = this.records[k].product.barCode;
                            this.XIAOQI = this.records[k];
                            this.records[k].scanNumber += 1;
                            this.form.zhima = "";
                            return;
                        }
                    };
                    let target = {
                        product:{},
                        "scanNumber": 0,
                        "goods_id":"0",
                        "stock_no":this.$route.query.stock_no,
                        "batches":this.$route.query.id
                    };
                    this.code_time = tit;
                    target.product["NewPRODUCTCD"] = this.codeaux;
                    target.product["barCode"] = this.form.zhima;
                    target["available_time"] = this.code_time;
                    target["state"] = "";
                    target["number"] = Number(0);
                    target.scanNumber += 1;
                    this.records.unshift(target);
                    this.form.zhima = "";
                    console.log(this.records)
                    return;
                }
            },
            subConfirm(){
                this.centerDialogVisible = false;
                let arr = this.coppyArray(this.records);
                if(arr.length == 0){
                    let foo = {};
                    foo["id"] = this.$route.query.id;
                    foo["stock_no"] = this.$route.query.stock_no;
                    doPandianStock(foo).then(res =>{
                        if(Object.is(res.code,200)){
                        this.$message({
                            tyep:"success",
                            message:"提交成功！"
                        });
                        this.$router.push({
                            path: "/barcode/pandian/stockList/"+this.$route.query.id
                        });
                    }
                })
                }else{
                    console.log(arr.length)
                    for(let k=0;k<arr.length;k++){
                        if(arr[k].scanNumber == 0 && arr[k].number == 0){
                            arr.splice(k,1);
                        };
                    };
                    doPandianStock(arr).then(res =>{
                        if(Object.is(res.code,200)){
                        this.$message({
                            tyep:"success",
                            message:"提交成功！"
                        });
                        this.$router.push({
                            path: "/barcode/pandian/stockList/"+this.$route.query.id
                        });
                    }
                })
                };
            },
            subCancel(){
                this.centerDialogVisible = false;
                this.$message({
                    tyep:"info",
                    message:"已取消提交！"
                });
            },
            coppyArray(arr){
                let res = [];
                for (let i = 0; i < arr.length; i++) {
                    res.push(arr[i])
                };
                return res;
            },
            doCreate() {
                this.centerDialogVisible = true;
            },
            handleBack() {
                this.$router.push({
                    path: "/barcode/pandian/stockList/"+this.$route.query.id
                });
            }
        }
    };
</script>

<style lang="less" scoped>

    .el-input /deep/ .el-input__inner{text-align: center}
    .numbers,.location{margin: 70px 0 0 0;font-size: 18px}
    .location{margin: 20px 0 0 0;}
    .next,.back{width: 30%!important;}
    .next{margin: 0 0 0 38%!important}
    .detail_content {
        height: auto;
    }
    .layout-header {
        position: fixed;
    }
    .dialog-footer {
        margin: 35px 0 0 0;
        display: block;
    }
    .head {
        width: 100%;
        position: relative;
        height: 60px;
    }
    .input-group {
        margin: 50px 10px 10px 10px;
        max-width: 750px;
        font-size: 16px;
        height: 100%;
    }
    .input-group .button-item {
        height: 30px;
        margin: 20px 0;
        font-size: 16px;
    }
    .input-groud /deep/ .el-input__inner[placeholder="请扫描"] {
        height: 150px !important;
    }
    .input-groud /deep/ .el-input__inner[placeholder="请扫描"] {
        height: 150px !important;
    }
    .show-group {
        margin: 30px auto;
        width: 100%;
        font-size: 16px;
    }
    .show-group .button-item {
        height: 30px;
        margin: 100px 0;
        font-size: 500px;
    }
</style>
<style lang="less">
    @media screen and (max-width: 768px){
        .el-dialog{width: 80%;}
    }
    /*.el-input__inner{*/
    /*height: 80px !important;*/
    /*width: 500px !important;*/
    /*}*/
    .el-table th > .cell {
        font-size: 14px;
    }
    .el-table .cell,
    .el-table th > div {
        font-size: 14px;
        padding: 0;
        text-align: center;
    }
    .el-table__header-wrapper {
        height: 45px !important;
    }
    .el-pagination__total {
        margin: 33px 0 0 0;
    }
    .btn-prev {
        font-size: 22px !important;
    }
    .el-pagination__editor {
        font-size: 14px !important;
        padding: 3px 2px;
    }
    .el-pagination__jump {
        font-size: 14px !important;
        margin: 0 10px;
    }
    .el-input/deep/ .el-input__inner {
        height: 27px !important;
    }
    .button-item /deep/.el-input__inner {
        height: 40px !important;
    }
    .el-pagination {
        padding: 10px 5px;
    }
    .el-pagination__sizes {
        margin: 0 0 0 20px;
    }
</style>
