<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content')->comment('html string');
            $table->text('raw_content')->comment('content in delta format');
            $table->integer('posted_by')->unsigned();
            $table->boolean('archived')->default(false);
            $table->boolean('public')->default(false);
            $table->boolean('draft')->default(true);
            $table->integer('office_id')->unsigned()->nullable();
            $table->string('discussionable_type')->comment('office, team or projects');
            $table->integer('discussionable_id')->unsigned();
            $table->integer('subject_id')->unsigned();
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
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
        Schema::dropIfExists('tickets');
    }
}
