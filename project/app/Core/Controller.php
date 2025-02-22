<?php

namespace App\Core;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{
    public string $layout = 'main';

    protected Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/Views');
        $this->twig = new Environment($loader, [
            'cache' => false, 
        ]);
    }
    public function setLayout($layout): void
    {
        $this->layout ="layouts/$layout";
    }
    public function render($view, $params = []): string {
        try {
        
               echo  $this->twig->render("$view.twig", $params);
               exit;

        } catch (\Exception $e) {
            die("Twig Error: " . $e->getMessage());
        }
    }
}