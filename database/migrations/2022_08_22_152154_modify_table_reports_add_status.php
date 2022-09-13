<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTableReportsAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('reports', function (Blueprint $table) {
        $table->enum('status', ['accept', 'decline', 'process'])->default('process')->after('message');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('reports', function (Blueprint $table) {
        $table->dropColumn('status');
      });
    }
}
