<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PublicController;

Route::get('/v1/public/results/{studentId}', [PublicController::class, 'getResults']);

Route::get('/v1/public/exams', [PublicController::class, 'getExams']);

Route::get('/v1/public/questions/{examId}', [PublicController::class, 'getQuestions']);

Route::post('/v1/public/exam/submit', [PublicController::class, 'submitExam']);

Route::post('/v1/public/group/create', [PublicController::class, 'createGroup']);

Route::get('/v1/admin/stats', [AdminController::class, 'getStats']);

Route::get('/v1/admin/results', [AdminController::class, 'getAdminResults']);

Route::post('/v1/admin/login', [AdminController::class, 'adminLogin']);

Route::post('/v1/admin/exam/create', [AdminController::class, 'createExam']);

Route::post('/v1/admin/exam/update', [AdminController::class, 'updateExam']);

Route::post('/v1/admin/exam/delete', [AdminController::class, 'deleteExam']);

Route::post('/v1/admin/question/create', [AdminController::class, 'createQuestion']);

Route::post('/v1/admin/question/update', [AdminController::class, 'updateQuestion']);

Route::post('/v1/admin/question/delete', [AdminController::class, 'deleteQuestion']);

Route::post('/v1/admin/setting/update', [AdminController::class, 'updateSetting']);

Route::post('/v1/admin/group/update', [AdminController::class, 'updateGroup']);

Route::post('/v1/admin/group/delete', [AdminController::class, 'deleteGroup']);
