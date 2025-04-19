<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\response;
use App\Repositories\OrderRepository;


class OrderController {
     
    private $orderRepository;
    private $response;

    public function __construct(){
      $this->orderRepository = new OrderRepository();
      $this->response = new Response();
    }

    public function index(){
        $orders = $this->orderRepository->fetchAll();
        $this->response->render('admin/order/index',  ['orders' => $orders]);
    }


}