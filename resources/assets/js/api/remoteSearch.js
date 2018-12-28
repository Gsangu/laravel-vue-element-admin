import request from '@/utils/request'

export function userSearch(name) {
  return request({
    url: '/user/search/',
    method: 'post',
    data: { name }
  })
}
