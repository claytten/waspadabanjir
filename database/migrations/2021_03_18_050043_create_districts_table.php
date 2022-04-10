<?php

use App\Models\Address\Regencies\Regency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->integer('regency_id')->unsigned();
            $table->foreign('regency_id')
                ->references('id')
                ->on('regencies')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name', 50);
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
        Schema::dropIfExists('districts');
    }
}
