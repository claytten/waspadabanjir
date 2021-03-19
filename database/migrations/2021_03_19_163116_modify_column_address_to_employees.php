<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnAddressToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('address', 'address_id');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->string('address_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('address_id', 'address');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('address');
        });
    }
}
