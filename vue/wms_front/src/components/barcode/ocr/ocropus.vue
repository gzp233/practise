<template>
  <div class="input-group">
    <v-header :navdata="navData"></v-header>
    <div class="ocr_box">
        <el-upload
            class="upload-demo"
            action=""
            :limit="1"   
            v-loading="loading"        
            :show-file-list="false" 
            :before-upload="beforeUpload"
            accept=".jpg,.jpeg,.JPG,.JPEG"
            >
            <el-button size="small" type="primary">点击上传</el-button>
            <div slot="tip" class="el-upload__tip">只能上传jpg文件，且不超过3M</div>
        </el-upload>
        <div class="tag">扫描结果：
          <ul>
            <li v-for="item in tag">
              {{item}}
            </li>
          </ul>
        </div>
    </div>    
    <span slot="footer" class="dialog-footer">
      <el-button type="primary" class="button-item back" @click="handleBack">返 回</el-button>
      <!-- <el-button type="primary" class="button-item next" @click="handleocr">提 交</el-button> -->
    </span>
  </div>
</template>

<script>
import appHeader from "@/components/common/header";
import {upload} from "@/api/ocr";
export default {
  name: "scan",
  data() {
    return {
     navData:[],
     loading:false,
     tag:""
    };
  },
  components: {
    "v-header": appHeader
  },    
  methods: { 
    handleaction(url) {        
      upload({id:url}).then(res =>{
        console.log(res)
        if(Object.is(res.code,200) && Array.isArray(res.data) && Object.is(res.data.length,0)){
          this.$message("未识别!");
          this.tag = "";
          return;            
        }else{
          this.tag = res.data
        };       
      })        
    },    
    getBase64(file) {  // 异步 转 base64
      return new Promise(function(resolve, reject) {
        let reader = new FileReader();
        let imgResult = "";
        reader.readAsDataURL(file);
        reader.onload = function() {
          imgResult = reader.result;
        };
        reader.onerror = function(error) {
          reject(error);
        };
        reader.onloadend = function() {
          resolve(imgResult);
        };
      });
    },   
    getSuccess(file){
      this.loading = true;
      setTimeout(() => {
          this.loading = false;
      }, 1000); 
      this.getBase64(file).then(res => {                    
          let base64 = res.split(",")[1]
          this.handleaction(base64)
      }).catch(err =>{
          this.$message(err)
      });
    },     
    beforeUpload(file) {
        let testmsg=file.name.substring(file.name.lastIndexOf('.')+1);				
        const extension = testmsg === 'jpg';
        const extension2 = testmsg === 'jpeg';
        const isLt2M = file.size / 1024 / 1024 < 3;     //这里做文件大小限制
        if(!extension && !extension2 && !extension3 && !extension4) {
            this.$message({
                message: '上传文件只能是jpg/jpeg格式!',
                type: 'warning'
            });
            return;
        }else{
            if(!isLt2M) {
                this.$message({
                    message: '上传文件大小不能超过 3MB!',
                    type: 'warning'
                });
                return;
            }else{
              this.getSuccess(file)
            };
        };        
        return extension || extension2 && isLt2M
    }, 
    handleBack() {
      this.$router.push({
        path: "/barcode/menu/"
      });
    },  
    handleocr(){
        this.$message("文件提交成功！");
        this.$router.push({
            path: "/barcode/ocr/"
        });        
    }  
  }
};
</script>


<style lang="less" scoped>
/*清除浮动*/
.clearfloat:after{display:block;clear:both;content:"";visibility:hidden;height:0}
.clearfloat{zoom:1}
.next,.back{width: 30%!important;margin: 0 0 0 70%!important}
.next{margin: 0 0 0 38%!important}
.layout-header {
  position: fixed;
}
.tag{margin: 20px 0 0 0;}
.tag ul li{line-height: 26px}
.ocr_box{margin: 70px auto 0 auto;}
.head {
  width: 100%;
  position: relative;
  height: 60px;
}
h1 {
  font-size: 26px;
  text-align: center;
}
.input-group {
  margin: 50px auto;
  max-width: 750px;
  width: 94%;
}
.input-group .button-item {
  margin: 20px 0 0 0;
  width: 100%;
}
</style>