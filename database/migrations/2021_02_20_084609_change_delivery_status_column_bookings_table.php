<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDeliveryStatusColumnBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            \DB::statement('ALTER TABLE `bookings` CHANGE `deliveryStatus` `deliveryStatus` ENUM("Process","Cancel","Delivered","Return") CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT "Process" COMMENT "Order Delivery Status"');
            //$table->enum('deliveryStatus', ['Process', 'Cancel', 'Delivered', 'Return'])->default('Process')->after('due_condition')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
}
