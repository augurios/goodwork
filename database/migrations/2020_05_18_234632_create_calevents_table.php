<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaleventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calevents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('classes')->nullable();
            $table->boolean('recurent')->default(false);
            $table->dateTime('startDate');
            $table->dateTime('endDate')->nullable();
            $table->integer('posted_by')->unsigned();
            $table->foreign('posted_by')->references('id')->on('users');
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
        Schema::dropIfExists('calevents');
    }
}
