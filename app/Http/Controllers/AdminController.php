<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class AdminController extends Controller {
    public function getStats() {
        return 'ok';
    }

    public function getAdminResults() {
        return 'ok';
    }

    public function adminLogin(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $username = $body['username'];
        $password = $body['password'];
    }

    public function createExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $name = $body['name'];
        $questions = $body['questions'];
    }

    public function updateExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $rawData = $body['data'];
        $data = json_decode($rawData, true);
        $name = $data['name'];
    }

    public function deleteExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
    }

    public function createQuestion(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $rawData = $body['data'];
        $data = json_decode($rawData, true);
        $choice1 = $data['choice1'];
        $choice2 = $data['choice2'];
        $text = $data['text'];
        $answer = $data['answer'];
    }

    public function updateQuestion(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $questionId = $body['questionId'];
        $rawData = $body['data'];
        $data = json_decode($rawData, true);
        $choice1 = $data['choice1'];
        $choice2 = $data['choice2'];
        $text = $data['text'];
        $answer = $data['answer'];
    }

    public function deleteQuestion(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $questionId = $body['questionId'];
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
        $rawData = $body['data'];
        $data = json_decode($rawData, true);
        $studenId = $data['studentId'];
        $studentName = $data['studentName'];
        $studentBranch = $data['studentBranch'];
    }

    public function deleteGroup(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $groupId = $body['groupId'];
    }
}
