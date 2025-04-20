<?php

namespace App\Core;
// use Twig\Environment;
// use Twig\Loader\FilesystemLoader;
// use Twig\TwigFunction;

class Controller {

    public function render($view, $params = []): string
    {
        
        return Application::$app->response->render($view, $params);
    }
    public function redirect($uri){
        return Application::$app->response->redirect($uri);
    }

}