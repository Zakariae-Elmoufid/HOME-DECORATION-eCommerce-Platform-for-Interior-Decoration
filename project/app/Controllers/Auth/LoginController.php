<?php

namespace App\Controllers\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;

class LoginController extends Controller  {

public function index(){
  return $this->render('auth/login');
}      

public function login(Request $request){
  $data = $request->getBody();

  $authService = new AuthService;
  
    $result = $authService->findUser($data);
    if (!empty($result['errors'])) {
        return $this->render('auth/login', 
        ['errors' => $result['errors'],
        'old' => $result['old']
        ]
      );
    }
}

}