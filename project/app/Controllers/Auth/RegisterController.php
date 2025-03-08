<?php

namespace App\Controllers\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Services\AuthService;
use App\Core\Session;
use App\Core\Response;
Session::start();

class RegisterController extends Controller  {

    private $authService ;

    public function __construct(){
        $this->authService = new AuthService() ; 
    }


    public function index(){
        return $this->render('auth/register');
    }      
    
    public function store(Request $request){ 
        $data = $request->getBody();
        
        
        $result = $this->authService->register($data);
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

    public function registerGoogle(Request $request){
        $postData = $request->getBody();
         $this->authService->registerGoogle($postData);

    }

}