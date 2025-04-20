<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\OrderRepository;


class OrderController {
     
    private $orderRepository;
    private $response;

    public function __construct(){
      $this->orderRepository = new OrderRepository();
      $this->response = new Response();
    }

    public function index(){
        $orders = $this->orderRepository->fetchAllOrder();
        $totalOrder = $this->orderRepository->countTotal();
        $completedOrders = $this->orderRepository->completedOrders();
        $pendingOrders = $this->orderRepository->pendingOrders();
        $totalRevenue= $this->orderRepository->totalRevenue();
        

        $this->response->render('admin/order/index',  ['orders' => $orders ,
        'countOrders' => $totalOrder ,
        "countCompleted" => $completedOrders,
        'countPending' => $pendingOrders,
        'totalRevenue' => $totalRevenue
     ]);
    }
    
    public function orderDetails(Request $request){
     $data = $request->getbody();
     $orderId = $data['id'];
     $order_items = $this->orderRepository->fetchAllOrderItems($orderId ); 

      $this->response->jsonEncode(["order" => $order_items]);

    }


}