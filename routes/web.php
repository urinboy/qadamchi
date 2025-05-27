<?php

Route::get('/', 'WelcomeController@index')->name('home');

Route::get('/about/{slug}', function($slug){
    echo "About page!";
    echo $slug;
})->name('about');

Route::group(['prefix' => '/admin', 'middleware' => 'AdminMiddleware'], function() {
    Route::get('/dashboard', 'AdminController@dashboard')->name('admin.dashboard');
    Route::post('/create', 'AdminController@create')->middleware('AnotherMiddleware');
});

Route::get('/user/{id}', 'UserController@profile')->name('user.profile');