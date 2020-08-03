<template>
	<div class="layout-main">
		<v-navbar :subdata="subdata"></v-navbar>
		<div class="layout-right-content">
			<transition name="fade" mode="out-in">
				<router-view></router-view>
			</transition>
		</div>
	</div>
</template>

<script>
import Navbar from '@/components/common/navbar'
export default {
  props: ['navdata'],
  computed: {
    subdata() {
      var subdata = {}
      const path = this.$route.path.split('/')
      for (var i = 0; i < this.navdata.length; i++) {
        if (this.navdata[i].module == path[1]) {
          subdata = this.navdata[i]
        }
      }
      const permissions = this.$store.getters.permissions

      if (permissions.length > 0 && this.$store.getters.roles.indexOf('admin') < 0) {
        // 是否有模块权限
        if (subdata.permission) {
          if (permissions.indexOf(subdata.permission) < 0) {
            this.$router.push({ path: '/' })
          }
        }
        // 是否有侧边栏权限
        for (let i = 0; i < subdata.sub.length; i++) {
          if (
            subdata.sub[i].permission &&
            permissions.indexOf(subdata.sub[i].permission) < 0
          ) {
            subdata.sub.splice(i, 1)
          }
        }
      }
      return subdata
    }
  },
  components: {
    'v-navbar': Navbar
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="less" scoped>
.layout-container {
  position: absolute;
  right: 0;
  top: 50px;
  bottom: 0;
  left: 0;
  z-index: 9;
  .layout-right-content {
    position: absolute;
    top: 0;
    left: 160px;
    right: 0;
    bottom: 0;
    border-left: 1px solid #ced4d9;
    overflow-y: scroll;
    background: #f0f0f0;
  }
}
</style>