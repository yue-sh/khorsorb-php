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

class AdminController extends Controller {
    public function getStats() {
        $examCount = DB::select('select count(*) as count from exam')[0]->count;
        $questionCount = DB::select('select count(*) as count from question')[0]->count;
        return $examCount;
    }

    public function getAdminResults() {
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

    public function adminLogin(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $username = $body['username'];
        $password = $body['password'];

        return 'qwertyuiop' + $username + ':' + $password;
    }

    public function createExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $name = $body['name'];
        $questions = $body['questions'];

        $exist = DB::select('select * from exam where name = ?', [$name]);
        if ($exist) {
            return 'exam already exist';
        }
        DB::insert('insert into exam (name) values (?)', [$name]);
        $exam = json_decode(json_encode(DB::select('select * from exam where name = ?', [$name])[0]), true);
        foreach ($questions as $question) {
            DB::insert('insert into question (examId, text, choice1, choice2, answer) values (?, ?, ?, ?, ?)', [$exam['id'], $question['text'], $question['choice1'], $question['choice2'], $question['answer']]);
        }

        return $exam;
    }

    public function updateExam(Request $request) {
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

    public function deleteExam(Request $request) {
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

    public function createQuestion(Request $request) {
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

    public function updateQuestion(Request $request) {
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

        return $updaqweqwetedQuestion;
    }

    public function deleteQuestion(Request $request) {
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

    public function updateSetting(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $key = $body['key'];
        $value = $body['value'];
    }

    public function getAdminQuestions() {

    }

    public function updateGroup(Request $request) {
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

    public function deleteGroup(Request $request) {
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
