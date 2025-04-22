<?php

namespace App\Repositories;
use App\Core\Session;
use PDO;

class AdminRepository extends BaseRepository{

    private $table = "permissions";
    
    public function fetchPermission(){
        return $this->getAll($this->table);
    }

    public function addAdminPermession($userId,$permissions){
        foreach($permissions as $permissionId){
             $this->insert("admin_permissions",['user_id' => $userId ,'permission_id' =>$permissionId ]);
        }
    }

    public function fetchAdmin(){
        $admin_id = Session::get('id');
        $stmt = $this->query("SELECT u.id,
        u.username, u.email, u.status, GROUP_CONCAT(p.name) AS permissions
        FROM users u
        INNER JOIN admin_permissions ap ON ap.user_id = u.id
        INNER JOIN permissions p ON p.id = ap.permission_id
        WHERE u.role_id = 1 AND u.id != ?
        GROUP BY u.id, u.username, u.email
        ",[$admin_id ]);
        $admins = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $admins;
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
    

}