<?php

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Session;
use App\Core\Request;

class RoleMiddleware implements MiddlewareInterface {
    private array $allowedRoles;

    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request): bool
    {
        Session::start();
        $userRole = Session::get('role'); // Exemple: "admin"

        if (!in_array($userRole, $this->allowedRoles)) {
            http_response_code(403);
            echo "403 - Forbidden";
            return false;
        }
        return true;
    }
}
