<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('category_name', 50)->unique();
            $table->string('desc')->default('');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('title');
            $table->mediumText('content');
            $table->boolean('is_published');
            $table->dateTime('published_at')->nullable();
            $table->index(['user_id', 'category_id']);
            $table->timestamps();
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tag_name', 50)->unique();
            $table->timestamps();
        });

        Schema::create('post_tag_relation', function (Blueprint $table) {
            $table->integer('post_id');
            $table->integer('tag_id');
        });

        Schema::create('post_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_id')->default(0);
            $table->integer('user_id');
            $table->integer('post_id');
            $table->string('content');
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
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_categories');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_tag_relation');
    }
}
