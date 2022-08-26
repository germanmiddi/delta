<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddressesDropColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->dropColumn('zipcode');
            $table->dropColumn('street');
            $table->dropColumn('strnum');
            $table->dropColumn('floor');
            $table->dropColumn('appartment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
            $table->dropColumn('zipcode');
            $table->dropColumn('street');
            $table->dropColumn('strnum');
            $table->dropColumn('floor');
            $table->dropColumn('appartment');
        });
    }
}
