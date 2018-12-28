<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->json('content')->nullable()->comment('内容');
            $table->boolean('status')->default(false)->comment("状态");
            $table->unsignedInteger('ip')->nullable()->comment("IP");
            $table->string('sourceUrl')->nullable()->comment("来源网址");
            $table->string('system')->nullable()->comment("系统");
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
        Schema::dropIfExists('messages');
    }
}
