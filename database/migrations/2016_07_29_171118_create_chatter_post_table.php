<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChatterPostTable extends Migration
{
    public function up()
    {
        Schema::create('chatter_post', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chatter_discussion_id')->unsigned();
            $table->foreignId('user_id')->unsigned();
            $table->text('body');
            $table->boolean('markdown')->default(0);
            $table->boolean('locked')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('chatter_post');
    }
}
