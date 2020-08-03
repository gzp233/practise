<template>
    <div class="button-group">
        <v-header :navdata="navData"></v-header>
        <mt-button size="large" v-permission="'6EAE5933379EB916FD46CCC29EEAB87A'" class="button-item button-white" type="default" @click="pandian">盘点扫码</mt-button>
        <mt-button size="large" v-permission="'E30F4F07BA567B4251E5FCB8FC91C7CE'" class="button-item button-white" type="default" @click="exportOut">出库复核</mt-button>
        <mt-button size="large" v-permission="'0224D36D7244A43220EBEEA2ABCECF47'" class="button-item button-white" type="default" @click="jianhuo">出库拣货</mt-button>
        <mt-button size="large" v-permission="'8DBFC6FB150BC863657F10D315F02B23'" class="button-item button-white" type="default" @click="jihuo">集货扫码</mt-button>
        <mt-button size="large" v-permission="'B8104099424DB41C0678B76C15420E47'" class="button-item button-white" type="default" @click="bozhong">播种扫码</mt-button>
        <mt-button size="large" v-permission="'92E8751E5401CF349D84DB2C66182BEA'" class="button-item button-white" type="default" @click="move">移库扫码</mt-button>
        <mt-button size="large" v-permission="'89D8DC7E46F5F6994B7FF154AC79F4A3'" class="button-item button-white" type="default" @click="fangchuanhuo">防串货</mt-button>
        <mt-button size="large" class="button-item" type="primary">返回</mt-button>
    </div>
</template>

<script>
import appHeader from '@/components/common/header'
export default {
    name: 'scanMenu',
    data() {
        return {
            navData: []
        }
    },
    methods: {
        exportOut(){
            this.$router.push({
                path: "/barcode/reivew/"
            });
        },
        jianhuo() {
            this.$router.push({
                path: "/barcode/jianhuo/"
            });
        },
        bozhong() {
            this.$router.push({
                path: "/barcode/bozhong/"
            });
        },
        jihuo(){
            this.$router.push({
                path: "/barcode/jihuo/"
            });
        },
        move(){
            this.$router.push({
                path: "/barcode/movement/"
            })
        },
        pandian(){
            this.$router.push({
                path: "/barcode/pandian/"
            })
        },
        fangchuanhuo(){
            this.$router.push({
                path: "/barcode/fangchuanhuo/"
            })
        },
        ocr(){
            this.$router.push({
                path: "/barcode/ocr/"
            })
        },
    },

    components: {
            'v-header': appHeader
        }
}
</script>
<style lang="less" scoped>
.layout-header{position: fixed}
.el-message-box {
  width: 80%!important;
}
.header-login {
  height: 50px;
  width: 100%;
  margin: 0 auto;
  padding: 60px 0 20px;
}
.header-login .login {
  display: inline-block;
  line-height: 50px;
  padding-left: 155px;
  background: url(../../assets/logo_01.png) no-repeat left center;
}
.header-login .login span {
  display: inline-block;
  border-left: 1px solid #d1d1d1;
  padding-left: 20px;
  height: 40px;
  line-height: 40px;
  margin-top: 5px;
  font-size: 24px;
  color: #333;
}
.button-group {
  margin: 70px 10px 10px 10px;
  max-width: 750px;
}
.button-group .button-item {
  line-height: 26px;
  margin: 20px 0;
  font-size: 16px;
  border-radius: 6px;
  box-shadow: 0 0 1px #ffffff;
}
.button-group .button-white {
  background: #ffffff;
}

</style>
