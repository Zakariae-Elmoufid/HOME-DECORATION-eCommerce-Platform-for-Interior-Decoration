<?php

namespace App\Repositories;

use PDO;
use DateTime;

class CustomerRepository extends BaseRepository {


public function fetchAllCustomer(){
        $stmt = $this->query("SELECT  
            u.email,
            u.username,
            u.created_at,
            ua.phone,
            COUNT(o.id) AS total_order,
            SUM(o.totalAmount) AS spending
                FROM users u
                LEFT JOIN user_addresses ua ON ua.user_id = u.id
                INNER JOIN orders o ON o.user_id = u.id
                WHERE u.role_id = 2
                GROUP BY 
            u.email,
            u.username,
            u.created_at,
            ua.phone;
        ");
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $customers;
    }

    public function total_customers(){
        $stmt = $this->query("select count(*) as total from orders ");
        $countCustomers =  $stmt->fetch(PDO::FETCH_OBJ);
        return $countCustomers->total;
    }
    
    public function new_customers(){
        $month = date('m');
        $year = date('Y');
        $stmt = $this->query("select count(*) as total from orders
         WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year",   [
            'month' => $month,
            'year' => $year
        ]);
        $countCustomers =  $stmt->fetch(PDO::FETCH_OBJ);
        return $countCustomers->total;
    }





}