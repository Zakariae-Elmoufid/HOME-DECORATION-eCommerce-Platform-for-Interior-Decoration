<?php

namespace App\Repositories;
use App\Core\Session;
use App\Models\Admin;
use PDO;

class AdminRepository extends BaseRepository{

    private $table = "permissions";
    private $permissionsTable = "permissions";
    private $adminPermissionsTable = "admin_permissions";
    private $usersTable = "users";
    
    public function fetchPermission(){
        return $this->getAll($this->permissionsTable);
    }

    public function addAdminPermession($userId,$permissions){
        foreach($permissions as $permissionId){
             $this->insert("admin_permissions",['user_id' => $userId ,'permission_id' =>$permissionId ]);
        }
    }

    public function fetchAdmin(){
        $admin_id = Session::get('id');
        $stmt = $this->query("SELECT u.id,
        u.username, u.email, u.status,u.created_at ,GROUP_CONCAT(p.name) AS permissions
        FROM users u
        INNER JOIN admin_permissions ap ON ap.user_id = u.id
        INNER JOIN permissions p ON p.id = ap.permission_id
        WHERE u.role_id = 1 AND u.id != ?
        GROUP BY u.id, u.username, u.email
        ",[$admin_id ]);
        $admins = $stmt->fetchAll(PDO::FETCH_OBJ);
        $adminInstances = [];
        foreach ($admins as $admin) {
            
            $adminInstance = new Admin($admin->username, $admin->email,$admin->created_at, 1, null, $admin->id, $admin->status);
            $adminInstance->setPermissions($admin->permissions);
            $adminInstances[] = $adminInstance;
        }

        return $adminInstances;

    }

    public function updateStatus($id) {
        try {
            $admin = $this->findById("users", $id);
    
            if (!$admin) {
                throw new Exception("Admin not found.");
            }
    
            $newStatus = ($admin->status == 1) ? 0 : 1;  
            
            $updateSuccess = $this->update("users", $id, ["status" => $newStatus]);
    
            if (!$updateSuccess) {
                throw new Exception("Failed to update status.");
            }
    
            return true;
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage(); 
            return false;
        }
    }

    
    public function getAdminById($id){
      
        try {
            $stmt = $this->query("SELECT u.id, u.username , u.email , u.status , u.role_id , u.created_at , GROUP_CONCAT(p.name) AS permissions 
            from users u 
            LEFT JOIN  admin_permissions ap ON ap.user_id = u.id
            LEFT JOIN  permissions p ON p.id = ap.permission_id
           WHERE u.role_id = 1 AND u.id = ?
            ", [$id]);
            $admin  = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$admin) {
                throw new Exception("Admin not found.");
            }

            $adminInstanse = new Admin($admin->username, $admin->email, $admin->created_at, $admin->role_id, null, $admin->id, $admin->status);
            $adminInstanse->setPermissions($admin->permissions);
            return$adminInstanse ;
        } catch (Exception $e) {
            
            echo "Error: " . $e->getMessage(); 
            return false;
        }
    }

    public function updateAdminPermession($id ,$permissions){
        $this->query('DELETE FROM admin_permissions WHERE user_id = ?', [$id]); 
                foreach($permissions as $permissionId){
            $this->insert("admin_permissions",['user_id' => $id ,'permission_id' =>$permissionId ]);
         }
         return true;

    }

    public function deleteAdmin($id){
        $isDelete = $this->delete("users", $id);
       if(!$isDelete){
        return ["error" => $isDelete];
       }
       $this->query('DELETE FROM admin_permissions WHERE user_id = ?', [$id]); 
       return  ["succes" => $isDelete];
    }
    
    

}