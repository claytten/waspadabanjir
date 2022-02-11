<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('date');
            $table->dropColumn('time');
            $table->dropColumn('detail_location');
            $table->string('date_out')->nullable()->after('injured');
            $table->string('date_in')->after('injured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropColumn('date_in');
            $table->dropColumn('date_out');
            $table->string('name');
            $table->string('date');
            $table->string('time');
            $table->string('detail_location');
        });
    }
}
