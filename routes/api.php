<?php

use App\Http\Controllers\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Optimizer;

use App\Http\Controllers\AuthData;
use App\Http\Controllers\TestRoute;
use App\Http\Controllers\TaskAnalyzerController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::match(['get', 'post'],'/callback',[Webhook::class,'webhook']);


Route::post('/linear_callback',[Optimizer::class,'linear_callback']); 

Route::get('/linear/redirect', [AuthData::class, 'redirectToLinear']);
Route::get('/linear_callback2', [AuthData::class, 'handleLinearCallback']);
Route::get('/linear/user', [AuthData::class, 'getLinearUser']);
Route::get('/linear/get_sub_issues', [AuthData::class, 'getSubIssues']);
Route::get('/linear/optimize', [AuthData::class, 'optimize']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [TestRoute::class, 'login'])->name('login'); 
    Route::get('/user', [TestRoute::class, 'user'])->name('user'); 
    Route::get('/services', [TestRoute::class, 'services'])->name('services'); 
    Route::get('/data', [TestRoute::class, 'datas'])->name('datas'); 

});
// lin_oauth_16ac063855b02234398894d8538e44760561d76a7d57c3f69d5d68dda4c225b4

Route::post('/searchuser', [AuthData::class, 'streamChat']);

Route::post('/task-analyzer/chat/stream', [TaskAnalyzerController::class, 'streamTaskAnalyzerChat']);
Route::post('/task-analyzer/search/test', [TaskAnalyzerController::class, 'testSimilaritySearch']);
Route::get('/task-analyzer/components', [TaskAnalyzerController::class, 'getComponentTypes']);

// Alternative route grouping (optional)
Route::prefix('task-analyzer')->group(function () {
    Route::post('/chat/stream', [TaskAnalyzerController::class, 'streamTaskAnalyzerChat']);
    Route::post('/search/test', [TaskAnalyzerController::class, 'testSimilaritySearch']);
    Route::get('/components', [TaskAnalyzerController::class, 'getComponentTypes']);
});