<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');


Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::post('me', 'me');
    });
});
Route::middleware('auth:api')->group(function () {
    Route::controller(TodoController::class)->group(function () {
        Route::get('todos', 'index');
        Route::post('todo', 'store');
        Route::get('todo/{id}', 'show');
        Route::put('todo/{id}', 'update');
        Route::post('todo/mark-as-done', 'markAsDone');
        Route::post('todo/mark-as-undone', 'markAsUndone');
        Route::delete('todo/{id}', 'destroy');
    });
});
