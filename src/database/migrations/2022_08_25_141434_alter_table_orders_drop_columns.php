<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrdersDropColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('hora_inicio');
            $table->dropColumn('fecha_retiro');
            $table->dropColumn('hora_retiro');
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
            $table->dropColumn('order_status');
                     
            $table->float('unit_price')->nullable()->after('client_id');
            $table->float('total_price')->nullable()->after('unit_price');
            $table->bigInteger('status_id')->unsigned()->nullable()->after('total_price'); 

            $table->foreign('status_id')->references('id')->on('orders_status')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('hora_inicio');
            $table->dropColumn('fecha_retiro');
            $table->dropColumn('hora_retiro');
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
            $table->dropColumn('order_status');
                     
            $table->float('unit_price')->nullable()->after('client_id');
            $table->float('total_price')->nullable()->after('unit_price');
            $table->bigInteger('status_id')->unsigned()->nullable()->after('total_price'); 

            $table->foreign('status_id')->references('id')->on('orders_status')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');

        });
    }
}
