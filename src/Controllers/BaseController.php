<?php

namespace App\Controllers;

abstract class BaseController
{
    protected $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }

    protected function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }
}
