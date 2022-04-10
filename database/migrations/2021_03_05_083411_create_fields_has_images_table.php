<?php

use App\Models\Maps\Fields\Field;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsHasImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields_has_images', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('field_id')->unsigned();
            $table->foreign('field_id')
                ->references('id')
                ->on('fields')
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
        Schema::dropIfExists('fields_has_images');
    }
}
