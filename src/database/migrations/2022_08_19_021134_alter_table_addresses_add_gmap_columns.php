<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAddressesAddGmapColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('google_address', 100)->nullable()->after('notes');
            $table->string('google_area1', 50)->nullable()->after('google_address');
            $table->string('google_postal_code', 10)->nullable()->after('google_area1');
            $table->double('google_latitude')->default(0)->after('google_postal_code');
            $table->double('google_longitude')->default(0)->after('google_latitude');
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
            $table->dropColumn('google_address');
            $table->dropColumn('google_area1');
            $table->dropColumn('google_postal_code');
            $table->dropColumn('google_latitude');
            $table->dropColumn('google_longitude');

        });
    }
}
