<?php
/**
 * Qisqa sinf aliaslari -> to'liq namespace.
 * routes/web.php va view'larda qisqa nomlar ishlatiladi (Route, DB, Auth...).
 */
return [
    'Route'     => 'Qadamchi\Routing\Route',
    'DB'        => 'Qadamchi\Database\DB',
    'Model'     => 'Qadamchi\Database\Model',
    'QueryBuilder' => 'Qadamchi\Database\QueryBuilder',
    'Schema'    => 'Qadamchi\Database\Schema',
    'Blueprint' => 'Qadamchi\Database\Blueprint',
    'Migration' => 'Qadamchi\Database\Migration',
    'Seeder'    => 'Qadamchi\Database\Seeder',
    'Request'   => 'Qadamchi\Http\Request',
    'Response'  => 'Qadamchi\Http\Response',
    'Middleware'=> 'Qadamchi\Http\Middleware',
    'Controller'=> 'Qadamchi\Http\Controller',
    'Session'   => 'Qadamchi\Http\Session',
    'CSRF'      => 'Qadamchi\Http\CSRF',
    'Security'  => 'Qadamchi\Security\Security',
    'Auth'      => 'Qadamchi\Auth\Auth',
    'Validator' => 'Qadamchi\Validation\Validator',
    'View'      => 'Qadamchi\View\View',
    'Blade'     => 'Qadamchi\View\Blade',
    'Lang'      => 'Qadamchi\Support\Lang',
    'Logger'    => 'Qadamchi\Support\Logger',
    'Config'    => 'Qadamchi\Support\Config',
];