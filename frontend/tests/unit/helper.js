import { createLocalVue } from '@vue/test-utils'
import Vuex from 'vuex'
import Vuesax from 'vuesax'
import { loader } from "@/store/loader.module";
import VueRouter from 'vue-router'

export const getLocalVue = () => {
    const localVue = createLocalVue()
    // localVue.use(VueRouter)
    localVue.use(Vuex)
    localVue.use(Vuesax)
    return localVue
}

export const getStore = () => {
    return new Vuex.Store({
        modules: {
            loader
        }
    })
}
