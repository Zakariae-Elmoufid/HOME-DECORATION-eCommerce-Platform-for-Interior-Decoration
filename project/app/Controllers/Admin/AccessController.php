<?php

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\AdminRepository;
use App\Services\AuthService;


class AccessController {
     
    private $response;
    private $adminRepository;
    private $authService;
    public function __construct(){
        $this->response = new Response();
        $this->adminRepository = new AdminRepository();
        $this->authService = new AuthService();
    }
    
    public function index(){
        $admins = $this->adminRepository->fetchAdmin();
        $this->response->render('admin/adminAccess/index',["admins" => $admins]);
    }
    
    public function create(){
        $permissions = $this->adminRepository->fetchPermission();
        $this->response->render('admin/adminAccess/create',["permissions" => $permissions]);

    }
     
    public function addAdmin(Request $request){
     $data = $request->getbody();
     $permission = $data['permissions'];
     unset($data['permissions']);
     $permissions = $this->adminRepository->fetchPermission();

     $user = $this->authService->register($data,1);
     if (is_array($user) && isset($user['errors'])) {
        return $this->response->render('admin/adminAccess/create', 
        ['errors' => $user['errors'],
        'old' => $user['old'],
        'permissions' => $permissions
        ]
    );
    }  

     if($user){
         $this->adminRepository->addAdminPermession($user->getId(),$permission);
         $this->response->render('admin/adminAccess/index');
     }
    }

    public function updateStatusAdmin(Request $request){
        $data = $request->getbody();
        $id = $data['id'];
        $isUpdate = $this->adminRepository->updateStatus($id);
        if($isUpdate){
          $this->response->redirect('/admin/access');
        }
    }

}    