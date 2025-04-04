<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;

error_reporting(E_ALL);
ini_set('display_errors', 0);

set_exception_handler(function (Throwable $e) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        exit;
    }

    echo '<h1>Application Error</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p>File: ' . $e->getFile() . ' on line ' . $e->getLine() . '</p>';
    exit;
});

date_default_timezone_set('Europe/Warsaw');

Config::load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../src/routes.php';
