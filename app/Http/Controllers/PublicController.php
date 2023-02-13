<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class PublicController extends Controller {
    public function getResults($studentId) {
        $results = DB::select('select * from results');
        return $results;
    }

    public function getExams() {
        $exams = DB::select('select * from exam');
        return $exams;
    }

    public function getQuestions($examId) {
        $questions = DB::select('select * from question where examId = ?', [$examId]);
        return $questions;
    }

    // $examId, $groupId, $answers -> { $questionId, $answer }
    public function submitExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $groupId = $body['groupId'];
        $answers = $body['answers'];

        $exam = DB::select('select * from exams where id = ?', [$examId]);
        $questions = DB::select('select * from questions where exam_id = ?', [$examId]);
        $score = 0;
        foreach ($questions as $question) {
            $questionId = $question->id;
            $correctAnswer = $question->correct_answer;
            $answer = $answers[$questionId];
            if ($answer == $correctAnswer) {
                $score++;
            }
        }

        return DB::insert('insert into examSubmit (examName, examId, groupId, originalAnswers, answers, point) values (?, ?, ?, ?, ?, ?)', [$exam['examName'], $exam['id'], $groupId, $question, $answer,$score]);
    }

    //$studentId, $studentName, $studentBranch
    public function createGroup(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $studentId = $body['studentId'];
        $studentName = $body['studentName'];
        $studentBranch = $body['studentBranch'];
        return DB::insert('insert into submitGroup (studentId, studentName, studentBranch) values (?, ?, ?)', [$studentId, $studentName, $studentBranch]);
    }
}
