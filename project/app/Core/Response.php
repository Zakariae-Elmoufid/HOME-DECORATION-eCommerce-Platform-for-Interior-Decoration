<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Response
{

    protected Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/Views');
        $this->twig = new Environment($loader, [
            'cache' => false, 
        ]);
    }
    public function render(string $view, array $params = []): string
    {
        echo $this->twig->render("$view.twig", $params);
        exit;
    }
    
    public function statusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }
    


}
