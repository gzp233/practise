<template>
    <div class="input-group">
        <v-header :navdata="navData"></v-header>
        <el-table :data="tableData" @row-click="handleNumber">
            <el-table-column prop="yiku_no" label="移库单号" ></el-table-column>
            <el-table-column prop="changeState" label="状态"></el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
            <el-button class="back" type="primary" @click="handleBack">返 回</el-button>
            <el-button class="next" :disabled="disabled" type="primary" @click="doCreate">创 建</el-button>
        </span>
    </div>
</template>

<script>
import appHeader from "@/components/common/header";
import { getYikuNos, createTask} from "@/api/out";
import { createTime } from "@/utils/common";
export default {
    data() {
        return {
            navData: [],
            tableData: [],
            statusList:"",
            disabled: false
        }
    },
    components: {
        "v-header": appHeader
    },
    mounted(){
        getYikuNos().then(res =>{  //移库单列表
            console.log(res);
            this.statusList = res.data
            this.tableData = this.statusList;
            for(let i=0;i<this.statusList.length;i++){
                if(this.statusList[i].status == 0){
                    this.statusList[i]["changeState"] = "未处理";
                };
                if(this.statusList[i].status == 1){
                    this.statusList[i]["changeState"] = "处理中";
                };
                if(this.statusList[i].status == 2){
                    this.statusList[i]["changeState"] = "已处理";
                }
            }
        })
    },
    methods:{
        handleBack(){  // 返回
            this.$router.push({
                path: "/barcode/menu"
            })
        },
        handleNumber(row, column, event){  //行
           console.log(row, column, event);           
                if(row.source == 0){
                    this.$router.push({
                        path: "/barcode/movement/taskout",
                        query:{
                            moveNumber:row.yiku_no
                        }
                    })
                };
                if(row.source == 1){
                    this.$router.push({
                        path: "/barcode/movement/createin",
                        query:{
                            moveNumber:row.yiku_no
                        }
                    })
                }              
                      
        },
        doCreate(){  //创建
            this.disabled = true;
            createTask().then(res =>{
                this.$router.push({
                    path: "/barcode/movement/createout",
                    query:{
                        moveNumber:res.data
                    }
                })
            }).catch(res =>{
                this.disabled = false;
            });          
        },
    },
};
</script>
<style scoped>
.el-table th>.cell{text-align: center!important;}
.input-group {
  margin: 75px 10px 10px 10px;
  max-width: 750px;
  font-size: 16px;
  height: 100%;
}
.input-group .button-item {
  height: 30px;
  margin: 20px 0;
  font-size: 16px;
}
.next,.back{width: 30%!important;}
.next{margin: 0 0 0 38%!important}
.dialog-footer {
  margin: 35px 0 0 0;
  display: block;
}
</style>
