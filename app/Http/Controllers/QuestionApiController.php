<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

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
            $questions,
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
