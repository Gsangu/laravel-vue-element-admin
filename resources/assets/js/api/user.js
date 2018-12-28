import request from '@/utils/request'
import Qs from 'qs'

export function fetchList(query) {
  return request({
    url: '/user/list/',
    method: 'get',
    params: query
  })
}

export function fetchAll() {
  return request({
    url: '/user/all/',
    method: 'get'
  })
}

export function fetchUser(id) {
  return request({
    url: '/user/detail/',
    method: 'get',
    params: { id }
  })
}

export function checkEmail(email) {
  return request({
    url: '/user/check-email/',
    method: 'get',
    params: { email }
  })
}

export function deleteUser(id) {
  return request({
    url: '/user/delete/',
    method: 'post',
    data: { id }
  })
}

export function createUser(user) {
  return request({
    url: '/user/create/',
    method: 'post',
    data: Qs.stringify({ 'user': user }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function updateUser(user) {
  return request({
    url: '/user/update/',
    method: 'post',
    data: Qs.stringify({ 'user': user }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}
