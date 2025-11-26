<?php

use Illuminate\Support\Facades\Route;

// Central domain routes
Route::get('/', function () {
    return view('welcome');
});

