<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(){
		Schema::create('posts', function(Blueprint $table){
			$table->bigIncrements('id');
			$table->bigInteger('uid')->comment('所属用户');
			$table->bigInteger('category_id')->comment('所属分类');
			$table->string('title', 48)->comment('标题');
			$table->string('description', 128)->comment('描述');
			$table->tinyInteger('status', 4)->comment('状态');
			$table->integer('view_count', 11)->unsigned()->comment('浏览量');
			$table->integer('praise_count', 11)->unsigned()->comment('点赞量');
			$table->integer('comment_count', 11)->unsigned()->comment('评论量');
			$table->text('content')->comment('内容');
			$table->softDeletes()->nullable()->comment('删除时间');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down(){
		Schema::dropIfExists('posts');
	}
}
