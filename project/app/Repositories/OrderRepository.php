<?php

namespace App\Repositories;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Core\Session;
use PDO;
use Exception;

class OrderRepository  extends BaseRepository {


    private $table = "orders";

    public function fetchAllOrder(){
        $stmt = $this->query("SELECT 
        o.id ,
        o.status,
        o.totalAmount,
        o.created_at,
        o.subTotal,
        o.shipping,
        u.username,
        u.email,
        us.phone,
         FROM orders o
        inner join user_addresses us on us.id = o.shipping_address_id 
        inner join users u on us.id = u.id
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);

       return $orders;
    }

 
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
        return new Order((array) $orderData);
    }
    

    public function getOrderItems(int $orderId){
       $stmt = $this->query("SELECT 
          oi.id,
          oi.order_id , 
          oi.product_id as productId ,
          oi.quantity,
          oi.price,
          oi.variant_id,
          oi.total_item,
          p.title as productTitle,
          pg.image_path as productImage,
          pv.size_name, 
          pv.color_name
          from order_items oi
          inner join products p on  oi.product_id = p.id
          inner join product_images pg on  pg.product_id = p.id AND pg.is_primary = 1
          left join product_variants pv on oi.variant_id = pv.id
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
        if (!$userAddressData) {
            return null; 
        }
    
        $user = $this->findById('users', $userAddressData->user_id);
    
        if ($user) {
            $userAddressData->username = $user->username;
            $userAddressData->email = $user->email;
        }
    
        $userAddress = new UserAddress($userAddressData);
    
        $userAddressData=$user;
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




    public function fetchAllOrderItems($orderId) {
        // 1. Get order info
        $orderStmt = $this->query("SELECT 
            o.id,
            o.status,
            o.totalAmount,
            o.created_at,
            o.subTotal,
            o.shipping,
            us.first_name,
            us.last_name,
            us.email,
            us.phone
            FROM orders o
            INNER JOIN user_addresses us ON us.id = o.shipping_address_id
            WHERE o.id = ?", [$orderId]);
    
        $order = $orderStmt->fetch(PDO::FETCH_OBJ);
    
        if (!$order) {
            return null; 
        }
    
        $itemsStmt = $this->query("SELECT 
            oi.id,
            oi.order_id,
            oi.product_id AS productId,
            oi.quantity,
            oi.price,
            oi.variant_id,
            oi.total_item,
          
            p.title AS productTitle,
            pg.image_path AS productImage
            FROM order_items oi
            INNER JOIN Products p ON oi.product_id = p.id
            INNER JOIN product_images pg ON pg.product_id = p.id AND pg.is_primary = 1
            WHERE oi.order_id = ?", [$orderId]);
    
        $order->items = $itemsStmt->fetchAll(PDO::FETCH_OBJ);
    
        return $order;
    }

    public function countTotal() {
        $stmt = $this->query('SELECT COUNT(id) AS total FROM orders');
        $total = $stmt->fetch(PDO::FETCH_OBJ); 
        return $total->total;
    }
    
    public function completedOrders() {
        $stmt = $this->query('SELECT COUNT(id) AS total FROM orders where `status` = "delivered"');
        $total = $stmt->fetch(PDO::FETCH_OBJ); 
        return $total->total;
    }

    public function pendingOrders() {
        $stmt = $this->query('SELECT COUNT(id) AS total FROM orders where `status` = "pending"');
        $total = $stmt->fetch(PDO::FETCH_OBJ); 
        return $total->total;
    }

    public function totalRevenue() {
        $stmt = $this->query("SELECT SUM(totalAmount) AS totalRevenue FROM orders WHERE `status` = 'delivered'");
        $totalRevenue = $stmt->fetch(PDO::FETCH_OBJ); 
        return $totalRevenue->totalRevenue; 
    }

    public function getMonthlyData($year){
        $stmt = $this->query("SELECT  MONTH(created_at) as month, SUM(totalAmount) as total
        FROM orders  WHERE YEAR(created_at) = ? AND status = 'delivered'
        GROUP BY MONTH(created_at) 
        ORDER BY month" ,[$year]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $monthlyData = array_fill(1, 12, 0);
        foreach ($results as $row) {
            $monthlyData[$row['month']] = (float) $row['total'];
        }
        return $monthlyData;
    }

    public function getAvailableYears(){
        $stmt = $this->query("SELECT DISTINCT YEAR(created_at) as year 
        FROM orders where status  ='delivered'");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getPopularProducts($period){
        $startDate = ($period === 'current') ? date('Y-m-01') :  date('Y-m-01', strtotime('first day of last month'));
       $endDate = ($period === 'current') ? date('Y-m-d') : date('Y-m-t', strtotime('last day of last month'));

       $stmt = $this->query(
        "SELECT 
            p.id, 
            p.title, 
            p.base_price, 
            (
                SELECT pi.image_path 
                FROM product_images pi 
                WHERE pi.product_id = p.id AND pi.is_primary = 1  
                LIMIT 1
            ) AS image_path,
            COUNT(oi.id) AS units_sold,
            SUM(oi.quantity) AS total_quantity,
            ROUND(
                SUM(oi.quantity) * 100.0 / (
                    SELECT MAX(total) FROM (
                        SELECT SUM(oi2.quantity) AS total
                        FROM products p2
                        JOIN order_items oi2 ON p2.id = oi2.product_id
                        JOIN orders o2 ON oi2.order_id = o2.id 
                        WHERE o2.created_at BETWEEN :start_date AND :end_date
                        AND o2.status = 'delivered'
                        GROUP BY p2.id
                    ) AS sub
                ), 2
            ) AS percentage
        FROM 
            products p
        JOIN 
            order_items oi ON p.id = oi.product_id
        JOIN 
            orders o ON oi.order_id = o.id
        WHERE 
            o.created_at BETWEEN :start_date AND :end_date
            AND o.status = 'delivered'
        GROUP BY 
            p.id, p.title, p.base_price
        ORDER BY 
            total_quantity DESC
        LIMIT 4",
        ['start_date' => $startDate, 'end_date' => $endDate]
     );
    
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $products;
    }

    public function currentMonth(){
        $stmt = $this->query("SELECT SUM(totalAmount) as month_total  FROM orders 
                 WHERE status = 'completed' 
                 AND MONTH(created_at) = MONTH(CURRENT_DATE())
                 AND YEAR(created_at) = YEAR(CURRENT_DATE())
                  AND  status = 'delivered'"
                 );
        return $stmt->fetchColumn() ;        
    }

    public function prevMonth(){
        $stmt = $this->query("SELECT SUM(totalAmount) as month_total  FROM orders 
        WHERE status = 'delivered' 
        AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
         AND  status = 'delivered'");
        return $stmt->fetchColumn();        
    }

    public function currentMonthOrder(){
        $stmt = $this->query("SELECT COUNT(*) as month_total FROM orders 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
             AND  status = 'delivered'");
        return $stmt->fetchColumn();     
    }

    public function prevMonthOrder(){
        $stmt = $this->query("SELECT count(*)  as month_total FROM orders 
             WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
             AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
             AND  status = 'delivered'");
        return $stmt->fetchColumn();     
    }


    public function getAvgOrder(){
        $stmt = $this->query("SELECT AVG(totalAmount) as avg_total FROM orders 
         where  status = 'delivered' ");
       return $stmt->fetchColumn();     
    }

    public function currentMonthAvg(){
        $stmt = $this->query("SELECT AVG(totalAmount) as avg_total FROM orders 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
         AND  status = 'delivered' ");
        return $stmt->fetchColumn();   
    }

    public function prevMonthAvg(){
        $stmt = $this->query("SELECT AVG(totalAmount)  as avg_total FROM orders 
        WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND  status = 'delivered'");
        return $stmt->fetchColumn();   

    }



    public function getOrderItemQuantity($order_id){
      $stmt  = $this->query("SELECT quantity , product_id, variant_id from order_items where order_id = ?",[$order_id]);
      return $stmt->fetchAll(PDO::FETCH_OBJ);

    }

    public function currentStock($variant_id){
        $stmt = $this->query("SELECT stock_quantity FROM product_variants WHERE id = ?", [$variant_id]);
        return   $stmt->fetchColumn();
    }

    public function updateQuantity($variant_id,$data){
      return  $this->update('product_variants',$variant_id,$data);
    }

    public function calculateTotalVariantStock($product_id){
    $stmt = $this->query(
        "SELECT SUM(stock_quantity) as total_stock 
         FROM product_variants 
         WHERE product_id = ?", 
        [$product_id]
    );

    $result = $stmt->fetch(PDO::FETCH_OBJ);

    return $result ? (int) $result->total_stock: 0;
    }

    public function updateProductStock($id,$data){
       return $this->update('products',$id,$data);
    }

    public function updateOrderStatus($order_id,$data){
       return $this->update('orders',$order_id,$data);
    }
    


    
    
    }


    

    
    

