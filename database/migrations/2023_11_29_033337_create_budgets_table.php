<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('observation')->nullable();
            $table->decimal('total', 9, 2);
            $table->decimal('tax_percentage', 9, 2)->nullable();
            $table->decimal('shipping_charge', 9, 2)->nullable();
            $table->decimal('tax_amount', 9, 2)->nullable();
            $table->decimal('payable_amount', 9, 2);
            $table->integer('created_by');
            $table->boolean('status')->default(1)->comment('1:Active,0:Inactive');
            $table->string('ip_address')->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('budgets');
    }
}
