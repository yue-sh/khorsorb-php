<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->json('questions')->default(new Expression('(JSON_ARRAY())'));
            // $table->json('answers')->default(new Expression('(JSON_ARRAY())'));
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('choice1');
            $table->string('choice2');
            $table->boolean('answer');

            $table->string('examId');
        });

        Schema::create('submitGroup', function (Blueprint $table) {
            $table->id();
            $table->string('studentId');
            $table->string('studentName');
            $table->string('studentBranch');
        });

        Schema::create('examSubmit', function (Blueprint $table) {
            $table->id();
            $table->json('answers')->default(new Expression('(JSON_ARRAY())'));
            $table->integer('point');
            $table->json('originalAnswers')->default(new Expression('(JSON_ARRAY())'));
            $table->string('examId');
            $table->string('groupId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
