<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRollbackStatusToTransferGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_groups', function (Blueprint $table) {
            $table->boolean('rollback_status')->after('message_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_groups', function (Blueprint $table) {
            $table->dropColumn('rollback_status');
        });
    }
}
