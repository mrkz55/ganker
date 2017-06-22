<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellers', function (Blueprint $table) {
           $table->bigInteger('id')->unique();
           $table->string('name')->nullable();
           $table->integer('feedback')->default(0);
           $table->string('location')->nullable();
           $table->string('cover')->nullable();
           $table->string('avatar')->nullable();
           $table->string('slug')->nullable();
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
        Schema::dropIfExists('sellers');
    }
}