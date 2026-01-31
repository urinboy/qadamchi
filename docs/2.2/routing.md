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

## Guruh Route-lar
```php
Route::group(['prefix' => 'api', 'middleware' => 'api'], function() {
    Route::get('/users', 'ApiController@users');
});
```

## Named Route-lar
```php
Route::get('/profile', 'UserController@profile')->name('profile');
```

URL olish:
```php
$url = Route::url('profile');
```