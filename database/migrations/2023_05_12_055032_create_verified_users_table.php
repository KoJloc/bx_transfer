<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifiedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verified_users', function (Blueprint $table) {
            $table->id();
            $table->integer('bx_id');
            $table->text('full_name')->nullable();
            $table->text('image')->nullable();
            $table->text('job')->nullable();
            $table->boolean('active');
            $table->boolean('verified')->default(0);
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
        Schema::dropIfExists('verified_users');
    }
}
