<?php

namespace App\Repositories;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\OrderItem;


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
    //   $orderItem = [];
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


    public function 
    
}
