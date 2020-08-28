<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInBookingPaymentThroughsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_payment_throughs', function (Blueprint $table) {
            if (Schema::hasColumn('booking_payment_throughs', 'is_installment_complete'))
            { }
            else
            {
                $table->boolean('is_installment_complete')->after('paid_installment')->default('0');
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
