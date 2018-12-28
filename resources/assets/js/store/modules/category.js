import { fetchAll } from '@/api/category'

const category = {
  state: {
    list: []
  },

  mutations: {
    SET_LIST: (state, list) => {
      state.list = list
    }
  },

  actions: {
    // 获取栏目列表
    GetCategoryList({ commit }) {
      return new Promise((resolve, reject) => {
        fetchAll().then(response => {
          const data = response.data
          if (data.err) {
            reject('获取分类列表失败，请重试！')
          } else {
            commit('SET_LIST', data.items)
          }
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
}

export default category
