<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->integer('entity_ID');
            $table->string('entity_type', 20);
            $table->integer('old_responsible_ID');
            $table->integer('new_responsible_ID');
            $table->bigInteger('transfer_group_ID')->unsigned();
                $table->foreign('transfer_group_ID')->references('id')->on('transfer_groups')->onDelete('cascade');
            $table->boolean('transfer_status')->default(false);
            $table->integer('rollbacks')->nullable();
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
        Schema::dropIfExists('histories');
    }
}
