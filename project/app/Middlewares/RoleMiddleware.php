<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Session;
use App\Core\Request;
use App\Core\Response;


class RoleMiddleware implements MiddlewareInterface{
    private array $allowedRoles;
    private  $response;
     
    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
        $this->response = new Response();

    }

    public function handle(Request $request): bool
    {
        Session::start();
        $user = Session::get('id')?? null;
        $userRole = Session::get('role');
        if (!$user || !in_array($userRole, $this->allowedRoles)) {
            return $this->response->renderError("don't access this role");
        }
        return true;
    }
}
