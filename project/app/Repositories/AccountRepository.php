<?php 

namespace App\Repositories;
use PDO;
use App\Models\UserAddress;
class AccountRepository  extends BaseRepository{

    private $table = "user_addresses";
    
    

    public function getUserAdress($user_id){
        $stmt = $this->query('SELECT ua.id , ua.address ,ua.city , ua.postal_code ,ua.country , ua.user_id , ua.phone ,u.username , u.email , u.created_at FROM user_addresses ua 
        inner join users u on u.id = ua.user_id
         where ua.user_id = ? 
         ORDER BY id DESC LIMIT 1',[$user_id]);
        $addressUser = $stmt->fetch(PDO::FETCH_OBJ);
        return new UserAddress($addressUser); 
    }

    public function  createUserAddresse($data){
        $user_addresse_id = $this->insert($this->table,$data);
        if($user_addresse_id){
           $data['id'] = $user_addresse_id;
            $userAddress =   new UserAddress($data);
            return $userAddress;
        }
     }

    public function updateAddress($id,$data){
        return $this->update($this->table,$id ,$data);
    }
    
}
