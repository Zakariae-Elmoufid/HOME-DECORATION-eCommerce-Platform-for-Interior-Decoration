<?php 

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Core\Validator;
use App\Core\Session;
use App\Core\Response;
Session::start();

class AuthService {

  

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

        $userRepository = new UserRepository();
        
        $userRepository->createUser($data);

        return  [
            'success' => 'Données créées avec succès'
        ];

    }

    public function findUser($data){
        $errors = [];
        $validator = new Validator($data);

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

        $userRepository = new UserRepository();
        $result = $userRepository->findUser($data);
          
        if (isset($result['errorEmail'])) {
            return $result['errorEmail']; 
        }
        
        if (isset($result['errorPassword'])) {
            return $result['errorPassword']; 
        }
        
        $response = new Response();
        $user = $result['user']; 
        
        if ($user->getRole() == 2) {
            $response->redirect('customer');
        } else {
            $response->redirect('admin');
        }

    }
}