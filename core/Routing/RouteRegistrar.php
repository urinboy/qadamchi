<?php
namespace Qadamchi\Routing;

/**
 * Route'ni chainable sozlash: ->name() / ->middleware()
 * Route::get('/users/{id}', 'UserController@show')->name('users.show')->middleware('auth');
 */
class RouteRegistrar
{
    protected array $route;
    protected int $id;

    public function __construct(array &$route, int $id)
    {
        $this->route = &$route;
        $this->id = $id;
    }

    public function name(string $name): self
    {
        Route::setName($this->id, $name);
        return $this;
    }

    public function middleware($middleware): self
    {
        Route::addMiddleware($this->id, $middleware);
        return $this;
    }
}