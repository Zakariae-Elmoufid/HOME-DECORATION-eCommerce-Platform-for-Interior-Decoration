<?php
namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
Session::start();
class Response

{

    protected Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader(dirname(__DIR__) . '/Views');
        $this->twig = new Environment($loader, [
            'cache' => false, 
        ]);
        $this->twig->addFunction(new TwigFunction('flash', function ($key) {
            return Session::getFlash($key);
        }));
        $this->twig->addGlobal('session', $_SESSION);


    }
    public function render(string $view, array $params = []): string
    {      
        if (session_status() == PHP_SESSION_NONE) {
           Session::start();
        }
        
        $this->twig->addGlobal('session', $_SESSION);
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

    public  function renderError(string $message) {
        $this->render('error', ['message' => $message]);
    }
    

    public function jsonEncode($data){
        echo json_encode($data);
        exit;
    }

}
