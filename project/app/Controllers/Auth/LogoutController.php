<?php

namespace App\Controllers\Auth;
use App\Services\AuthService;

class LogoutController {

    public function logout(){
        $authServices = new AuthService();
        $authServices->logout();

    }
}