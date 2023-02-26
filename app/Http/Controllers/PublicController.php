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

class PublicController extends Controller
{
    public function getResults()
    {
        $results = DB::select('select * from results');
        return $results;
    }

    public function getExams()
    {
        $exams = DB::select('select * from exam left join (select examId, count(*) as questionCount from question group by examId) as questionCount on exam.id = questionCount.examId');
        return $exams;
    }

    public function getQuestions($examId)
    {
        $questions = DB::select('select * from question where examId = ?', [$examId]);
        return $questions;
    }

    // $examId, $groupId, $answers -> { $questionId, $answer }
    public function submitExam(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $groupId = $body['groupId'];
        $answers = $body['answers'];

        $score = 0;
        $group = DB::select('select * from submitGroup where id = ?', [$groupId]);
        if (!$group) {
            return 'group not found';
        }
        $exam = json_decode(json_encode(DB::select('select * from exam where id = ?', [$examId])[0]), true);
        $questions = json_decode(json_encode(DB::select('select * from question where examId = ?', [$examId])), true);
        foreach ($questions as $question) {
            $questionId = $question['id'];
            $correctAnswer = $question['answer'];
            foreach ($answers as $answer) {
                if ($answer['questionId'] == $questionId) {
                    $answer = $answer['answer'];
                    break;
                }
            }
            if ($answer == $correctAnswer) {
                $score++;
            }
        }
        $questions = json_encode($questions);
        $answers = json_encode($answers);
        DB::insert('insert into examSubmit (examId, groupId, point, originalAnswers, answers) values (?, ?, ?, ?, ?)', [$exam['id'], $groupId, $score, $questions, $answers]);

        //Empty object
        $examSubmit = new \stdClass();
        $examSubmit->examName = $exam['name'];
        $examSubmit->point = $score;
        $examSubmit->questionsCount = DB::select('select count(*) as count from question where examId = ?', [$examId])[0]->count;
        return $examSubmit;
    }

    //$studentId, $studentName, $studentBranch
    public function createGroup(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $input = $request->getContent();
        $body = json_decode($input, true);
        $studentId = $body['studentId'];
        $studentName = $body['studentName'];
        $studentBranch = $body['studentBranch'];

        // $exist = DB::select('select * from submitGroup where studentId = ?', [$studentId]);
        // if ($exist) {
        //     return 'group already exist';
        // }
        DB::insert('insert into submitGroup (studentId, studentName, studentBranch) values (?, ?, ?)', [$studentId, $studentName, $studentBranch]);
        $group = DB::select('select * from submitGroup order by id desc limit 1')[0];

        // $group = DB::select('select * from submitGroup where studentId = ?', [$studentId])[0];

        return $group;
    }
}
