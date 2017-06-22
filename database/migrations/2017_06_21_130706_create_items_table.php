<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('name')->nullable();
            $table->string('seller_id', 64)->nullable();
            $table->string('store_id', 64)->nullable();
            $table->integer('sold')->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('watching')->default(0);
            $table->integer('sales_recent')->default(0);
            $table->integer('sales_total')->default(0);
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
        Schema::dropIfExists('items');
    }
}