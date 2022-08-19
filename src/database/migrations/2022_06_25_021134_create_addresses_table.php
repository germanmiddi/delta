<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            
            $table->id();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->bigInteger('city_id')->unsigned()->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->string('street')->nullable();
            $table->Integer('strnum')->nullable();
            $table->string('floor')->nullable();
            $table->string('appartment', 10)->nullable();
            $table->string('notes')->nullable();
            $table->string('google_address', 100)->nullable();
            $table->string('google_area1', 50)->nullable();
            $table->string('google_postal_code', 10)->nullable();
            $table->double('google_latitude')->default(0);
            $table->double('google_longitude')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('city_id')->references('id')->on('cities');



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
