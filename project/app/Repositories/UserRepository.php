<?php

namespace App\Repositories;
use App\Models\User;
use App\Models\Role;
use PDO;

class UserRepository extends BaseRepository {

    private $table = "users";

    public function createUser($data) {
        $role = new Role(2,"customer");
        $roleId = $role->getId();
        $userdata = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role_id' => $roleId
        ];
        
        
        $create_user = $this->insert($this->table,$userdata );
        
        if($create_user){
            $user = new User($data['username'],$data['email'],$data['password'],$roleId,$create_user);
            return $user;
        }

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
          
         
          return ['user' => new User($user->username, $user->email, $user->password, $user->role_id ,$user->id)];
        
    }

   
}