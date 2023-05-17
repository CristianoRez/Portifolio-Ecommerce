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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('price');
            $table->bigInteger('promotion_price')->nullable();
            $table->string('description');
            $table->float('height');
            $table->float('width');
            $table->float('lenght');
            $table->float('weight');
            $table->string('photo')->nullable();
            $table->bigInteger('stock');
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
        Schema::dropIfExists('products');
    }
};
