<?php 
namespace App\Services;
require_once __DIR__.'/../../vendor/autoload.php';


use App\Models\User;
use App\Repositories\UserRepository;
use App\Core\Validator;
use App\Core\Session;
use App\Core\Response;
use Google_Client;


Session::start();

class AuthService {
   
    private $response;
    private $userRepository;

    public function __construct(){
        $this->response = new Response();
        $this->userRepository = new UserRepository();

    }

  

    public function register($data) {

        $errors = [];
        $validator = new Validator($data);

        $validator->setRules([
            'username' => 'required|min:8|max:50',
            'email' => 'required|email',
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

        // $userRepository = new UserRepository();
        
        $this->userRepository->createUser($data);

        return  [
            'success' => 'Données créées avec succès'
        ];

    }

    public function findUser($data){
        $errors = [];
        // $validator = new Validator($data);

        $validator->setRules([
            'email' => 'required|email',
            'password' => 'required|min:8|max:100'
        ]);

        if (!$validator->validate()) {
            $errors = $validator->getErrors();
            return  [
                'errors' => $errors,
                'old' => $oldData
            ]; 
        }

        // $userRepository = new UserRepository();
        $result = $this->userRepository->findUser($data);
          
        if (isset($result['errorEmail'])) {
            return $result['errorEmail']; 
        }
        
        if (isset($result['errorPassword'])) {
            return $result['errorPassword']; 
        }
        
        // $response = new Response();
        $user = $result['user']; 
        
        if ($user->getRole() == 2) {
            $this->response->redirect('customer');
        } else {
            $this->response->redirect('admin');
        }

    }

    public function google($postData){
        $credential = $postData['credential'] ?? null;

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

            $client_id = '4542774844-43fkmsis3a9m16u9l1jes9htn0cb2gc6.apps.googleusercontent.com' ;
            $client = new Google_Client(['client_id' => $client_id]);  // Specify the CLIENT_ID of the app that accesses the backend
                $id_token = $credential;
                $user  = $client->verifyIdToken($id_token);

                if ($user) {
                $userid = $user['sub'];
               
                } else {
                // Invalid ID token
            }

    }




}