const getters = {
  sidebar: state => state.app.sidebar,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  device: state => state.app.device,
  token: state => state.user.token,
  userid: state => state.user.userid,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  email: state => state.user.email,
  roles: state => state.user.roles,
  permission_routers: state => state.permission.routers,
  addRouters: state => state.permission.addRouters,
  language: state => state.app.language,
  size: state => state.app.size,
  categories: state => state.category.list,
  users: state => state.user.list,
  siteUrl: state => 'http://' + window.location.host + '/'
}
export default getters
