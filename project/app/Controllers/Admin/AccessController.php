<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Core\Session;
use Exception;
Session::start();

class AccessController  extends BaseControllerAdmin{

    private $authService;
    private $response;

    public function __construct(){
        parent::__construct();
        $this->response = new Response();
        $this->authService = new AuthService();
    }


    

    public function index(){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->renderError('Access denied');
            }
            $admins = $this->adminRepository->fetchAdmin();
            $this->response->render('admin/adminAccess/index', ["admins" => $admins]);
        } catch (Exception $e) {
            $this->response->renderError("Failed to load admin list. \n ". $e->getMessage());
        }
    }

    public function create(){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $permissions = $this->adminRepository->fetchPermission();
            $this->response->render('admin/adminAccess/create', ["permissions" => $permissions]);
        } catch (Exception $e) {
            $this->response->renderError("Failed to load permissions.");
        }
    }

    public function addAdmin(Request $request){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $data = $request->getbody();
            $permission = $data['permissions'];
            unset($data['permissions']);
            $permissions = $this->adminRepository->fetchPermission();

            $user = $this->authService->register($data, 1);

            if (is_array($user) && isset($user['errors'])) {
                return $this->response->render('admin/adminAccess/create', [
                    'errors' => $user['errors'],
                    'old' => $user['old'],
                    'permissions' => $permissions
                ]);
            }

            if ($user) {
                $this->adminRepository->addAdminPermession($user->getId(), $permission);
                $this->response->redirect('/admin/access');
            }
        } catch (Exception $e) {
            $this->response->renderError("Failed to create admin.");
        }
    }

    public function edit(Request $request){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $data = $request->getbody();
            $id = $data['id'];
            $admin = $this->adminRepository->getAdminById($id);
            $permissions = $this->adminRepository->fetchPermission();
            $this->response->render('admin/adminAccess/edit', [
                "permissions" => $permissions,
                "admin" => $admin
            ]);
        } catch (Exception $e) {
            $this->response->renderError("Failed to load edit form.");
        }
    }

    public function update(Request $request){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $data = $request->getbody();
            $id = intval($data['id']);
            unset($data['id']);
            $permission = $data['permissions'];
            unset($data['permissions']);
            $permissions = $this->adminRepository->fetchPermission();

            $admin = $this->authService->updateAdmin($id, $data);

            if (is_array($admin) && isset($admin['errors'])) {
                return $this->response->render('admin/adminAccess/edit', [
                    'errors' => $admin['errors'],
                    'old' => $admin['old'],
                    'permissions' => $permissions
                ]);
            }

                $this->adminRepository->updateAdminPermession($id ,$permission);
                $this->response->redirect('/admin/access');
             
        } catch (Exception $e) {
            $this->response->renderError("Failed to update admin.");
        }
    }

    public function updateStatusAdmin(Request $request){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $data = $request->getbody();
            $id = $data['id'];
            $isUpdate = $this->adminRepository->updateStatus($id);
            if ($isUpdate) {
                $this->response->redirect('/admin/access');
            } else {
                $this->response->renderError("Failed to update status.");
            }
        } catch (Exception $e) {
            $this->response->renderError("Error occurred while updating status.");
        }
    }

    public function delete(Request $request){
        try {
            $currentAdmin = $this->getCurrentAdmin();
            if (!$currentAdmin->hasPermission('Manage Admins')) {
                return $this->response->renderError('Access denied');
            }
            $data = $request->getbody();
            $id = $data['id'];
            $isDelete = $this->adminRepository->deleteAdmin($id);

            if (!$isDelete) {
                $this->response->renderError("Failed to delete admin.");
            } else {
                $this->response->redirect('/admin/access');
            }
        } catch (Exception $e) {
            $this->response->renderError("Error occurred while deleting admin.");
        }
    }

  
}
