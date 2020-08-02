<template>
  <post-detail :is-edit='0'></post-detail>
</template>

<script>
import PostDetail from './components/PostDetail'

export default {
  name: 'CreateForm',
  components: { PostDetail }
}
</script>
