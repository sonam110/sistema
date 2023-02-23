<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClmToSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            $table->boolean('type')->nullable()->comment('1=pedido; 2=factura compra');
            $table->unsignedBigInteger('concept_id');
            $table->decimal('perc_iibb', 17, 2)->nullable();      
            $table->decimal('perc_iva', 17, 2)->nullable();      
            $table->decimal('perc_gan', 17, 2)->nullable() ;     
            $table->boolean('payment',10)->nullable()->comment('0=no pagado; 1=pagado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
           $table->dropColumn('type');
           $table->dropColumn('concept_id');
           $table->dropColumn('perc_iibb');
           $table->dropColumn('perc_iva');
           $table->dropColumn('perc_gan');
           $table->dropColumn('payment');
        });
    }
}
