<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('node_id')->unsigned();
            $table->bigInteger('item_id')->unsigned();
            $table->uuid('uuid');
            $table->integer('count')->unsigned();
            $table->integer('amount')->unsigned();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('node_id')->references('id')->on('nodes');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('node_items');
    }
}
