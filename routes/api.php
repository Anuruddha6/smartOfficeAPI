<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//U
use App\Http\Controllers\Services\UsersController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/test', function (Request $request) {
    return response()->json(['status' => 'success']);
})->name('test');

Route::post('/Users/setUser', [UsersController::class, 'setUser'])->name('setUser');
Route::post('/Users/loginUser', [UsersController::class, 'loginUser'])->name('loginUser');


// Auth Routes
Route::middleware('auth:sanctum')->group(function () {

    // U
    Route::prefix('Users')->group(function (){
        Route::post('/getUsers', [UsersController::class, 'getUsers'])->name('Users.getUsers');
        Route::post('/getUser', [UsersController::class, 'getUser'])->name('Users.getUser');
        Route::post('/deleteUser', [UsersController::class, 'deleteUser'])->name('Users.deleteUser');
        Route::post('/activateUser', [UsersController::class, 'activateUser'])->name('Users.activateUser');
        Route::post('/logoutUser', [UsersController::class, 'logoutUser'])->name('Users.logoutUser');
    });


});
