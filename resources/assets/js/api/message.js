import request from '@/utils/request'
import Qs from 'qs'

export function fetchList(query) {
  return request({
    url: '/message/list/',
    method: 'get',
    params: query
  })
}

export function deleteMessage(id) {
  return request({
    url: '/message/delete/',
    method: 'post',
    data: { id }
  })
}

export function batchUpdateMessage(messages, status) {
  return request({
    url: '/message/batch-update/',
    method: 'post',
    data: Qs.stringify({ 'messages': messages, 'status': status }),
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
    }
  })
}
