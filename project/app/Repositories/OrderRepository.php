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
         us.first_name,
        us.last_name,
        us.email,
        us.phone
         FROM orders o
        inner join user_addresses us on us.id = o.shipping_address_id 
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
          oi.selectedColor,
          oi.selectedSize,
          oi.total_item,
          p.title as productTitle,
          pg.image_path as productImage 
        from order_items oi
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


    public function getOrderStock($orderId)
    {
        try {
            $this->conn->beginTransaction();
    
            $stmt = $this->query('SELECT 
                oi.id,
                oi.product_id,
                oi.quantity,
                oi.selectedColor as selected_color,
                oi.selectedSize as selected_size,
                p.stock,
                ps.id as size_id,
                ps.stock_quantity as stock_size,
                ps.size_name,
                pc.id as color_id,
                pc.stock_quantity as stock_color,
                pc.color_name
                FROM orders o
                INNER JOIN order_items oi ON o.id = oi.order_id
                INNER JOIN Products p ON p.id = oi.product_id
                INNER JOIN Product_sizes ps ON ps.product_id = p.id AND ps.size_name = oi.selectedSize
                INNER JOIN Product_colors pc ON pc.product_id = p.id AND pc.color_name = oi.selectedColor
                WHERE oi.order_id = ?', [$orderId]);
    
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($items as $item) {
                $quantity = (int) $item['quantity'];
    
                if (
                    $item['stock'] < $quantity ||
                    $item['stock_size'] < $quantity ||
                    $item['stock_color'] < $quantity
                ) {
                    throw new Exception("Stock insuffisant pour le produit ID: {$item['product_id']}");
                }
    
                $this->update("Products", $item['product_id'], [
                    "stock" => $item['stock'] - $quantity
                ]);
    
                $this->update("Product_sizes", $item['size_id'], [
                    "stock_quantity" => $item['stock_size'] - $quantity
                ]);
    
                $this->update("Product_colors", $item['color_id'], [
                    "stock_quantity" => $item['stock_color'] - $quantity
                ]);
            }
    
            $this->conn->commit();
            return true;
    
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Erreur : " . $e->getMessage();
            return false;
        }
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
            oi.selectedColor,
            oi.selectedSize,
            oi.total_item,
          
            p.title AS productTitle,
            pg.image_path AS productImage
            FROM order_items oi
            INNER JOIN Products p ON oi.product_id = p.id
            INNER JOIN Product_images pg ON pg.product_id = p.id AND pg.is_primary = 1
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
        $stmt = $this->query('SELECT COUNT(id) AS total FROM orders where `status` = "completed"');
        $total = $stmt->fetch(PDO::FETCH_OBJ); 
        return $total->total;
    }

    public function pendingOrders() {
        $stmt = $this->query('SELECT COUNT(id) AS total FROM orders where `status` = "pending"');
        $total = $stmt->fetch(PDO::FETCH_OBJ); 
        return $total->total;
    }

    public function totalRevenue() {
        $stmt = $this->query("SELECT SUM(totalAmount) AS totalRevenue FROM orders WHERE `status` = 'completed'");
        $totalRevenue = $stmt->fetch(PDO::FETCH_OBJ); 
        return $totalRevenue->totalRevenue; 
    }

    public function getMonthlyData($year){
        $stmt = $this->query("SELECT  MONTH(created_at) as month, SUM(totalAmount) as total
        FROM orders  WHERE YEAR(created_at) = ? AND status = 'completed'
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
        FROM orders where status  ='completed'");
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
                FROM Product_images pi 
                WHERE pi.product_id = p.id AND pi.is_primary = 1  
                LIMIT 1
            ) AS image_path,
            COUNT(oi.id) AS units_sold,
            SUM(oi.quantity) AS total_quantity,
            ROUND(
                SUM(oi.quantity) * 100.0 / (
                    SELECT MAX(total) FROM (
                        SELECT SUM(oi2.quantity) AS total
                        FROM Products p2
                        JOIN order_items oi2 ON p2.id = oi2.product_id
                        JOIN orders o2 ON oi2.order_id = o2.id 
                        WHERE o2.created_at BETWEEN :start_date AND :end_date
                        AND o2.status = 'completed'
                        GROUP BY p2.id
                    ) AS sub
                ), 2
            ) AS percentage
        FROM 
            Products p
        JOIN 
            order_items oi ON p.id = oi.product_id
        JOIN 
            orders o ON oi.order_id = o.id
        WHERE 
            o.created_at BETWEEN :start_date AND :end_date
            AND o.status = 'completed'
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
                  AND  status = 'completed'"
                 );
        return $stmt->fetchColumn() ;        
    }

    public function prevMonth(){
        $stmt = $this->query("SELECT SUM(totalAmount) as month_total  FROM orders 
        WHERE status = 'completed' 
        AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
         AND  status = 'completed'");
        return $stmt->fetchColumn();        
    }

    public function currentMonthOrder(){
        $stmt = $this->query("SELECT COUNT(*) as month_total FROM orders 
            WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
             AND  status = 'completed'");
        return $stmt->fetchColumn();     
    }

    public function prevMonthOrder(){
        $stmt = $this->query("SELECT count(*)  as month_total FROM orders 
             WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
             AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
             AND  status = 'completed'");
        return $stmt->fetchColumn();     
    }


    public function getAvgOrder(){
        $stmt = $this->query("SELECT AVG(totalAmount) as avg_total FROM orders 
         where  status = 'completed' ");
       return $stmt->fetchColumn();     
    }

    public function currentMonthAvg(){
        $stmt = $this->query("SELECT AVG(totalAmount) as avg_total FROM orders 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
         AND  status = 'completed' ");
        return $stmt->fetchColumn();   
    }

    public function prevMonthAvg(){
        $stmt = $this->query("SELECT AVG(totalAmount)  as avg_total FROM orders 
        WHERE MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
        AND  status = 'completed'");
        return $stmt->fetchColumn();   

    }


    
    
    }


    

    
    

