<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsDetailLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields_detail_locations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('field_id')->unsigned();
            $table->string('district');
            $table->string('village');

            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields_detail_locations');
    }
}
