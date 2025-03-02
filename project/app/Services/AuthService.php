<?php 

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Core\Validator;
use App\Core\Session;
Session::start();

class AuthService {

    // private $userRepository;
    
    // public function __construct(UserRepository $userRepository) {
    //     $this->userRepository = $userRepository;
    // }

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

        return  [
            'success' => 'Données créées avec succès'
        ];









      
    }
}