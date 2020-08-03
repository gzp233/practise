
import store from '@/store'

export default {
  inserted(el, binding, vnode) {
    const { value } = binding
    const permissions = store.getters && store.getters.permissions
    if (value) {
      if (permissions.indexOf(value) < 0) {
        el.parentNode && el.parentNode.removeChild(el)
      }
    } else {
      throw new Error(`need permissions! Like v-permission="'4B86E52A58C73EB692F993D7E0884E00'"`)
    }
  }
}
