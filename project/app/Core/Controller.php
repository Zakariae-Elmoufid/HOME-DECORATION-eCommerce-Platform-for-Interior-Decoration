<?php

namespace App\Core;
// use Twig\Environment;
// use Twig\Loader\FilesystemLoader;
// use Twig\TwigFunction;

class Controller
{

    // protected Environment $twig;
    // public function __construct()
    // {
    //     $loader = new FilesystemLoader(dirname(__DIR__) . '/Views');
    //     $this->twig = new Environment($loader, [
    //         'cache' => false, 
    //     ]);

    //     $this->twig->addFunction(new TwigFunction('flash', function ($key) {
    //         return Session::getFlash($key);
    //     }));
    // }
    

    public function render($view, $params = []): string
    {
        
        return Application::$app->response->render($view, $params);
    }
    public function redirect($uri){
        return Application::$app->response->redirect($uri);
    }



    
    


}