<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateOrderDetailsTable extends Migration
  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('order_details', function (Blueprint $table) {
        $table->unsignedInteger('order_id');
        $table->unsignedInteger('product_id')->nullable();
        $table->unsignedInteger('package_id')->nullable();
        $table->unsignedInteger('quantity');
        $table->enum('type', ['package', 'product']);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('order_details');
    }
  }
