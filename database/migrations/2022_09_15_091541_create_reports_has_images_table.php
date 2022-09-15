<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsHasImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_has_images', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('report_id')->unsigned();
            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('src');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports_has_images');
    }
}
