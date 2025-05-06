<?php

namespace App\Services;

use App\Models\Product;
// use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;



class StockService {

    private $orderRepository;

    public function __construct(){
        $this->orderRepository = new OrderRepository();
    }

    public function stockManagement($orderId){
       return  $this->orderRepository->getOrderStock($orderId);
    }

}