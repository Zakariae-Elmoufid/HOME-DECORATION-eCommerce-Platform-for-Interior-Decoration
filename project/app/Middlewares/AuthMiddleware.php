<?php

namespace App\Middlewares;



use App\Core\MiddlewareInterface;
use App\Core\Session;
use App\Core\Request;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool
    {
        if (!$this->isAuthenticated()) {
            header('Location: /login');
            return false;
        }
        return true;
    }
    
    private function isAuthenticated()
    {
        Session::start();
        return Session::get('id') !== null;
    }
}