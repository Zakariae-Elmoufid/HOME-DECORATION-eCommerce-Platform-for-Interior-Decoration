<?php

namespace App\Core;

class Router {

    private  $routes;
    private Request $request;
    private Response $response;



    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public   function  get( $route, $callback) {
        $this->routes['get'][$route] = $callback;
    }

    public   function  post( $route, $callback) {
        $this->routes['post'][$route] = $callback;
    }

    
    public  function  dispatch() {
        
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();
        $callback = $this->routes[$method][$url] ?? false;
        if (!$callback) {
            $this->response->statusCode(code: 404);
            return 'Not Found';
        }else{
       

            if (is_string($callback) && str_contains($callback, '@')) {
                [$controller, $method] = explode('@', $callback);
                $namespace = "App\\Controllers\\";
                $controllerClass = $namespace . $controller;

                if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                    $controllerInstance = new $controllerClass();
                    return $controllerInstance->$method();
                } else {
                    http_response_code(404);
                    echo "404 - Controller ou méthode introuvable";
                    return;
                }
            }

        }

    }





    
        
    




}