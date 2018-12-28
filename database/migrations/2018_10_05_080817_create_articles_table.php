<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id')->default(1)->comment("分类id");
            $table->unsignedInteger('user_id')->default(1)->comment("用户id");
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->string('content_short')->default('')->comment('描述');
            $table->string('slug')->comment("短链接");
            $table->string('image_uri')->default('')->comment("文章图片");
            $table->boolean('status')->default(false)->comment("文章状态：0，draft；1，published");
            $table->timestamp('published_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment("发布时间");
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
        Schema::dropIfExists('articles');
    }
}
