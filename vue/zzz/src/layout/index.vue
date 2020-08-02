<template>
  <div class="page-wrapper">
    <app-header />
    <div class="home-wrapper f-clear">
      <div class="home-container">
        <div class="content-wrapper">
          <app-main />
          <app-sidebar />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { AppHeader, AppMain, AppSidebar } from "./components";

export default {
  name: "Layout",
  components: {
    AppHeader,
    AppSidebar,
    AppMain
  },
  data() {
    return {
      mainWidth: 12,
      mainOffset: 3,
      sideClass: ""
    };
  },
  mounted() {
    this.resize();
    window.onresize = () => {
      return (() => {
        this.resize();
      })();
    };
  },
  methods: {
    resize() {
      if (document.body.clientWidth < 1000) {
        this.mainWidth = 24;
        this.mainOffset = 0;
        this.sideClass = "sidebar-hide";
      } else {
        this.mainWidth = 13;
        this.mainOffset = 3;
        this.sideClass = "";
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "~@/styles/variables.scss";
.page-wrapper {
  background-color: #f0f2f5;
  .home-wrapper {
    position: relative;
    height: 100%;
    width: 100%;
    font-family: Helvetica Neue, Helvetica, PingFang SC, Hiragino Sans GB,
      Microsoft YaHei, SimSun, sans-serif;
    font-size: 16px;
    color: #001528;
    .home-container {
      margin-top: 16px;
      min-height: calc(100vh - 78px);
      position: relative;
      width: 100%;
      .content-wrapper {
        width: 1160px;
        margin: 0 auto;
        position: relative;
      }
    }
  }
  .f-clear:after {
    content: "";
    display: block;
    visibility: hidden;
    clear: both;
  }
  .sidebar-hide {
    display: none;
  }
}

</style>

<style lang="scss">
@import "~@/styles/mini-size.scss";
</style>
