<?php

namespace App\Models;

class Admin extends User {

    private $isSuperAdmin;
    private $permissions = [];
    private $isActive;

    public function __construct($username , $email ,$createdAt , $role ,$password=null , $id=null ,$isActive = null , $isSuperAdmin = false){
        parent::__construct($username , $email ,$createdAt , $role ,$password=null , $id );
        $this->isSuperAdmin = $isSuperAdmin;
        $this->isActive = $isActive;
    }

  
 


    public function setPermissions( $permission): void {
        $this->permissions = array_map('trim', explode(',', $permission));
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public  function  getStatus(): bool {
        return $this->isActive;
    }

    public function hasPermission( $permission): bool {
        return in_array($permission, $this->permissions);
    }

}