<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionApiController;
use App\Http\Controllers\AnswerApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $user = App\Models\User::where('email', $request->get('email'))->first();

    return $user->createToken('token-name')->plainTextToken;
});

Route::prefix('/questions')->group(function(){
    Route::get('/',[QuestionApiController::class, 'index']);
    Route::get('/{question}',[QuestionApiController::class, 'show']);
    Route::post('/',[QuestionApiController::class, 'store'])->middleware('auth:sanctum');
});

Route::prefix('/answers')->group(function(){
    Route::post('/',[AnswerApiController::class, 'store'])->middleware('auth:sanctum')->name('answer.store');
    Route::put('/{answer}',[AnswerApiController::class, 'update'])->middleware('auth:sanctum')->name('answer.update');
    Route::delete('/{answer}',[AnswerApiController::class, 'destroy'])->name('answer.destroy');
});
