<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->date('invoice_date');
            $table->string('invoice_no')->unique();
            $table->decimal('total_amount', 17, 2)->default('0.00');
            $table->decimal('tax_percentage', 17, 2)->default('0.00');
            $table->decimal('tax_amount', 17, 2)->default('0.00');
            $table->decimal('gross_amount', 17, 2)->default('0.00');
            $table->decimal('convention', 17, 2)->default('0.00');
            $table->decimal('profit_advance', 17, 2)->default('0.00');
            $table->boolean('status')->default('1')->comment('1:Paid,0:Unpaid');
            $table->text('remark')->nullable();
            $table->index('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_invoices');
    }
}
