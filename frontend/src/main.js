import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import Vuesax from 'vuesax'
import 'vuesax/dist/vuesax.css'
import VueRouter from "vue-router"

Vue.config.productionTip = false

Vue.use(VueRouter)
Vue.use(Vuesax, {
    // options here
})

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
