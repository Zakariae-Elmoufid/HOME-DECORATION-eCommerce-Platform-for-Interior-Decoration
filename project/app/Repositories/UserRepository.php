<?php

namespace App\Repositories;
use App\Models\User;
use PDO;

class UserRepository extends BaseRepository {

    private $table = "users";

    public function createUser($data) {
        $user = new User($data['username'],$data['email'],$data['password'],2);
        
        $userdata = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            'role_id' => $user->getRole()
        ];
        
      
        return $this->insert($this->table,$userdata );

    }


    public function findUser($data){

          $user = $this->findBy($this->table , ['email' => $data['email']]);
          $errors = [];

          if (!$user) {
              $errors['errorEmail'] = 'This email was not found!';
          } elseif (!password_verify($data['password'], $user->password)) {
              $errors['errorPassword'] = 'Incorrect password!';
          }
      
          if (!empty($errors)) {
              return $errors;
          }
      
          return ['user' => new User($user->username, $user->email, $user->password, $user->role_id)];
        
    }

    public function findUserByEmail($email){
        return $this->findBy($this->table , ['email' => $email]);
    }

   
}