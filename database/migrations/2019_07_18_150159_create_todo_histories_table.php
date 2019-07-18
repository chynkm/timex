<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTodoHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('todo_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('requirement_id');
            $table->string('task', 1000);
            $table->dateTime('deadline')->nullable();
            $table->dateTime('completed')->nullable();
            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->foreign('todo_id')
                ->references('id')
                ->on('todos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('todo_histories');
    }
}
