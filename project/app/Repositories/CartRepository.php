<?php 

namespace App\Repositories;
use PDO;
use App\Modeles\CartItem;
class CartRepository  extends BaseRepository{

    private $table = "carts";
    

    public function searchCartExisting($user_id ,$guest_id){
     if($user_id !== null ){
        $id = $user_id;
     }
     if($guest_id !== null){
        $id = $guest_id;
     } 
    $stmt = $this->query("SELECT id FROM carts WHERE user_id = ? or session_id = ?", [$id,$id]);
    return  $stmt->fetchColumn(); 
    }

    public function createNewCart($data){
       return  $this->insert($this->table, $data);
    }

    public function getProductPrice($id){
        $stmt = $this->query("SELECT base_price FROM Products Where id = ?",[$id]);
       return  $stmt->fetchColumn();
    }

    public function addCartItem($item){
        return  $this->insert('cart_items', $item);
    }

    public function getcartItems($user_id, $guest_id){
        if($user_id !== null ){
            $id = $user_id;
         }
         if($guest_id !== null){
            $id = $guest_id;
         } 
        $stmt = $this->query('SELECT 
        c.id AS cart_id, 
        c.total,
        ci.total_item,
        ci.id AS cart_item_id, 
        ci.quantity, 
        p.id AS product_id, 
        p.title AS product_title, 
        p.base_price AS product_price, 
        p.stock,
        pi.image_path AS product_image
         from carts c
         inner join cart_items  ci on ci.cart_id = c.id
         inner join Products  p on ci.product_id = p.id
         left join Product_images  pi on pi.product_id  =  p.id AND pi.is_primary = 1 
         where user_id = ? or session_id = ? ',[$id,$id]);
         $data =  $stmt->fetchAll(PDO::FETCH_OBJ);
         return $data;
         dump($data);
        //  exit;
        // $items = [];
        
        // foreach($data as $item){
        //     $items[] = new CartItem($item);
        // }
        // return $items;
    }



}   