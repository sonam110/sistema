<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingInstallmentPaidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_installment_paids', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booking_id');
            $table->unsignedInteger('created_by');
            $table->decimal('amount',17,2)->comment('paid amount');

            $table->index('booking_id');
            $table->index('created_by');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('booking_installment_paids');
    }
}
