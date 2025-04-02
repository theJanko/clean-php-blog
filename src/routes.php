<?php

use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Core\Router;
use App\Middleware\AuthMiddleware;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);

$router = new Router($twig);

$router->add('GET', '/login', AuthController::class, 'loginPage');
$router->add('POST', '/login', AuthController::class, 'login');
$router->add('GET', '/logout', AuthController::class, 'logout');

$router->add('GET', '/admin', AdminController::class, 'adminPage', [AuthMiddleware::class]);
$router->add('GET', '/admin/article/create', AdminController::class, 'createArticle', [AuthMiddleware::class]);
$router->add('POST', '/admin/article/create', AdminController::class, 'createArticle', [AuthMiddleware::class]);
$router->add('GET', '/admin/article/edit/{id}', AdminController::class, 'editArticle', [AuthMiddleware::class]);
$router->add('POST', '/admin/article/edit/{id}', AdminController::class, 'editArticle', [AuthMiddleware::class]);
$router->add('POST', '/admin/article/delete', AdminController::class, 'deleteArticle', [AuthMiddleware::class]);
$router->add('GET', '/admin/article/{id}', AdminController::class, 'getArticle', [AuthMiddleware::class]);

$router->dispatch();
