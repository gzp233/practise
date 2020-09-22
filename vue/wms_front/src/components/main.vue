<template>
    <div id="main">
        <v-header :navdata="navData"></v-header>
        <div class="layout-container">
            <transition name="fade" mode="out-in">
                <router-view :navdata="navData" :key="key"></router-view>
            </transition>
        </div>
    </div>
</template>

<script>
import appHeader from '@/components/common/header'
export default {
  name: 'appMain',
  data: function() {
    return {
      navData: []
    }
  },
  computed:{
    key() {
      return this.$route.name !== undefined ? this.$route.name + new Date() : this.$route + new Date()
    }
  },
  created: function() {
    this.navData = [
      {
        text: '首页',
        module: 'index'
      },
      {
        text: '基本信息',
        module: 'baseinfo',
        icon: 'ui-icon ui-icon-instorage',
        permission:'A1D900F5C06D6C0ECABA678BF182D0ED',
        sub: [
          {
            text: '客户列表',
            id: 'customer_manage',
            permission:'090841164BE1919CFBEB53524E27F0B4'
          },
          {
            text: '公司列表',
            id: 'company',
            permission:'CC14828FD389A4C8355BD86DB6F67F2E'
          },
          {
            text: '品牌列表',
            id: 'brand',
            permission: 'B0D021DC14D003F27BD9412128E03F82'
          },
          {
            text: '送货地列表',
            id: 'deliver',
            permission:'90273E65E45DE3407AC52264B8DDE58F'
          },
          {
            text: '产品区分列表',
            id: 'flg',
            permission:'BA3FD0700049E5F12DA8DF42F9147136'
          },
          {
            text: '产品列表',
            id: 'product',
            permission:'9E85D5486B0AA6E441B660129FB025F5'
          },
          {
            text: '订单原因列表',
            id: 'reason',
            permission:'B84A0DA2DA7E32AFDFEF890064782A5C'
          },
          {
            text: '返回原因列表',
            id: 'rejected',
            permission:'4587454B3AC70F14D55171F786B5D703'
          },
          {
            text: '产品系列列表',
            id: 'series',
            permission:'61E25E09832AB6F878D491415D1D637C'
          },
          {
            text: '供应商列表',
            id: 'vendor',
            permission:'F21A729B835EA1FD6BD22C748E74CB8C'
          }
        ]
      },
      {
        text: '入库作业',
        module: 'instorage_action',
        icon: 'ui-icon ui-icon-stock',
        permission: '753174F8638EBF52E7F4DD0C4EC8BCA7',
        sub: [
          {
            text: '采购入库',
            id: 'procurement_storage',
            permission: '074BDEC0E56F394D9D56F2142CA3D409'
          },
          {
            text: '退货入库',
            id: 'sales_returns_warehousing',
            permission: '3A12A39B51126775C94353B45AAE3580'
          },
          {
            text: '内向交货入库',
            id: 'inner_storage',
            permission: 'BF9866C3F8AE6F47A2E31364CAB79FB4'
          },
          {
            text: '移动入库',
            id: 'movement_storage',
            permission: '31CB76D14D63AF5E45AD39740BA237AE'
          },
          {
            text: '在库调整',
            id: 'adjust',
            permission: 'E250AFBA1B86BD993248F7090281469F'
          },
          {
            text: '入库加工',
            id: 'instorage_process',
            permission: '1DBAAA6F8720DDD59C1F8C9B286D0865'
          }
        ]
      },
      {
        text: '库内作业',
        module: 'storage_action',
        icon: 'ui-icon ui-icon-user',
        permission: 'D69E7314DEBE2FE5B8384D8C061285B6',
        sub: [
          {
            text: '商品库存',
            id: 'goods_stock',
            permission: 'F8C1C778BC0D15D5D78DC9F7821B091F'
          },
          {
            text: '入库汇总',
            id: 'enter',
            permission: '09918D1675A600F7EAAA78ED0DFA727A'
          },
          {
            text: '出库汇总',
            id: 'appear',
            permission: 'C390E8CF7402798D824D500AE0802D3B'
          },
          {
            text: '库存冻结',
            id: 'frost',
            permission: '7932C37DF6838A36780F4F797E62CC7D'
          },
          {
            text: '全仓/区域盘点',
            id: 'check',
            permission: '7731FB0FE5DE9974F22BC6C3DECFBA19'
          },
          {
            text: '异动盘点',
            id: 'diff_check',
            permission: '45BF4C2EA0325A1423597554D9077EFA'
          },
          {
            text: '库内移动',
            id: 'move_stock2',
            permission: '0F9AC4C003655D37AA90E51859AE826D'
          }
        ]
      },
      {
        text: '出库作业',
        module: 'outstorage_action',
        icon: 'ui-icon ui-icon-outgoing',
        permission: '7BA525D1784B7F364F09535016166A72',
        sub: [
          {
            text: '受注出库',
            id: 'sell_out',
            permission: '1F1E6E615587530B99C8F0B4C89AA0C9'
          },
          {
            text: '移动出库',
            id: 'move_out',
            permission: '24F0BF1CCF72AC7C55737AF28913186A'
          },
          {
            text: '在库调整',
            id: 'adjust',
            permission: 'D25679399C1721F803A50443CB9647E6'
          },
          {
            text: '防串货',
            id: 'anticode',
            permission: '135ABD17AC2A072DAA3EE9AB0BE1AC87'
          },
          {
            text: '集货',
            id: 'consolidation',
            permission: '5CE587C99A9C2B63AAB24271060768AA'
          }
        ]
      },
      {
        text: '用户配置',
        module: 'users',
        icon: 'ui-icon ui-icon-user',
        permission: '3EF5724B5FD1086F43D9A0894819439A',
        sub: [
          {
            text: '用户管理',
            permission: '7F63625130EB64094A931192F1EA491A',
            id: 'user'
          },
          {
            text: '角色管理',
            permission: '1CF7FBB0CCE48D04E357AA78C6D74293',
            id: 'role'
          },
          {
            text: '权限管理',
            permission: '80007654A3C9B46B95F9516F1308A5F1',
            id: 'permission'
          }
        ]
      },
      {
        text: '基础配置',
        module: 'basic',
        permission: 'BC06A30DF9C33F50740D3DC8837FAA64',
        icon: 'ui-icon ui-icon-user',
        sub: [
          {
            text: '仓库管理',
            permission: '2B2D844CFFF28452BB4ED74506923C0F',
            id: 'warehourse'
          },
          {
            text: '库区管理',
            permission: 'C425620AB1CCC1B8AE57E4B066FFC6CB',
            id: 'area',
          },
          {
            text: '库位管理',
            permission: '5A88294519AA8B7E5EFB4E483FBB6E47',
            id: 'location',
          },
          {
            text: '商品属性管理',
            permission: 'D3F678220D3D7F79285F0714EEB434A9',
            id: 'attributes'
          }
        ]
      },
      {
        text: '后台管理',
        module: 'management',
        permission: 'D369BAD80CC23260EE99B52963C189CA',
        icon: 'ui-icon ui-icon-user',
        sub: [
          {
            text: '商品库存',
            permission: 'CB7570DC39A0DB6B57EA81E731C48C58',
            id: 'goodslist'
          },
        ]
      },
      {
        text: '扫描条码',
        module: 'barcode',
        permission: 'F0104BA0DA57416163D3BEE833642E5B',
        icon: 'ui-icon ui-icon-user',
        sub: []
      }
    ]
  },
  components: {
    'v-header': appHeader
  }
}
</script>

