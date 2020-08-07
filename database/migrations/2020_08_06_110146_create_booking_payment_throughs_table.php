<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingPaymentThroughsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_payment_throughs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booking_id');
            $table->string('payment_mode')->comment('Credit Card, Debit Card, Cash, Cheque, Installment');
            $table->decimal('amount',17,2);

            $table->integer('no_of_installment')->nullable()->comment('if choose payment mode Installment.');
            $table->decimal('installment_amount', 17,2)->nullable()->comment('installment amount is amount/no_of_installment');
            $table->integer('paid_installment')->default(0)->comment('Nunmber of installment paid');

            $table->string('cheque_number')->nullable()->comment('if choose payment mode Cheque.');
            $table->string('bank_detail')->nullable()->comment('if choose payment mode Cheque.');

            $table->index('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
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
        Schema::dropIfExists('booking_payment_throughs');
    }
}
