<?php

namespace App\Controllers\Admin;

use App\Core\Session;
use App\Repositories\AdminRepository;

class BaseControllerAdmin {
    protected $adminRepository;

    public function __construct(){
        $this->adminRepository = new AdminRepository();
    }


    public  function getCurrentAdmin(){
            Session::start();
        
        if (Session::get('id') === null) {
            header('Location: /login');
            exit;
        }
        $id = Session::get('id');
        return $this->adminRepository->getAdminById($id);
    }
}