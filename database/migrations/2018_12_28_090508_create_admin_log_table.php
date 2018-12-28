<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminLogTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('admin_log', function (Blueprint $table) {
      $table->increments('id');
      $table->integer("admin_id")->comment("管理员id");
      $table->string("operator")->comment("操作人");
      $table->string("method")->comment("操作方法");
      $table->string('ip');
      $table->string('url')->comment("访问地址");
      $table->text('details')->comment("详情");
      $table->tinyInteger('type')->default(1)->comment('类型 1日志 2错误 3警告');
      $table->timestamps();
      $table->index('url');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('admin_log');
  }
}
