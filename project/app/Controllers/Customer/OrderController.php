<?php

namespace App\Controllers\Customer;

use App\repositories\OrderRepository;
use App\repositories\CartRepository;
use App\Services\OrderServise;
use App\Services\CartService;
use App\Core\controller;
use App\Core\Response;
use App\Core\Request;

class OrderController extends Controller {

    private $orderService ;
    private $cartService ;
    private $orderRepository ;
    private $cartRepository ;
    private $response ;

    public function __construct(){
        // $this->orderService = new OrderServise;
        // $this->orderRepository = new OrderRepository;
        $this->cartRepository = new CartRepository();
        $this->cartService = new CartService();
        $this->response = new Response;
        
    }

    public function index(){
        $isConnected =  $this->cartService->isConnected();
        if($isConnected){
           $user_id = $isConnected;
           $guest_id = null ; 
        }else{
         $guest_id = $this->cartService->getOrCreateGuestId();
         $user_id = null ; 
        }
 
        $items = $this->cartRepository->getcartItems($user_id, $guest_id);
        $this->response->render('customer/order',["items" => $items]);
    }



}