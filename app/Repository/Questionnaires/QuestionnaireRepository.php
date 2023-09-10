<?php
namespace App\Repository\Questionnaires;


use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Questionnaire;
use App\Interfaces\Questionnaires\QuestionnaireRepositoryInterface;

class QuestionnaireRepository implements QuestionnaireRepositoryInterface
{
    public function answer(Request $request){

        $question_id = Questionnaire::where('id',$request->id)->first();
        $answer = Answer::where('question_id',$request->id)->first();
        if ( isset($question_id))
        {
            //----------if the question is already answer we will just update the answer----------//
            if(isset($answer))
            {
                $options = Questionnaire::whereNotNull('options')->where('id',$request->id)->first();
                $answer -> answer = $request->answer;
                $answer -> save();
                return response([
                    'status' => true,
                    $options,
                    $answer
                ]);
            }
            //-----------------------------------------------------------------------------------//
            else
            {
            //------------if the question isn't answered we will create a new answer------------//
                $options = Questionnaire::whereNotNull('options')->where('id',$request->id)->first();
                $answer =  Answer::create([
                    'question_id' => $question_id->id,
                    'answer' => $request->answer,
                    ]);
                return response([
                    'status' => true,
                    $options,
                    $answer
                ]);
            //-----------------------------------------------------------------------------------//
        }

        }
    }
}
