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

class PublicController extends Controller {
    public function getResults($studentId) {
        return 'ok';
    }

    public function getExams() {
        return 'ok';
    }

    public function getQuestions($examId) {
        return 'ok';
    }

    // $examId, $groupId, $answers -> { $questionId, $answer }
    public function submitExam(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $examId = $body['examId'];
        $groupId = $body['groupId'];
        $answers = $body['answers'];
    }

    //$studentId, $studentName, $studentBranch
    public function createGroup(Request $request) {
        $input = $request->getContent();
        $body = json_decode($input, true);
        $studentId = $body['studentId'];
        $studentName = $body['studentName'];
        $studentBranch = $body['studentBranch'];
    }
}
