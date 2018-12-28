
## 前端
文件目录位置：项目目录/resources/assets/
### 文件结构
```
├── api  后端接口目录
├── components  公共组件库
├── filters 过滤器
├── images  图片资源
├── lang  多语言支持
├── libs  方法扩展库      
├── router  路由配置目录
├── store  vuex 配置
├── styles  样式库
├── utils  请求和公共方法封装
└── views  前端视图界面
```
#后端
- 认真阅读[Laravel5.5 开发规范](https://laravel-china.org/docs/laravel-specification/5.5)
- 默认登录账号密码 admin@admin.com 123456 <br>
- 公共函数写在 app/Helpers/helpers.php里面
## 后端
### 生成 Swigger-ui 接口文档
- 运行php artisan vendor:publish（这一步的意义是将包的内容发布）。
- 编写接口注释。
- 使用php artisan l5-swagger:generate生成文档
- 访问你的项目域名+/api/documentation
### 常用命令
```
#创建控制器
php artisan make:controller Controller

#创建模型
php artisan make:model Model

#新建表
php artisan make:migration create_name_table

创建中间件（app/Http/Middleware 下）
php artisan make:middleware Activity

创建队列（数据库）的表迁移（需要执行迁移才生效）
php artisan queue:table

#创建队列类（app/jobs下）：
php artisan make:job SendEmail

#创建请求类（app/Http/Requests下）
php artisan make:request CreateArticleRequest

回滚上一次的迁移
php artisan migrate:rollback

回滚所有迁移
php artisan migrate:reset
```

## 常用的第三方服务包
**权限管理**
 spatie/laravel-permission 

**图形验证码**

`composer require mews/captcha`

`Mews\Captcha\CaptchaServiceProvider::class,`

`'Captcha' => Mews\Captcha\Facades\Captcha::class,`

`php artisan vendor:publish`  #生成config/captcha.php

**图片处理扩展包**

`composer require intervention/image`

`Intervention\Image\ImageServiceProvider::class,`

`'Image' => Intervention\Image\Facades\Image::class,`

`php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravel5"`  #生成config/image.php

// 修改指定图片的大小

`$img = Image::make('images/avatar.jpg')->resize(200, 200);`

// 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素

`$img->insert('images/watermark.png', 'bottom-right', 15, 10);`

// 将处理后的图片重新保存到其他路径

`$img->save('images/new_avatar.jpg');`

// 上面的逻辑可以通过链式表达式搞定

`$img = Image::make('images/avatar.jpg')->resize(200, 200)->insert('images/new_avatar.jpg', 'bottom-right', 15, 10);`

**excel服务**

`composer require "maatwebsite/excel:~2.1.0"`

`Maatwebsite\Excel\ExcelServiceProvider::class,`

`'Excel' => Maatwebsite\Excel\Facades\Excel::class,`

`php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"`  #生成config/excel.php

**pdf 服务**

`composer require barryvdh/laravel-dompdf`

`Barryvdh\DomPDF\ServiceProvider::class,`

`'PDF' => Barryvdh\DomPDF\Facade::class,`

`php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"`  #生成config/dompdf.php

**html过滤包**

`composer require mews/purifier`

`Mews\Purifier\PurifierServiceProvider::class,`

`'Purifier' => Mews\Purifier\Facades\Purifier::class,`

`php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"`  #生成config/purifier.php

`clean(Input::get('inputname'));`

`Purifier::clean(Input::get('inputname'));`

**浏览器跨域**

`composer require barryvdh/laravel-cors`

`Barryvdh\Cors\ServiceProvider::class,`

`php artisan vendor:publish --provider="Barryvdh\Cors\ServiceProvider"`  #生成config/cors.php

**根据ip获取地址位置**

`composer require "zhuzhichao/ip-location-zh"`

`'Ip'  => 'Zhuzhichao\IpLocationZh\Ip::class,`

`Ip::find('171.12.10.156')` or `Ip::find(Request::getClientIp())`

## laravel队列

配置文件`config/queue.php`, 可选驱动`sync/database/sqs/redis`等

若使用`database`驱动, 需要执行 `php artisan queue:table` 和 `php aritsan migrate` 以创建表.

若使用`redis`驱动, 需要配置`config/databse.php`里的`redis`项; 需要安装依赖`predis/predis`.

执行 `php artisan make:job SendReminderEmail` 将在 `app/Jobs` 目录下生成 `SendReminderEmail.php`