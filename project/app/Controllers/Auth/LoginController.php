<?php

namespace App\Controllers\Auth;

use App\Core\Session;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\CartService;

class LoginController extends Controller  {

  private $authService;
  private $cartServise;
  private $response;

  public function __construct(){
      $this->authService = new AuthService(); 
      $this->cartServise = new CartService();
      $this->response = new Response;
  }

public function index(){
  return $this->render('auth/login');
}      

public function login(Request $request){
  $data = $request->getBody();

  
    $user = $this->authService->findUser($data);
    if (isset($user['errors']) || isset($user['errorEmail']) || isset($user['errorPassword'])) {
      return $this->render('auth/login', 
      ['errors' => $user,
      'old' => $data['email']
      ]
    );
  }
  $user = $user['user'];
  Session::set('id',$user->getId());
  Session::set("username" , $user->getUsername());
  Session::set("email" , $user->getEmail());
  Session::set("role" , $user->getRole());
  
    if ($user->getRole() == 2) {
        $this->cartServise->associateCartAfterLogin($user->getId());
        $this->response->redirect('customer/account');
    } else {
        $this->response->redirect('admin');
    }
  
    



}

public function loginGoogle(Request $request) {
  $postData = $request->getBody();
  
  $result  = $this->authService->loginGoogle($postData);
  $user = $result['user']; 
  Session::set('id',$user->getId()); 
  Session::set("username" , $user->getUsername());
  Session::set("email" , $user->getEmail());
  Session::set("role" , $user->getRole());
  $this->cartServise->associateCartAfterLogin($user->getId());

  $this->response->redirect('customer');

}



}