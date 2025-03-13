<?php 

namespace App\Models;


class User {

    private $id;
    private $username;
    private $email;
    private $password;
    private $role;

    public function __construct( $username , $email , $password ,$role_id , $id = null){
      $this->id = $id ;
      $this->username = $username;
      $this->email = $email;
      $this->password = $password;
      $this->role  = $role_id;
    } 
    

    public function getId(){
     return $this->id;
    }

    public function getUsername() {
         return $this->username; 
    }
   
    public function getEmail() {
         return $this->email;
    }
    public function getPassword() 
    {
         return $this->password; 
    }
    public function getRole() {
        return $this->role;
    }


}    