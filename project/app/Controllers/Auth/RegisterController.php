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
    private $response;

    public function __construct(){
        $this->authService = new AuthService() ; 
        $this->response = new Response;
    }


    public function index(){
        return $this->render('auth/register');
    }      
    
    public function store(Request $request){ 
        $data = $request->getBody();
        
        
        $user = $this->authService->register($data);
        if (is_array($user) && isset($user['errors'])) {
            return $this->render('auth/register', 
            ['errors' => $user['errors'],
            'old' => $user['old']
            ]
        );
        }  

        if($user){
            Session::set("username" , $user->getUsername());
            Session::set("email" , $user->getEmail());
            Session::set("role" , $user->getRole());
            Session::set('id',$user->getId());
            $this->response->redirect('customer');
        }
        

          $this->redirect('/login');


    }

    public function registerGoogle(Request $request){
        $postData = $request->getBody();
        $user = $this->authService->registerGoogle($postData);
            if($user){
                Session::set('id',$user->getId());
                Session::set("username" , $user->getUsername());
                Session::set("email" , $user->getEmail());
                Session::set("role" , $user->getRole());
                $this->response->redirect('customer');
            }
            $this->response->redirect('customer');
        
    }

}