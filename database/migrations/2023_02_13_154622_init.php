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

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('question', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('choice1');
            $table->string('choice2');
            $table->boolean('answer');

            $table->string('examId');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('submitGroup', function (Blueprint $table) {
            $table->id();
            $table->string('studentId');
            $table->string('studentName');
            $table->string('studentBranch');

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::create('examSubmit', function (Blueprint $table) {
            $table->id();
            $table->json('answers')->default(new Expression('(JSON_ARRAY())'));
            $table->integer('point');
            $table->json('originalAnswers')->default(new Expression('(JSON_ARRAY())'));
            $table->string('examId');
            $table->string('groupId');

            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }
};
