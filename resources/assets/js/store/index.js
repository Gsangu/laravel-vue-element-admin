import Vue from 'vue'
import Vuex from 'vuex'
import app from './modules/app'
import permission from './modules/permission'
import user from './modules/user'
import tagsView from './modules/tagsView'
import category from './modules/category'
import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    app,
    permission,
    user,
    tagsView,
    category
  },
  getters
})

export default store
