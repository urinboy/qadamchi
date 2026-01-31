# Qadamchi 2.2 - Misollar

## Oddiy Blog Loyihasi

### 1. Migration
```bash
php qadamchi make:migration create_posts_table
```

`app/Migrations/..._create_posts_table.php`:
```php
<?php
class CreatePostsTable extends Migration {
    public function up() {
        Schema::create('posts', function($table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }
}
```

### 2. Model
```bash
php qadamchi make:model Post
```

### 3. Controller
```bash
php qadamchi make:controller PostController
```

`app/Controllers/PostController.php`:
```php
<?php
namespace App\Controllers;

class PostController extends Controller {
    public function index() {
        $posts = Post::all();
        return $this->view('posts.index', ['posts' => $posts]);
    }

    public function show($id) {
        $post = Post::find($id);
        return $this->view('posts.show', ['post' => $post]);
    }
}
```

### 4. Routes
`routes/web.php`:
```php
Route::get('/posts', 'PostController@index');
Route::get('/posts/{id}', 'PostController@show');
```

### 5. Views
`app/Views/posts/index.php`:
```php
<h1>Posts</h1>
<?php foreach($posts as $post): ?>
    <h2><a href="/posts/<?php echo $post->id; ?>"><?php echo $post->title; ?></a></h2>
<?php endforeach; ?>
```

## API Misoli
`routes/api.php`:
```php
Route::group(['prefix' => 'api'], function() {
    Route::get('/posts', function() {
        return Response::json(Post::all());
    });
});
```

## Middleware Misoli
`app/Middlewares/AuthMiddleware.php`:
```php
<?php
class AuthMiddleware implements Middleware {
    public function handle($request, $next) {
        if (!Auth::check()) {
            return redirect('/login');
        }
        return $next($request);
    }
}
```

Route da:
```php
Route::get('/dashboard', 'DashboardController@index')->middleware('auth');
```