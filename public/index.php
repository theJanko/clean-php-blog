<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Europe/Warsaw');

// Załaduj zmienne środowiskowe
Config::load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/routes.php';
