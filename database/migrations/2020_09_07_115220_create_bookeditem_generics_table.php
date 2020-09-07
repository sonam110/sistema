<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookeditemGenericsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookeditem_generics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->index('booking_id');
            $table->string('item_name');
            $table->decimal('itemqty', 15, 2)->default('0.00')->nullable();
            $table->decimal('return_qty', 15, 2)->default('0.00')->nullable();
            $table->decimal('itemPrice', 15, 2)->default('0.00')->nullable();
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
        Schema::dropIfExists('bookeditem_generics');
    }
}
