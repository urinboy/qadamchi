<?php
abstract class Middleware {
    abstract public function handle($request, $next);
}