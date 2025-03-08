<?php 
 
namespace App\Middleware; 
use App\Core\Middleware;

class AuthMiddleware implements Middleware {
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
        return isset($_SESSION['user_id']);
    }
}
