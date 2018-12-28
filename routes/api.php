<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
  'namespace' => 'App\Http\Controllers',
  'middleware' => 'serializer:array'
], function ($api) {
  $api->group([
    'middleware' => 'api.throttle', // 启用节流限制
    'limit' => 1000, // 允许次数
    'expires' => 1, // 分钟
  ], function ($api) {
    $api->group(['prefix' => 'user'], function ($api) {
      $api->post('login', 'UserController@login');
      $api->post('logout', 'UserController@logout');
    });

    $api->group(['middleware' => ['refresh.token', 'role:admin'], 'prefix' => 'user'], function ($api) {
      $api->post('info', 'UserController@info');
      $api->post('search', 'UserController@search');
      $api->get('list', 'UserController@getList');
      $api->get('detail', 'UserController@getUser');
      $api->post('update', 'UserController@updateUser');
      $api->post('create', 'UserController@createUser');
      $api->get('check-email', 'UserController@checkEmail');
      $api->post('delete', 'UserController@deleteUser');
      $api->get('all', 'UserController@userAll');
    });

    $api->group(['middleware' => 'refresh.token', 'prefix' => 'message'], function ($api) {
      $api->get('list', 'MessageController@getList');
      $api->post('delete', 'MessageController@deleteMessage');
      $api->post('batch-update', 'MessageController@batchUpdateMessage');
    });

    $api->group(['middleware' => 'refresh.token', 'prefix' => 'field'], function ($api) {
      $api->get('list', 'FieldController@getList');
      $api->post('delete', 'FieldController@deleteField');
      $api->post('update', 'FieldController@updateField');
      $api->get('check-index', 'FieldController@checkIndex');
      $api->post('create', 'FieldController@createField');
    });

    $api->group(['middleware' => ['refresh.token', 'role:admin'], 'prefix' => 'auth'], function ($api) {
      $api->post('uploadFile', 'UploadController@uploadfile');
    });


    $api->group(['middleware' => ['refresh.token', 'role:admin|editor'], 'prefix' => 'article'], function ($api) {
      $api->post('check-slug', 'ArticleController@checkSlugDuplication');
      $api->get('list', 'ArticleController@articlelist');
      $api->get('detail', 'ArticleController@getArticle');
      $api->post('create', 'ArticleController@createArticle');
      $api->post('update', 'ArticleController@updateArticle');
      $api->post('batch-update', 'ArticleController@batchUpdateArticle');
      $api->post('delete', 'ArticleController@deleteArticle');
    });

    $api->group(['middleware' => 'refresh.token', 'prefix' => 'category'], function ($api) {
      $api->post('check-slug', 'CategoryController@checkSlugDuplication');
      $api->get('list', 'CategoryController@categoryList');
      $api->get('all', 'CategoryController@categoryAll');
      $api->get('detail', 'CategoryController@getCategory');
      $api->post('create', 'CategoryController@createCategory');
      $api->post('update', 'CategoryController@updateCategory');
      $api->post('delete', 'CategoryController@deleteCategory');
    });
  });
});
