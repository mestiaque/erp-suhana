<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('Auth.adminLogin');
});

Route::get('/dashboard', function () {


    return view('Admin.dashboard');
});

