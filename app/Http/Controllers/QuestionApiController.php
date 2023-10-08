<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Validator;

class QuestionApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::join('users',function($join){
                                        $join->on('questions.user_id','=','users.id')
                                            ->where('users.deleted',0);
                                    })
                                ->where('questions.deleted',0)
                                ->select(
                                    'questions.title',
                                    \DB::raw('left(content,20) as content'),
                                    'questions.created_at',
                                    'users.breed',
                                )
                                ->paginate(6);
        
        return response()->json([
            "success" => "success",
            "response" => $questions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'in:사료 고민,집사 고민,그루밍'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => "fail",
            ]);
        }

        $question = new Question;
        $question->user_id = \Auth::user()->id;
        $question->title = $request->get('title');
        $question->content = $request->get('content');
        $question->category = $request->get('category');
        $question->save();

        return response()->json([
            "success" => "success",
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::leftjoin('answers',function($join){
                                $join->on('questions.id','=','answers.question_id')
                                    ->where('answers.deleted',0);
                            })
                            ->join('users as q_user', 'questions.user_id','=','q_user.id')
                            ->leftjoin('users as a_user', 'answers.user_id','=','a_user.id')
                            ->where('questions.deleted',0)
                            ->where('questions.id',$id)
                            ->select(
                                'questions.id as question_id',
                                'questions.title as question_title',
                                'questions.content as question_content',
                                'questions.created_at as question_at',
                                'q_user.breed as question_breed',
                                'answers.content as answer_content',
                                'answers.is_chosen',
                                'answers.created_at as answer_at',
                                'a_user.breed as answer_breed',
                            )
                            ->get()
                            ->groupBy('question_id');

        return response()->json([
            "success" => "success",
            "response" => $question,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
