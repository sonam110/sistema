<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInBookeditemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookeditems', function (Blueprint $table) {
            if (Schema::hasColumn('bookeditems', 'return_qty')) {
            } else {
                $table->decimal('return_qty', 15,2)->after('itemqty')->default('0.00');
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
        Schema::table('bookeditems', function (Blueprint $table) {
            //
        });
    }
}
