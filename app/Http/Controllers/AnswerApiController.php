<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;

class AnswerApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $answers = Answer::where('question_id',$request->get('question_id'))
                            ->where('deleted',0)
                            ->count();

        if($answers + 1 <= 3){
            $answer = new Answer;
            $answer->question_id = $request->get('question_id');
            $answer->user_id = \Auth::user()->id;
            $answer->content = $request->get('content');
            $answer->save();

            return response()->json([
                "success" => "success",
            ]);

        }else{
            return response()->json([
                "success" => "fail",
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if(\Auth::user()->is_chosen == 0){
            return response()->json([
                "success" => "fail",
            ]);
        }

        $answer = Answer::where('id',$id)
                        ->where('deleted',0)
                        ->first();

        if($answer->is_chosen == 0){
            $answer->deleted = 1;
            $answer->save();

            return response()->json([
                "success" => "success",
            ]);

        }else if($answer->is_chosen == 1){
            return response()->json([
                "success" => "fail",
            ]);

        }else{
            return response()->json([
                "success" => "fail",
            ]);
        }
    }
}
