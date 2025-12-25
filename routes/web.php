<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/articles', function () {
    return view('articles.index');
})->name('articles.index');

Route::get('/categories', function () {
    return view('categories.index');
})->name('categories.index');
