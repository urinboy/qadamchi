<?php
/**
 * Middleware uchun asos
 */
abstract class Middleware {
    abstract public function handle($request, $next);
}