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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('fullname')->nullable();
            $table->integer('dni')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();            
            $table->string('cellphone')->nullable();            
            $table->string('client_type')->nullable();            
            $table->bigInteger('company_id')->unsigned()->nullable();            
            $table->float('price')->nullable();            
            $table->text('notes')->nullable();            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('company_id')->references('id')->on('companies')
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
        Schema::dropIfExists('clients');
    }
};
