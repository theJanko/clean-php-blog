<?php

namespace App\Controllers;

use Twig\Environment;

abstract class BaseController
{
    public function __construct(
        protected readonly Environment $twig
    ) {}

    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }

    protected function redirect(string $path): never
    {
        header("Location: $path");
        exit;
    }
}
