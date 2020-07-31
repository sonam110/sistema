<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->date('po_date');
            $table->string('po_no')->unique();
            $table->decimal('total_amount', 17, 2)->default('0.00');
            $table->decimal('tax_percentage', 17, 2)->default('0.00');
            $table->decimal('tax_amount', 17, 2)->default('0.00');
            $table->decimal('gross_amount', 17, 2)->default('0.00');
            $table->enum('po_status', ['Pending','Sent','Receiving','Completed'])->default('Pending');
            $table->date('po_completed_date')->nullable();
            $table->text('remark')->nullable();
            $table->string('is_read_token')->unique();
            $table->boolean('is_read_status')->default(false);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
