<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Sprawdzenie trybu konserwacji
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Rejestracja autoloadera Composer
require __DIR__.'/../vendor/autoload.php';

// Uruchomienie aplikacji Laravel
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
