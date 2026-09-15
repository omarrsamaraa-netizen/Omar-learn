<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/main', function () {

 $category=[
   ["name" => "watches", "matireals" => "gold","rate" => "60", "id" => "1"], 
   ["name" => "suits", "matireals" => "fabric", "rate" => "70", "id" => "2"],

 ];


    return view('main.index', ["luxury" => "be a gentle" , "category"=> $category]);
});

Route::get('/main/create', function () {
    return view('main.create');
});

Route::get('/main/{id}', function ($id) {

 
    return view('main.show', ["id"=> $id]);
});