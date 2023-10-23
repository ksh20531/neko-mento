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
        try{
            $questions = Question::with(['user' => function($q){
                                    $q->select(
                                        'id',
                                        'email',
                                        'breed'
                                    );
                                }])
                                ->where('deleted',0)
                                ->select(
                                    "*",
                                    \DB::raw('left(content,20) as content')
                                )
                                ->paginate(6);
            
            return response()->json([
                "success" => "success",
                "response" => $questions,
                "code" => 200
            ]);

        }catch(Exception $e){
            return response()->json([
                    "success" => "fail",
                    "error" => "DB_connection_error",
                    "code" => 500
                ]);
        }
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
            'category' => 'nullable|in:사료 고민,집사 고민,그루밍'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => "fail",
                "error" => "invalid_value",
                "code" => 202
            ]);
        }
        try{
            $question = new Question;
            $question->user_id = \Auth::user()->id;
            $question->title = $request->get('title');
            $question->content = $request->get('content');
            $question->category = $request->get('category');
            $question->save();

            return response()->json([
                "success" => "success",
                "code" => 201
            ]);

        }catch(Exception $e){
            return response()->json([
                    "success" => "fail",
                    "error" => "DB_connection_error",
                    "code" => 500
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
        try{
            $question = Question::with(['answers.user' => function($q){
                                    $q->where('deleted',0);
                                }])
                                ->with(['answers' => function($q){
                                    $q->where('deleted',0);
                                }])
                                ->with(['user' => function($q){
                                    $q->where('deleted',0);
                                }])
                                ->find($id);

            return response()->json([
                "success" => "success",
                "response" => $question,
                "code" => 200
            ]);

        }catch(Exception $e){
            return response()->json([
                "success" => "fail",
                "error" => "DB_connection_error",
                "code" => 500
            ]);
        }
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
        \Log::info("QuestionApiController::update");
        try{
            $question = Question::where('id',$id)
                                ->where('deleted',0)
                                ->first();

            $question->title = $request->get('title');
            $question->content = $request->get('content');
            $question->category = $request->get('category');
            $question->save();

            return response()->json([
                "success" => "success",
                "code" => 201
            ]);

        }catch(Exception $e){
            return response()->json([
                "success" => "fail",
                "error" => "DB_connection_error",
                "code" => 500
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $question = Question::where('id',$id)
                                ->where('deleted',0)
                                ->first();

            $question->deleted = 1;
            $question->save();

            return response()->json([
                "success" => "success",
                "code" => 201
            ]);

        }catch(Exception $e){
            return response()->json([
                "success" => "fail",
                "error" => "DB_connection_error",
                "code" => 500
            ]);
        }
    }
}
