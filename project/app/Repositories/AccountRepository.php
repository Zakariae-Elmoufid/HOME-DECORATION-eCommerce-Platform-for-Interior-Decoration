<?php 

namespace App\Repositories;
use PDO;
use App\Models\UserAddress;
class AccountRepository  extends BaseRepository{

    private $table = "user_addresses";
    
    

    public function getUserAdress($user_id){
        $stmt = $this->query('SELECT * FROM user_addresses where user_id = ?   ORDER BY id DESC LIMIT 1',[$user_id]);
        $addressUser = $stmt->fetch(PDO::FETCH_OBJ);
        return new UserAddress($addressUser); 
    }

    public function updateAddress($id,$data){
        return $this->update($this->table,$id ,$data);
    }
    
}
