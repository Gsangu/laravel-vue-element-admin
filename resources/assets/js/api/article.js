import request from '@/utils/request'
import { parseTime } from '@/utils'
import Qs from 'qs'

export function fetchList(query) {
  return request({
    url: '/article/list/',
    method: 'get',
    params: query
  })
}

export function fetchArticle(id) {
  return request({
    url: '/article/detail/',
    method: 'get',
    params: { id }
  })
}

export function deleteArticle(id) {
  return request({
    url: '/article/delete/',
    method: 'post',
    data: { id }
  })
}

export function createArticle(article) {
  article.published_at = parseTime(article.published_at)
  return request({
    url: '/article/create/',
    method: 'post',
    data: Qs.stringify({ 'article': article }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function updateArticle(article) {
  return request({
    url: '/article/update/',
    method: 'post',
    data: Qs.stringify({ 'article': article }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function batchUpdateArticle(articles, category, status, user) {
  return request({
    url: '/article/batch-update/',
    method: 'post',
    data: Qs.stringify({ 'articles': articles, 'category': category, 'status': status, 'user': user }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}

export function checkSlug(slug) {
  return request({
    url: '/article/check-slug/',
    method: 'post',
    data: { slug }
  })
}
