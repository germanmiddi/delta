<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->date('fecha_retiro')->nullable();
            $table->time('hora_retiro')->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('driver_id')->unsigned()->nullable();
            $table->string('order_status')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('client_id')->references('id')->on('clients')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');

            $table->foreign('driver_id')->references('id')->on('drivers')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');

            $table->foreign('created_by')->references('id')->on('users')
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
        Schema::dropIfExists('orders');
    }
};
