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
			$table->bigInteger('uid');
			$table->string('title', 48);
			$table->string('keywords', 48);
			$table->string('description', 128);
			$table->text('content');
			$table->softDeletes()->nullable();
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
