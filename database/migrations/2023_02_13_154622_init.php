<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->timestampsTz($precision = 0); // created_at updated_at
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('choice1');
            $table->string('choice2');
            $table->boolean('answer');

            $table->string('examId');
            $table->timestampsTz($precision = 0);
        });

        Schema::create('submitGroup', function (Blueprint $table) {
            $table->id();
            $table->string('studentId');
            $table->string('studentName');
            $table->string('studentBranch');

            $table->timestampsTz($precision = 0);
        });

        Schema::create('examSubmit', function (Blueprint $table) {
            $table->id();
            $table->json('answers')->default(new Expression('(JSON_ARRAY())'));
            $table->integer('point');
            $table->json('originalAnswers')->default(new Expression('(JSON_ARRAY())'));
            $table->string('examId');
            $table->string('groupId');

            $table->timestampsTz($precision = 0);
        });
    }
};
