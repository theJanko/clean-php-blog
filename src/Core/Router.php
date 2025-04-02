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

    public function add(string $method, string $path, string $controller, string $action, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    private function matchRoute(string $uri, array $route): ?array
    {
        $routePath = preg_replace('/{[^}]+}/', '([^/]+)', $route['path']);
        $routePath = str_replace('/', '\/', $routePath);
        $routePath = '/^' . $routePath . '$/';

        if (preg_match($routePath, $uri, $matches)) {
            array_shift($matches);
            return $matches;
        }

        return null;
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $params = $this->matchRoute($uri, $route);
                if ($params !== null) {
                    // Execute middleware
                    foreach ($route['middleware'] as $middlewareClass) {
                        $middleware = new $middlewareClass();
                        $middleware->handle();
                    }

                    $controller = new $route['controller']($this->twig);
                    echo $controller->{$route['action']}(...$params);
                    return;
                }
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
    }
}
