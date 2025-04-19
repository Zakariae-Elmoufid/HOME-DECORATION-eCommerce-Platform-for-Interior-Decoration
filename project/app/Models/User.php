<?php 

namespace App\Models;


class User {

    private $id;
    private $username;
    private $email;
    private $password;
    private  $role;
    private  $createdAt;

    public function __construct( $username , $email ,$createdAt , $role ,$password=null , $id = null){
      $this->id = $id ;
      $this->username = $username;
      $this->email = $email;
      $this->password = $password;
      $this->role  = $role;
      $this->createdAt  = $createdAt;
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

    public function getRole() {
        return $this->role;
    }
    public function getCreatedAt() {
        return $this->createdAt;
    }


}    