<?php

namespace App\Controllers\Customer;

use App\repositories\OrderRepository;
use App\repositories\CartRepository;
use App\repositories\AccountRepository;
use App\Services\OrderServise;
use App\Services\CartService;
use App\Core\controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Session;

class OrderController extends Controller {

    private $orderService ;
    private $cartService ;
    private $orderRepository ;
    private $cartRepository ;
    private $accountRepository;
    private $response ;

    public function __construct(){
        // $this->orderService = new OrderServise;
        $this->orderRepository = new OrderRepository;
        $this->cartRepository = new CartRepository();
        $this->accountRepository = new AccountRepository();
        $this->cartService = new CartService();
        $this->response = new Response;
        
    }

    public function index(){
        $isConnected =  $this->cartService->isConnected();
        if($isConnected){
           $user_id = $isConnected;
           $guest_id = null; 
        }else{
         $guest_id = $this->cartService->getOrCreateGuestId();
         $user_id = null; 
        }

        $items = $this->cartRepository->getcartItems($user_id, $guest_id);
        $user_addresse = $this->accountRepository->getUserAdress($user_id);
        $this->response->render('customer/order',["items" => $items , "user_addresse" => $user_addresse]);
    }


    public function store(Request $request){
        $data =$request->getbody();
        $isValidet = $this->orderService->validetOrder($data);
        
        if(isset($isValidet["errors"])){
           $this->response->jsonEncode(["errors"  => $isValidet["errors"] , "olddata" => $isValidet['old']]);
        }
        
        $user_id = Session::get("id");
        $items = $this->cartRepository->getcartItems($user_id,null);
        
     

        $user_addresse = $this->orderRepository->createUserAddresse([
            'user_id' => $user_id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
        ]);
         

        
        $order = $this->orderRepository->create([
            'shipping_address_id' => $user_addresse->getId(),
            'user_id' => $user_addresse->getUserId(),
            'shipping' => $data['shipping_method'],
            'status' => 'pending',
            'subTotal' =>  $data['subTotal'],
            'totalAmount' =>  $data['totalAmount'],
            'orderDate' => date('Y-m-d'),
            'comment' => $data['comments'] ?? null,
        ]);
        
        
      

        foreach($data['items'] as $itemId) {

            $cartItem = null;
            foreach($items as $item) {
                if($item->getCartItemId() == $itemId) {
                    $cartItem = $item;
                    break;
                }
            }

            
            if($cartItem) {
            $order_item =   $this->orderRepository->createOrderItem([
                    'order_id' => $order->getId(),
                    'product_id' => $cartItem->getProductId(),
                    'price' => $cartItem->getProductPrice(),
                    'quantity' => $cartItem->getQuantity(),
                    'selectedColor' => $cartItem->getProductColor(),
                    'selectedSize' => $cartItem->getProductSize(),
                    'total_item' => $cartItem->getTotalItems()
                ]);
            }
            $this->cartRepository->deleteCartItem($item->getCartItemId()) ;
            
        }
        return $this->response->jsonEncode([
            'success' => true,
            'order_id' => $order->getId(),
            'total' => $data['totalAmount']
        ]);

    }



    public function show(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
    }    
}