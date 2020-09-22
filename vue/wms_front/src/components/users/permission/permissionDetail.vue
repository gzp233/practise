<template>
	<div class="customer-detail">
		<div class="detail-item">
			<span class="item-title">权限ID:</span>
			<div class="item-content">{{detail.id}}</div>
		</div>
		<div class="detail-item">
			<span class="item-title">权限名称:</span>
			<div class="item-content">{{detail.permission_name}}</div>
		</div>
    <div class="detail-item">
			<span class="item-title">权限标识:</span>
			<div class="item-content">{{detail.permission_code}}</div>
		</div>
		<div class="detail-item">
			<span class="item-title">权限描述:</span>
			<div class="item-content">{{detail.desc}}</div>
		</div>
		<div class="detail-item">
			<span class="item-title">创建日期:</span>
			<div class="item-content">{{detail.created_at}}</div>
		</div>
	</div>
</template>

<script type="text/javascript">
export default {
  name: 'customerdetail',
  props: ['detail']
}
</script>

<style lang="less" scoped>
.customer-detail {
  height: auto;
  overflow: hidden;
  .detail-item {
    height: auto;
    overflow: hidden;
    line-height: 25px;
    padding-bottom: 15px;
    .item-title {
      display: inline-block;
      width: 100px;
      font-size: 14px;
      float: left;
      color: #333;
      text-align: right;
    }
    .item-content {
      margin-left: 110px;
      color: #999;
    }
  }
}
</style>