<?php

namespace App\Http\Controllers\API\Patient;

use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Http\Controllers\Controller;
use App\Interfaces\Questionnaires\QuestionnaireRepositoryInterface;

class QuestionnaireController extends Controller
{
    private $questionnaireRepository;

    public function __construct(QuestionnaireRepositoryInterface $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }


    //display all questions
    public function show() {
        $questions = Questionnaire::where('type','written')
        ->orwhere('type','options')->get();
        return response([
            'status' => true,
            'questions'=>$questions
        ]);
    }

    public function answer(Request $request) {
        return $this->questionnaireRepository->answer($request);
    }

}


