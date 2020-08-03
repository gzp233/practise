<template>
	<div class="layout-nav">
		<ul class="slide-navbar">
			<li :class="item.navClass" v-for="item in columns">
				<router-link :to="'/'+subdata.module+'/'+item.id" :key="item.id">
					<span>{{item.text}}</span>
				</router-link>
			</li>
		</ul>
		<!-- <div class="navbar-footer"><a href="javascript:void(0);" class="icon_down">{{ columns }}</a></div> -->
	</div>
</template>

<script>
export default {
  data: function() {
    return {
      array: []
    }
  },
  name: 'navbar',
  props: ['subdata'],
  computed: {
    columns() {
      var columns = []
      const path = this.$route.path.split('/')
      for (var i = 0; i < this.subdata.sub.length; i++) {
          if(this.subdata.sub[i].id == path[2]) {
            this.subdata.sub[i].navClass = 'active navbar-item'
          } else {
            this.subdata.sub[i].navClass = 'navbar-item'
          }
          columns.push(this.subdata.sub[i])
      }

      return columns
    }
  },
}
</script>

<style lang="less">
.layout-nav {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 160px;
  background: #dbe1e6;
  z-index: 99;
  .slide-navbar {
    width: 160px;
    height: auto;
    overflow: hidden;
    .active{
      background: #c0bdbd83;
    }
    .navbar-item {
      height: 40px;
      a {
        position: relative;
        display: block;
        height: 40px;
        line-height: 40px;
        border-left: 2px solid #dbe1e6;
        padding-left: 10px;
        text-decoration: none;
        cursor: pointer;
        span {
          color: #777;
          font-size: 14px;
        }
      }
      a.active {
        background: #ced4d9 url(../../assets/icon_arr_01.png) no-repeat right
          center;
        border-left: 4px solid #486181;
        span {
          color: #444;
        }
      }
    }
  }
  .navbar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 44px;
    border-top: 1px solid #ced4d9;
    text-align: center;
    a {
      line-height: 44px;
      display: inline-block;
      font-size: 12px;
      color: #999;
      padding-left: 20px;
      text-decoration: none;
      background: url(../../assets/icon_down.png) no-repeat center left;
    }
  }
}
</style>