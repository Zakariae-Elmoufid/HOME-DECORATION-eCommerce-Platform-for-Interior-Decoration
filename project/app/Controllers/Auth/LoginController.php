<?php

namespace App\Controllers\Auth;
use App\Core\Session;


use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;

class LoginController extends Controller  {

  private $authService ;

  public function __construct(){
      $this->authService = new AuthService() ; 
  }

public function index(){
  return $this->render('auth/login');
}      

public function login(Request $request){
  $data = $request->getBody();

  
    $result = $this->authService->findUser($data);
    if (!empty($result)) {
        return $this->render('auth/login', 
        ['errors' => $result,
        'old' => $data['email']
        ]
      );
    }
}

public function loginGoogle(Request $request) {
  $postData = $request->getBody();
   
  

  $result  = $this->authService->loginGoogle($postData);




}



}