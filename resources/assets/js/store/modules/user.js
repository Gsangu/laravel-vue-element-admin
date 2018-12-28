import { login, logout, getInfo } from '@/api/login'
import { fetchAll } from '@/api/user'
import { getToken, setToken, removeToken } from '@/utils/auth'

const user = {
  state: {
    userid: null,
    token: getToken(),
    email: '',
    name: '',
    avatar: '',
    roles: [],
    defaultPass: '',
    list: []
  },

  mutations: {
    SET_USERID: (state, id) => {
      state.userid = id
    },
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_EMAIL: (state, email) => {
      state.email = email
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_AVATAR: (state, avatar) => {
      state.avatar = avatar
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    },
    SET_USERLIST: (state, list) => {
      state.list = list
    },
    SET_DEFAULTPASS: (state, defaultPass) => {
      state.defaultPass = defaultPass
    }
  },

  actions: {
    // 获取用户列表
    GetUserList({ commit }) {
      return new Promise((resolve, reject) => {
        fetchAll().then(response => {
          const data = response.data
          if (data.err) {
            reject('获取用户列表失败，请重试！')
          } else {
            commit('SET_USERLIST', data.items)
          }
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },
    // 登录
    Login({ commit }, userInfo) {
      const email = userInfo.email.trim()
      return new Promise((resolve, reject) => {
        login(email, userInfo.password).then(response => {
          const data = response.data
          setToken(data.token)
          commit('SET_TOKEN', data.token)
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 刷新token
    RefreshToken({ commit }, token) {
      return new Promise(resolve => {
        setToken(token)
        commit('SET_TOKEN', token)
        resolve()
      })
    },

    // 获取用户信息
    GetUserInfo({ commit }) {
      return new Promise((resolve, reject) => {
        getInfo().then(response => {
          const data = response.data
          if (data.roles && data.roles.data.length > 0) { // 验证返回的roles是否是一个非空数组
            commit('SET_ROLES', data.roles.data)
          } else {
            reject('getInfo: roles must be a non-null array !')
          }
          commit('SET_USERID', data.id)
          commit('SET_NAME', data.name)
          commit('SET_EMAIL', data.email)
          commit('SET_AVATAR', data.avatar)
          commit('SET_DEFAULTPASS', data.default_pass)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 登出
    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          commit('SET_TOKEN', '')
          commit('SET_ROLES', [])
          removeToken()
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },

    // 前端 登出
    FedLogOut({ commit }) {
      return new Promise(resolve => {
        commit('SET_TOKEN', '')
        removeToken()
        resolve()
      })
    }
  }
}

export default user
