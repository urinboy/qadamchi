<?php

Route::get('/', 'WelcomeController@index')->name('home');

Route::get('/about/{slug}', function($slug){
    echo "About page!";
    echo $slug;
})->name('about');
