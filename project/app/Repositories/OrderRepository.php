<?php

namespace App\Repositories;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Core\Session;
use PDO;

class OrderRepository  extends BaseRepository {

    

    public function  createUserAddresse($data){
       $user_addresse_id = $this->insert("user_addresses",$data);
       if($user_addresse_id){
          $data['id'] = $user_addresse_id;
           $userAddress =   new UserAddress($data);
           return $userAddress;
       }
    }

    public function create($data){
        $order_id = $this->insert("orders",$data);
        if($order_id){
         $data['id'] = $order_id;
          $order = new Order($data);
          return $order;
        }  
    }

    public function createOrderItem($item){
      $orderItem = [];
        $order_items_id = $this->insert("order_items",$item);
        if($order_items_id){
            $item['id'] = $order_items_id;
            $orderItem = new OrderItem($item); 
        }
      return $orderItem;
    }

    public function getOrderById(int $orderId) {
        $orderData  = $this->findById('orders' ,$orderId);
        
        if (!$orderData) {
            return null;
        }
        
        return new Order($orderData);
    }


    public function getOrderItems(int $orderId){
       $stmt = $this->query("SELECT 
          oi.id,
          oi.order_id , 
          oi.quantity,
          oi.price,
          oi.selectedColor,
          oi.selectedSize,
          oi.total_item,

          p.title as productTitle,
          pg.image_path as productImage 
        from order_items oi
    --    inner join orders o on o.id = oi.order_id
       inner join Products p on  oi.product_id = p.id
       inner join Product_images pg on  pg.product_id = p.id AND pg.is_primary = 1
       where oi.order_id = ?
       ",[$orderId]) ;
       $orderItemsData = $stmt->fetchAll(PDO::FETCH_OBJ);
       if (!$orderItemsData) {
           return null;
        }
        $items = [];
      
        foreach ($orderItemsData as $item){
            $items[] = new OrderItem($item); 
        }
        
      return $items;
    }

    public function getUserAddressById($userAdderessId){
        $userAddressData  = $this->findById('user_addresses' ,$userAdderessId);
        $userAddress =   new UserAddress($userAddressData);
        return $userAddress;
    }

    public function getOrderByUserId($userId){
        $stmt =  $this->query("SELECT * FROM  orders o
        WHERE o.user_id  = ? ORDER BY id DESC LIMIT 5",[$userId]) ; 
        $orderData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $orders = [];
        foreach ($orderData as $order) {
            $orders[] = new Order($order);
        }
        return $orders;
    }

    public function getOrderItemByUserId($userId){
        $stmt =  $this->query("SELECT * FROM  orders where user_id = ? ",[$userId]) ; 
        $orderData = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        
        $orders = [];
        $groupedOrders = [];
        foreach ($orderData as  $obj) {
            $orderItems = $this->getOrderItems($obj->id);
            $obj->items = $orderItems; 
            $orders[] = new Order((array)$obj);
        }
        
        return $orders;
    }

    
    
}
