前后端分离后台管理系统，使用Vue Element admin + Laravel 5.5构建。带swagger自动文档生成。
接口基于dingo/api和tymon/jwt，权限认证带后端Token认证和前端vue.js的动态权限

---
## 开发步骤
### 配置
```bash
# 安装
composer install

# 复制配置文件
cp .env.example .env

# 生成加密key
php artisan key:generate

# 生成jwt加密key
php artisan jwt:secret

# 配置env中的数据库链接
配置数据库名称、用户名和密码

# 数据库迁移和填充
php artisan migrate
php artisan db:seed

# 启动 (或者用普通方式启动laravel项目)
php artisan serve

yarn (或 npm install)
yarn run dev (或 npm run dev)
```
## 后端
### 技术栈
- Laravel 5.5
- L5-Swagger
- ...
## 前端
### 技术栈
- Vue全家桶
- [Vue-element-admin](https://panjiachen.github.io/vue-element-admin-site/zh/)
- Axios
- Laravel Mix
- ...
