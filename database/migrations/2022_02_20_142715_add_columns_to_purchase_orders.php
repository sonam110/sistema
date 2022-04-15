<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPurchaseOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
      $table->unsignedInteger('type',11)->nullable()->comment('1=pedido; 2=factura compra');
      $table->unsignedBigInteger('concept_id',20);
      $table->decimal('perc_iibb', 17, 2)      //
      $table->decimal('perc_iva', 17, 2)      //
      $table->decimal('perc_gan', 17, 2)      //
      $table->unsignedInteger('payment',10)->nullable()->comment('0=no pagado; 1=pagado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
          $table->dropColumn('type');
          $table->dropColumn('concept_id');
          $table->dropColumn('perc_iibb');
          $table->dropColumn('perc_iva');
          $table->dropColumn('perc_gan');
          $table->dropColumn('payment');
            //
        });
    }
}
