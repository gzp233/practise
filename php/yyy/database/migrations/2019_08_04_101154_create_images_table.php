<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_directories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->string('directory_name', 50)->unique();
            $table->string('desc')->default('');
            $table->tinyInteger('is_forbidden');
            $table->index('user_id');
            $table->timestamps();
        });

        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('directory_id');
            $table->string('image');
            $table->index(['user_id', 'directory_id']);
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
        Schema::dropIfExists('image_directories');
        Schema::dropIfExists('images');
    }
}
