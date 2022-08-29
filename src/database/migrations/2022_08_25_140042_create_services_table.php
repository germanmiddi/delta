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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->bigInteger('order_id')->unsigned()->nullable(); 
            $table->bigInteger('status_id')->unsigned()->nullable(); 
            $table->bigInteger('driver_id')->unsigned()->nullable(); 
            $table->bigInteger('type_id')->unsigned()->nullable(); 
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');

            $table->foreign('status_id')->references('id')->on('services_status')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');
            
            $table->foreign('driver_id')->references('id')->on('drivers')
                   ->onDelete('cascade')
                   ->onUpdate('cascade');

            $table->foreign('type_id')->references('id')->on('services_type')
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
        Schema::dropIfExists('services');
    }
};
