<?php

namespace App\Core;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Controller
{

    protected Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/Views');
        $this->twig = new Environment($loader, [
            'cache' => false, 
        ]);
    }
    

    public function render($view, $params = []): string
    {
        
        return Application::$app->response->render($view, $params);
    }


    
    


}