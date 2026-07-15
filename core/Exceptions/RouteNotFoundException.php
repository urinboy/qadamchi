<?php
namespace Qadamchi\Exceptions;

use Qadamchi\Exceptions\QadamchiException;

class RouteNotFoundException extends QadamchiException
{
    public function __construct(string $route)
    {
        parent::__construct("Route topilmadi: $route");
    }
}