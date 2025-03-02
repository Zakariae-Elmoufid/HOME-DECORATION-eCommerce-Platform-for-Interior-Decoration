<?php

namespace App\Core;

class Router {

    private  $routes = [];
    private $middlewares = [];

    private Request $request;
    private Response $response;



    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function middleware($route, $middlewareClass)
    {
        $this->middlewares[$route][] = $middlewareClass;
        return $this;
    }
    
    public function getRoutes(){
        return $this->routes;
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
            // $this->response->statusCode(code: 404);
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

                if (isset($this->middlewares[$url])) {
                    foreach ($this->middlewares[$url] as $middlewareClass) {
                        $middleware = new $middlewareClass();
                        $result = $middleware->handle($this->request);
                        
                        // Si un middleware retourne false, arrêter le traitement
                        if ($result === false) {
                            return false;
                        }
                    }
                }
                
                if (is_array($callback)) {
                    // dump(Application::$app->controller );
                    $controller = $callback[0];
                    $method = $callback[1];
                    $namespace = "App\\Controllers\\";
                    $controllerClass = $namespace . $controller;
                    $controllerInstance = new $controllerClass();
                    return $controllerInstance->$method( $this->request);
                    // Application::$app->controller = $controller;
                    // $callback[0] = $controller;
                }

         

        }

    }





    
        
    




}