<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClmBookingPaymentThroughsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_payment_throughs', function (Blueprint $table) {
            if (Schema::hasColumn('booking_payment_throughs', 'card_brand')) {
            } else {
              $table->string('card_brand', 50)->after('amount')->nullable()->comment('if choose payment mode Credit Card.');
              $table->string('card_number', 50)->after('card_brand')->nullable();
            }
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
            //
        });
    }
}
