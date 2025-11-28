<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-mail', [TestController::class, 'testMail'])->name('testMail');
