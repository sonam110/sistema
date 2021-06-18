<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Facturaafip extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('bookings', function (Blueprint $table) {
         $table->string('cae_nro',20);
         $table->string('cae_vto',20);
         $table->string('cae_fac',20);
         $table->string('cae_type',1);
        });

        Schema::table('sales_order_returns', function (Blueprint $table) {
         $table->string('cae_nro',20);
         $table->string('cae_vto',20);
         $table->string('cae_fac',20);
         $table->string('cae_type',1);
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
        $table->dropColumn('cae_nro',20);
        $table->dropColumn('cae_vto',20);
        $table->dropColumn('cae_fac',20);
        $table->dropColumn('cae_type',1);
      });
      Schema::table('sales_order_returns', function (Blueprint $table) {
       $table->dropColumn('cae_nro',20);
       $table->dropColumn('cae_vto',20);
       $table->dropColumn('cae_fac',20);
       $table->dropColumn('cae_type',1);
      });

    }
}
