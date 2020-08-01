<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesOrderReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_order_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booking_id');
            $table->unsignedInteger('bookeditem_id');
            $table->unsignedInteger('producto_id');
            $table->string('return_token', 50);
            $table->decimal('return_qty',9,2);
            $table->text('return_note')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('bookeditem_id')->references('id')->on('bookeditems')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales_order_returns');
    }
}
