<?php

use App\Controllers\AuthController;
use App\Core\Router;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

$router = new Router($twig);

$router->add('GET', '/login', AuthController::class, 'loginPage');
$router->add('POST', '/login', AuthController::class, 'login');
$router->add('GET', '/logout', AuthController::class, 'logout');
$router->add('GET', '/admin', AuthController::class, 'adminPage');

$router->dispatch();
