<?php

namespace App\Services;

use App\Models\Product;
// use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;



class StockService {

    private $productRepository;
    private $orderRepository;

    public function __construct(){
        // $this->ProductRepository = new ProductRepository();
        $this->orderRepository = new OrderRepository();
    }

    public function stockManagement($orderId){
       return  $this->orderRepository->getOrderStock($orderId);
    }

}