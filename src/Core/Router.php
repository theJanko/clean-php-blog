<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    public function add(string $method, string $path, string $controller, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch(): void
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                $controller = new $route['controller']($this->twig);
                echo $controller->{$route['action']}();
                return;
            }
        }

        if ($uri === '/') {
            if (!isset($_SESSION['user_logged']) || $_SESSION['user_logged'] !== true) {
                header('Location: /login');
                exit;
            }
            header('Location: /admin');
            exit;
        }

        http_response_code(404);
        echo $this->twig->render('errors/404.twig');
        return;
    }
}