<style lang="less">
.layout-container {
  position: absolute;
  right: 0;
  top: 50px;
  bottom: 0;
  left: 0;
  overflow: hidden;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease-out;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.page-bread {
  height: 40px;
  padding: 0 15px;
  background: #fff;
  border-bottom: 1px solid #eaeaea;
  .el-breadcrumb {
    line-height: 40px;
  }
}
.search-form {
  height: auto;
  overflow: hidden;
  padding: 22px 15px 0;
  background: #f9f9f9;
}
.grid-toolbar {
  height: auto;
  overflow: hidden;
  padding: 10px 20px;
  .tool-btn {
    display: inline-block;
    padding: 8px 15px;
    border: none;
    background: none;
    font-size: 12px;
    border-radius: 3px;
    outline: none;
    cursor: pointer;
    margin: 0 2px;
  }
  .btn-refresh {
    background: #76c884 url(../assets/icon_refresh.png) no-repeat 10px center;
    background-size: 15px 15px;
    padding-left: 32px;
    color: #fff;
  }
  .btn-add {
    background: #efa759 url(../assets/icon_add.png) no-repeat 10px center;
    background-size: 15px 15px;
    padding-left: 32px;
    color: #fff;
  }
  .btn-export {
    background: url(../assets/icon_export.png) no-repeat 10px center;
    background-size: 13px 13px;
    padding-left: 25px;
    color: #777;
  }
}
.grid-content {
  height: auto;
  overflow: hidden;
  padding: 0 10px;
  .el-table th {
    background: #fff;
  }
  .el-table__header-wrapper {
    border-bottom: 1px solid #eaeaea;
  }
  .el-table__header-wrapper thead div,
  .el-table__fixed-header-wrapper thead div {
    background: #fff;
    padding: 20px 18px 10px;
    color: #444;
    font-weight: bold;
  }
  .el-table td {
    height: 48px;
  }
  .el-button--text {
    color: #4796ef;
  }
  .el-table--striped .el-table__body tr.el-table__row--striped td {
    background: #f4f8fb;
  }
}
.grid-page {
  height: auto;
  overflow: hidden;
  padding: 25px 15px;
  .el-pagination__sizes {
    margin: 0 0 0 10px;
  }
  .el-pagination__total {
    margin: 0;
  }
}
.statistic-content {
  height: auto;
  overflow: hidden;
  padding: 10px;
  color: #f97042;
  border: 1px solid #fee7a9;
  border-radius: 4px;
  background: #fffbee;
  text-align: center;
  font-size: 16px;
  span {
    display: inline-block;
    padding: 0 5px;
    font-size: 14px;
  }
}
</style>
