<?php

// Bosh sahifa
Route::get('/', 'WelcomeController@index')->name('home');

// Biz haqimizda
Route::get('/biz-haqimizda', 'AboutController@index')->name('about');
Route::get('/biz-haqimizda/{slug}', 'AboutController@index')->name('about.slug');

// Jamoa va Aloqa
Route::get('/jamoa', 'AboutController@team')->name('team');
Route::get('/aloqa', 'AboutController@contact')->name('contact');

// Blog (kelajak uchun)
Route::get('/blog', function() {
    return view('blog.index');
})->name('blog');

// API route-lar (web.php da, lekin API uchun)
Route::group(['prefix' => 'api'], function() {
    Route::get('/about', function() {
        return Response::json(['message' => 'Biz haqimizda API']);
    });
});

// Middleware bilan himoyalangan route (masalan, admin)
Route::group(['middleware' => 'auth'], function() {
    Route::get('/admin', function() {
        return 'Admin panel';
    })->name('admin');
});
