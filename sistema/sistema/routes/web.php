<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
Route::get('/', function () {
    return view('template/administrador');
});

Route::resource('categories', CategoryController::class)->except('show');