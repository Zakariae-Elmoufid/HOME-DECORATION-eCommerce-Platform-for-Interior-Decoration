<?php 

namespace App\Repositories;
use PDO;
use App\Models\CartItem;
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
        $stmt = $this->query("SELECT base_price FROM products Where id = ?",[$id]);
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
        ci.variant_id,
        p.id AS product_id, 
        p.title AS product_title, 
        ci.price AS product_price, 
        p.stock,
        pi.image_path AS product_image,
        pv.color_name,
        pv.size_name ,
        pv.stock_quantity 

         from carts c
         inner join cart_items  ci on ci.cart_id = c.id
         inner join products  p on ci.product_id = p.id
         left join product_images  pi on pi.product_id  =  p.id AND pi.is_primary = 1  
         left join product_variants  pv on pv.id  =  ci.variant_id 
        
         where user_id = ? or session_id = ? ',[$id,$id]);
         $data =  $stmt->fetchAll(PDO::FETCH_ASSOC); 
        $items = [];
        
        foreach($data as $item){
            $items[] = new CartItem($item);
        }
        return $items;
    }
    

    public function updateCart($cartId,$total){
      return $this->update($this->table , $cartId, ['total' => $total]);
    }

    public function updateCartItem($id ,$data){
        return $this->update('cart_items' , $id, $data);
    }
    public function deleteCartItem($id){
        return $this->delete('cart_items',$id);
    }
    
    public function countItem($id){
        $stmt = $this->query("SELECT COUNT(id) from cart_items where  cart_id = ? ",[$id]);
        return $stmt->fetchColumn();

    }



    public function getCartIdBySessionId($sessionId) {
        $stmt = $this->query("SELECT id FROM carts WHERE session_id = ? LIMIT 1" , [$sessionId]);
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getCartIdByUserId($userId) {
        $stmt = $this->query("SELECT id FROM carts WHERE user_id = ? LIMIT 1" , [$userId]);
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function mergeCarts($userCartId, $guestCartId){
       
        $stmt = $this->query("SELECT cart_items.id as id  from carts 
        inner join cart_items  on carts.id = cart_items.cart_id
        where carts.id = ? ",[$guestCartId]);
        $items =  $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        foreach($items as $item){
            $id = $item['id'];
            $cartItem = $this->updateCartItem($id,["cart_id" => $userCartId ]);
        }
         return true;

    }

    public function assignCartToUser($guestCartId, $userId){
        return $this->update($this->table , $guestCartId, ['user_id' => $userId ,'session_id' => null]);
    }


}   