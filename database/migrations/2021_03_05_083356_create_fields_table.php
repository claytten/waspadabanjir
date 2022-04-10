<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('color', 7);
            $table->text('area');
            $table->text('description')->nullable();
            $table->smallInteger('deaths');
            $table->smallInteger('losts');
            $table->smallInteger('injured');
            $table->string('date_in', 20);
            $table->string('date_out', 20)->nullable();
            $table->boolean('status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
