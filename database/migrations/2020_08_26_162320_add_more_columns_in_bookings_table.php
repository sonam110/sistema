<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnsInBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
          if (Schema::hasColumn('bookings', 'tax_percentage')) {
          } else {
              $table->decimal('tax_percentage', 5,2)->after('interestAmount')->default('0.00');
              $table->decimal('tax_amount', 15,2)->after('tax_percentage')->default('0.00');
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
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
}
