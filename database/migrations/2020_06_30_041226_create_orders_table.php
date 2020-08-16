<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateOrdersTable extends Migration
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
        $table->string('number', 10);
        $table->enum('state', ['processing', 'ready', 'delivering', 'delivered']);
        $table->unsignedInteger('client_id');
        $table->unsignedInteger('beneficiary_id');
        $table->unsignedInteger('taken_by')->nullable();
        $table->unsignedInteger('delivered_by')->nullable();
        $table->string("receipt")->nullable();
        $table->float('transport_fee');
        $table->float('service_fee');
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
      Schema::dropIfExists('orders');
    }
  }
