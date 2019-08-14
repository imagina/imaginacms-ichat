<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIchatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ichat__messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('message');
            $table->string('attached')->nullable();
            $table->integer('sender_id')->unsigned()->nullable();
            $table->foreign('sender_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
            $table->integer('receiver_id')->unsigned()->nullable();
            $table->foreign('receiver_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
            $table->boolean('read')->default(false);
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
        Schema::dropIfExists('ichat__messages');
    }
}
