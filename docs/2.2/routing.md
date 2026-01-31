# Qadamchi 2.2 - Routing

## Asosiy Route-lar
`routes/web.php` da:
```php
Route::get('/', 'WelcomeController@index');
Route::post('/contact', 'ContactController@store');
Route::put('/user/{id}', 'UserController@update');
Route::delete('/user/{id}', 'UserController@destroy');
```

## Parametrli Route-lar
```php
Route::get('/user/{id}', function($id) {
    return "User ID: $id";
});
```

## Middleware
```php
Route::get('/admin', 'AdminController@index')->middleware('auth');
```

## Controller bilan Route-lar
```php
Route::get('/about', 'AboutController@index');
Route::get('/about/{slug}', 'AboutController@index');
```

## Guruh Route-lar
```php
Route::group(['prefix' => 'api'], function() {
    Route::get('/users', 'ApiController@users');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/dashboard', 'DashboardController@index');
});
```

## Misol: To'liq web.php
```php
<?php
Route::get('/', 'WelcomeController@index')->name('home');
Route::get('/about', 'AboutController@index')->name('about');
Route::get('/about/{slug}', 'AboutController@index')->name('about.slug');
Route::get('/team', 'AboutController@team')->name('team');
Route::get('/contact', 'AboutController@contact')->name('contact');

Route::group(['prefix' => 'api'], function() {
    Route::get('/about', function() {
        return Response::json(['message' => 'About API']);
    });
});
```