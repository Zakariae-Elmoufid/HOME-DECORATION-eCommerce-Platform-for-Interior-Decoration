<?php

namespace App\Controllers\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;
use App\Core\Session;
use App\Core\Response;
Session::start();

class RegisterController extends Controller  {

    

    public function index(){
        return $this->render('auth/register');
    }      
    
    public function store(Request $request){ 
        $data = $request->getBody();
        
        
        $authService = new AuthService;
        
        $result = $authService->register($data);
    if (!empty($result['errors'])) {
        
        return $this->render('auth/register', 
         ['errors' => $result['errors'],
           'old' => $result['old']
         ]
         );
    
    }
    Session::setFlash('success', 'Registration successful. You can now log in.');

    return $this->redirect('/login');
    
    
}

}