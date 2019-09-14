<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImpactComplexityToTodos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->enum('impact', ['high', 'medium', 'low'])
                ->default('medium')
                ->after('completed');
            $table->enum('complexity', ['hard', 'medium', 'easy'])
                ->default('medium')
                ->after('impact');
        });

        Schema::table('todo_histories', function (Blueprint $table) {
            $table->enum('impact', ['high', 'medium', 'low'])
                ->nullable()
                ->after('completed');
            $table->enum('complexity', ['hard', 'medium', 'easy'])
                ->nullable()
                ->after('impact');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn(['impact', 'complexity']);
        });

        Schema::table('todo_histories', function (Blueprint $table) {
            $table->dropColumn(['impact', 'complexity']);
        });
    }
}
