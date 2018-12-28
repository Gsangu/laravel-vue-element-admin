import request from '@/utils/request'
import Qs from 'qs'

export function fetchList(query) {
  return request({
    url: '/field/list/',
    method: 'get',
    params: query
  })
}

export function deleteField(id) {
  return request({
    url: '/field/delete/',
    method: 'post',
    data: { id }
  })
}

export function checkIndex(index) {
  return request({
    url: '/field/check-index/',
    method: 'get',
    params: { index }
  })
}

export function createField(field) {
  return request({
    url: '/field/create/',
    method: 'post',
    data: Qs.stringify({ 'field': field }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function updateField(field) {
  return request({
    url: '/field/update/',
    method: 'post',
    data: Qs.stringify({ 'field': field }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}
