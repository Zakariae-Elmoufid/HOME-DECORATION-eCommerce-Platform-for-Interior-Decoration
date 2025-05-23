<?php 
namespace App\Services;
require_once __DIR__.'/../../vendor/autoload.php';


use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\AdminRepository;
use App\Core\Validator;
use App\Core\Session;
use App\Core\Response;
use Google_Client;


Session::start();

class AuthService {
   
    private $response;
    private $userRepository;
    private $adminRepository;

    public function __construct(){
        $this->response = new Response();
        $this->userRepository = new UserRepository(); 
        $this->adminRepository  = new AdminRepository();
    }

  

    public function register($data,$role_id) {
        $errors = [];
        $validator = new Validator($data);

        $validator->setRules([
            'username' => 'required|min:6|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:100|confirmed:confirm_password'
        ]);

         
        $oldData = $data;

        if (!$validator->validate()) {
            $errors = $validator->getErrors();

            return  [
                'errors' => $errors,
                'old' => $oldData
            ];
           
            
        }
        
        $user = $this->userRepository->createUser($data,$role_id);
        return $user;

    }

    public function findUser($data){
        $errors = [];
        $validator = new Validator($data);
        
        $validator->setRules([
            'email' => 'required|email',
            'password' => 'required|min:8|max:100'
        ]);
        
        $oldData = $data['email'];
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            return  [
                'errors' => $errors,
                'old' => $oldData
            ]; 
        }

        $result = $this->userRepository->findUser($data);
        
        if (isset($result['errorEmail'])) {
            return ['errorEmail' =>$result['errorEmail']]; 
        }
        
        if (isset($result['errorPassword'])) {
            return ['errorPassword'=> $result['errorPassword']]; 
        }
        return  ['user' => $result['user']]; 
     

    }

    private function verfiyToken($postData){

        $csrf_token_cookie = $_COOKIE['g_csrf_token'] ?? null;

        if (!$csrf_token_cookie) {
            $this->response->statusCode(400);
            die('No CSRF token in Cookie.');
        }

        $csrf_token_body = $postData['g_csrf_token'] ?? null;
        if (!$csrf_token_body) {
            $this->response->statusCode(400);
            die('No CSRF token in post body.');
        }

        if ($csrf_token_cookie !== $csrf_token_body) {
            $this->response->statusCode(400);
            die('Failed to verify double submit cookie.');
        } 
    }

    public function loginGoogle($postData){
        $credential = $postData['credential'] ?? null;

         $this->verfiyToken($postData);
         
            $client_id = '4542774844-43fkmsis3a9m16u9l1jes9htn0cb2gc6.apps.googleusercontent.com' ;
            $client = new Google_Client(['client_id' => $client_id]);  
                $id_token = $credential;
                $user  = $client->verifyIdToken($id_token);
                if ($user) {
                $email = $user['email'];
                $name = $user['name'];
                $google_id = $user['sub'];

                $data = [
                    'email' => $email,
                    'password' => $google_id
                ];
                 
                $result = $this->userRepository->findUser($data);
            
               return $result;
                

                
                } else {
                    $this->response->statusCode(400);
                    die('Invalid token');
                }


    }

    public function registerGoogle($postData){
        $credential = $postData['credential'] ?? null;

         $this->verfiyToken($postData);

         $client_id = '4542774844-43fkmsis3a9m16u9l1jes9htn0cb2gc6.apps.googleusercontent.com' ;
         $client = new Google_Client(['client_id' => $client_id]);  
         $id_token = $credential;
         $user  = $client->verifyIdToken($id_token);
        if ($user){
            $email = $user['email'];
            $name = $user['name'];
            $google_id = $user['sub'];

            $data = [
                'email' => $email,
                'username' => $name,
                'password' => $google_id,
                'confirm_password' => $google_id
            ]; 

            $validator = new Validator($data);
    
            $validator->setRules([
                'email' => 'required|email|unique:users,email',
            ]);
    
            if (!$validator->validate()) {
                $uniqueEmail = $validator->getErrors();
                $this->response->render('auth/register',['unique' => $uniqueEmail]);

            }
    


            $user = $this->userRepository->createUser($data,2);
            return $user;
            
        } else {
            $this->response->statusCode(400);
            die('Invalid token');
        }

    }


    public function updateAdmin($id,$data){
        $errors = [];
        $validator = new Validator($data);

        $validator->setRules([
            'username' => 'required|min:8|max:50',
            'email' => 'required|email',
            // 'password' => 'required|min:8|max:100|confirmed:confirm_password'
        ]);

         
        $oldData = $data;

        if (!$validator->validate()) {
            $errors = $validator->getErrors();

            return  [
                'errors' => $errors,
                'old' => $oldData
            ]; 
        }
        return $this->adminRepository->updateAdmin($id,$data);
   
    }


    public function logout(){
        Session::destroy();
        $this->response->redirect('login');

    }


}