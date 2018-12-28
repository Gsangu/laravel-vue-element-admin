<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('default')->comment("昵称");
            $table->string('avatar')->default('uploads/avatar/default.gif')->comment("头像");
            $table->string('email')->unique()->comment("邮箱");
            $table->string('password')->comment("密码");
            $table->boolean('default_pass')->default(true)->comment("是否初始密码：true or false");
            $table->json('roles')->nullable()->comment('角色权限');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
