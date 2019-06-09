<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeEntiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_enties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('requirement_id');
            $table->unsignedInteger('hourly_rate_id');
            $table->text('description');
            $table->double('time', 10, 2);
            $table->decimal('inr', 20, 2)->nullable();
            $table->timestamps();

            $table->foreign('requirement_id')
                ->references('id')
                ->on('requirements');
            $table->foreign('hourly_rate_id')
                ->references('id')
                ->on('hourly_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_enties');
    }
}
