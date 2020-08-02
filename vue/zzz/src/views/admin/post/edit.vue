<template>
  <post-detail :is-edit='1'></post-detail>
</template>

<script>
import PostDetail from './components/PostDetail'

export default {
  name: 'EditForm',
  components: { PostDetail }
}
</script>
