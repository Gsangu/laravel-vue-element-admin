import request from '@/utils/request'
import Qs from 'qs'

export function fetchAll() {
  return request({
    url: '/category/all/',
    method: 'get'
  })
}

export function fetchList(query) {
  return request({
    url: '/category/list/',
    method: 'get',
    params: query
  })
}

export function deleteCategory(id) {
  return request({
    url: '/category/delete/',
    method: 'post',
    data: { id }
  })
}

export function fetchCategory(id) {
  return request({
    url: '/category/detail/',
    method: 'get',
    params: { id }
  })
}

export function createCategory(category) {
  return request({
    url: '/category/create/',
    method: 'post',
    data: Qs.stringify({ 'category': category }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function updateCategory(category) {
  return request({
    url: '/category/update/',
    method: 'post',
    data: Qs.stringify({ 'category': category }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function checkSlug(slug) {
  return request({
    url: '/category/check-slug/',
    method: 'post',
    data: { slug }
  })
}
