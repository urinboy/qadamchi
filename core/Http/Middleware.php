<?php
namespace Qadamchi\Http;

/**
 * Middleware bazasi (Laravel uslubidagi pipeline kontrakti).
 * handle($request, $next) — return $next($request) davom ettiradi;
 * Response qaytarsa pipeline to'xtaydi (short-circuit).
 */
abstract class Middleware
{
    abstract public function handle($request, \Closure $next);
}