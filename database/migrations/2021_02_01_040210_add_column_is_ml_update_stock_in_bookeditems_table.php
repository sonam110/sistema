<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsMlUpdateStockInBookeditemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookeditems', function (Blueprint $table) {
            if (Schema::hasColumn('bookeditems', 'is_stock_updated_in_ml')) {
            } else {
                $table->boolean('is_stock_updated_in_ml')->default('0')->nullable()->after('postcode');
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
            $table->dropColumn('is_stock_updated_in_ml');
        });
    }
}
