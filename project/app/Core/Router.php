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
        return $this;
    }

    
    public   function  post( $route, $callback) {
        $this->routes['post'][$route] = $callback;
    }
    public   function  patch( $route, $callback) {
        $this->routes['patch'][$route] = $callback;
    }

    public   function  delete( $route, $callback) {
        $this->routes['delete'][$route] = $callback;
    }
    
    public  function  dispatch() {
        
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();

        $callback = $this->routes[$method][$url] ?? false;
        if (!$callback) {
            $this->response->error404();

        }else{
            
            if (isset($this->middlewares[$url])) {
                foreach ($this->middlewares[$url] as $middleware) {
                    if (is_string($middleware)) {
                        $middlewareInstance = new $middleware(); 
                    } else {
                        $middlewareInstance = $middleware; 
                    }
                    $middlewareInstance->handle($this->request);
                }
                    
            }
            
            
                
                if (is_string($callback) && str_contains($callback, '@')) {
                    [$controller, $method] = explode('@', $callback);
                    $namespace = "App\\Controllers\\";
                    $controllerClass = $namespace . $controller;
                    
                    if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
                        $controllerInstance = new $controllerClass();
                        return $controllerInstance->$method();
                    } else {
                        $this->response->error404();
                       
                    }
                }


                
                if (is_array($callback)) {
                    $controller = $callback[0];
                    $method = $callback[1];
                    $controllerInstance = new $controller();
                    return $controllerInstance->$method($this->request);
                }

         

        }

    }


 

    
        
    




}