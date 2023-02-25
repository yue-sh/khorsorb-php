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

class AdminController extends Controller
{
    public function getStats()
    {
        $examCount = DB::select('select count(*) as count from exam')[0]->count;
        $questionCount = DB::select('select count(*) as count from question')[0]->count;
        $examSubmitCount = DB::select('select count(*) as count from examSubmit')[0]->count;
        $examSubmit = DB::select('select * from examSubmit');
        $submitGroup = DB::select('select * from submitGroup');
        //Get monday to sunday of this week and submit count to array to make chart js
        $weekSubmitCount = [];
        $week = [];
        $week[0] = date('Y-m-d', strtotime('monday this week'));
        $week[1] = date('Y-m-d', strtotime('tuesday this week'));
        $week[2] = date('Y-m-d', strtotime('wednesday this week'));
        $week[3] = date('Y-m-d', strtotime('thursday this week'));
        $week[4] = date('Y-m-d', strtotime('friday this week'));
        $week[5] = date('Y-m-d', strtotime('saturday this week'));
        $week[6] = date('Y-m-d', strtotime('sunday this week'));
        for ($i = 0; $i < 7; $i++) {
            $weekSubmitCount[$i] = DB::select('select count(*) as count from examSubmit where created_at like ?', [$week[$i] . '%'])[0]->count;
        }
        //examSubmitByBranch in the week to make bar chart
        $examSubmitByBranch = [];
        $branches = DB::select('select * from submitGroup');
        foreach ($branches as $branch) {
            $name = $branch->studentBranch;
            //If branch name is not in array, add it
            if (!array_key_exists($name, $examSubmitByBranch)) {
                $examSubmitByBranch[$name] = 0;
            }
            //Get submit count of this branch in this week
            $examSubmitByBranch[$name] += 1;
        }
        $stats = array(
            "examCount" => $examCount,
            "questionCount" => $questionCount,
            "examSubmitCount" => $examSubmitCount,
            "examSubmit" => $examSubmit,
            "submitGroup" => $submitGroup,
            "weeklyExamSubmitCount" => $weekSubmitCount,
            "examSubmitByBranch" => $examSubmitByBranch
        );

        return $stats;
    }

    public function getAdminResults()
    {
        $groups = DB::select('select * from submitGroup');
        $submits = DB::select('select * from examSubmit');
        $results = [];
        foreach ($groups as $group) {
            $groupId = $group->id;
            $groupSubmits = [];
            foreach ($submits as $submit) {
                if ($submit->groupId == $groupId) {
                    $groupSubmits[] = $submit;
                }
            }
            $group->submits = $groupSubmits;
            $results[] = $group;
        }

        return $results;
    }

    public function adminLogin(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $username = $body['username'];
        $password = $body['password'];

        return 'ok';
    }

    public function createExam(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $name = $body['name'];
        $questions = $body['questions'];
        $exist = DB::select('select * from exam where name = ?', [$name]);
        $i = 1;
        while ($exist) {
            $name = preg_replace('/\(\d+\)$/', '', $name);
            $name = $name . ' (' . $i . ')';
            $exist = DB::select('select * from exam where name = ?', [$name]);
            $i++;
        }
        DB::insert('insert into exam (name) values (?)', [$name]);
        $exam = json_decode(json_encode(DB::select('select * from exam where name = ?', [$name])[0]), true);
        foreach ($questions as $question) {
            DB::insert('insert into question (examId, text, choice1, choice2, answer) values (?, ?, ?, ?, ?)', [$exam['id'], $question['text'], $question['choice1'], $question['choice2'], $question['answer']]);
        }

        return $exam;
    }

    public function updateExam(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $data = $body['data'];
        $name = $data['name'];

        $exist = DB::select('select * from exam where id = ?', [$examId]);
        if (!$exist) {
            return 'exam not exist';
        }
        DB::update('update exam set name = ? where id = ?', [$name, $examId]);
        $updatedExam = DB::select('select * from exam where id = ?', [$examId])[0];

        return $updatedExam;
    }

    public function deleteExam(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];

        $exist = DB::select('select * from exam where id = ?', [$examId]);
        if (!$exist) {
            return 'exam not exist';
        }
        DB::delete('delete from question where examId = ?', [$examId]);
        DB::delete('delete from exam where id = ?', [$examId]);

        return 'exam deleted';
    }

    public function createQuestion(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $data = $body['data'];
        $choice1 = $data['choice1'];
        $choice2 = $data['choice2'];
        $text = $data['text'];
        $answer = $data['answer'];

        $examExist = DB::select('select * from exam where id = ?', [$examId]);
        if (!$examExist) {
            return 'exam not exist';
        }
        DB::insert('insert into question (examId, text, choice1, choice2, answer) values (?, ?, ?, ?, ?)', [$examId, $text, $choice1, $choice2, $answer]);
        $question = DB::select('select * from question where examId = ? and text = ?', [$examId, $text]);

        return end($question);
    }

    public function updateQuestion(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $questionId = $body['questionId'];
        $data = $body['data'];
        $choice1 = $data['choice1'];
        $choice2 = $data['choice2'];
        $text = $data['text'];
        $answer = $data['answer'];

        $questionExist = DB::select('select * from question where id = ?', [$questionId]);
        if (!$questionExist) {
            return 'question not exist';
        }
        DB::update('update question set text = ?, choice1 = ?, choice2 = ?, answer = ? where id = ?', [$text, $choice1, $choice2, $answer, $questionId]);
        $updatedQuestion = DB::select('select * from question where id = ?', [$questionId])[0];

        return $updatedQuestion;
    }

    public function deleteQuestion(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $questionId = $body['questionId'];

        $questionExist = DB::select('select * from question where id = ?', [$questionId]);
        if (!$questionExist) {
            return 'question not exist';
        }
        DB::delete('delete from question where id = ?', [$questionId]);

        return 'question deleted';
    }

    public function updateSetting(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $key = $body['key'];
        $value = $body['value'];
    }

    public function getAdminQuestions()
    {
    }

    public function updateGroup(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $groupId = $body['groupId'];
        $data = $body['data'];
        $studenId = $data['studentId'];
        $studentName = $data['studentName'];
        $studentBranch = $data['studentBranch'];

        $groupExist = DB::select('select * from submitGroup where id = ?', [$groupId]);
        if (!$groupExist) {
            return 'group not exist';
        }
        DB::update('update submitGroup set studentId = ?, studentName = ?, studentBranch = ? where id = ?', [$studentId, $studentName, $studentBranch, $groupId]);
        $updatedGroup = DB::select('select * from group where id = ?', [$groupId])[0];

        return $updatedGroup;
    }

    public function deleteGroup(Request $request)
    {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $groupId = $body['groupId'];

        $groupExist = DB::select('select * from submitGroup where id = ?', [$groupId]);
        if (!$groupExist) {
            return 'group not exist';
        }
        DB::delete('delete from submitGroup where id = ?', [$groupId]);

        return 'group deleted';
    }
}
