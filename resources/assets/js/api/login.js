import request from '@/utils/request'

export function login(email, password) {
  return request({
    url: '/user/login/',
    method: 'post',
    data: {
      email,
      password
    }
  })
}

export function getInfo() {
  return request({
    url: '/user/info/',
    method: 'post'
  })
}

export function logout() {
  return request({
    url: '/user/logout/',
    method: 'post'
  })
}
