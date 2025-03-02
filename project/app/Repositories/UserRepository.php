<?php

namespace App\Repositories;
use App\Models\User;
use PDO;

class UserRepository extends BaseRepository {

    private $table = "users";

    public function createUser(User $user) {
        $data = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            'role_id' => $user->getRole()
        ];
        
      
        return $this->insert($this->table,$data );

    }

   
}