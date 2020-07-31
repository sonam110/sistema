<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table)
        {
            if (Schema::hasColumn('users', 'userType'))
            {
                $table->dropColumn('userType');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'status')) {
            } else {
                $table->enum('status', [0, 1, 2])->default('0')->comment('0:Active, 1:Inactive, 2:Delete')->after('phone');
            }

            if (Schema::hasColumn('users', 'userType')) {
            } else {
                $table->enum('userType', [0, 1, 2])->default('1')->comment('0:Admin, 1:Customer, 2:Employee')->after('id');
            }

            if (Schema::hasColumn('users', 'locktimeout')) {
            } else {
                $table->string('locktimeout')->after('status')->default('30')->comment('System auto logout if no activity found.');
            }

            if (Schema::hasColumn('users', 'doc_type')) {
            } else {
                $table->enum('doc_type',['DNI', 'CUIT', 'PASSPORT'])->after('status')->nullable();
                $table->string('doc_number')->after('doc_type')->nullable();
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
        Schema::table('users', function (Blueprint $table) {

        });
    }
}
