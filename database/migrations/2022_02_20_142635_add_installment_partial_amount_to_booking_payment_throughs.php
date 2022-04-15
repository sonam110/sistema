<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentPartialAmountToBookingPaymentThroughs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_payment_throughs', function (Blueprint $table) {
          $table->decimal('installment_partial_amount', 17, 2)      //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_payment_throughs', function (Blueprint $table) {
          $table->dropColumn('installment_partial_amount');
            //
        });
    }
}
