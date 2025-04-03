<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Services\CartService;
use App\Repositories\CartRepository;
use App\Models\Cart;

class CartController extends Controller {
    
    private $response;
    private $cartService;
    private $cartRepository;


    public function __construct(){
      $this->response  =  new Response();
      $this->cartService = new CartService();
      $this->cartRepository = new CartRepository();
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
        $this->render("customer/cart",['items' => $items]);
    }

    public function addToCart(Request $request){
      $data = $request->getbody();
  
    
      dump($data);
       $isConnected =  $this->cartService->isConnected();
       if($isConnected){
          $user_id = $isConnected;
          $guest_id = null ; 
       }else{
        $guest_id = $this->cartService->getOrCreateGuestId();
        $user_id = null ; 
       }


       $cartId = $this->cartRepository->searchCartExisting($user_id, $guest_id);
       
       if (!$cartId) {
        // $productPrice = $this->cartRepository->getProductPrice($data['product_id']); 

        // $total = $productPrice * $data['quantity'];
        $cart = [
          'user_id '=> $user_id,
          'session_id' => $guest_id,
          'total' =>$data['totalPrice'] ,
          'quantity' => $data['quantity'],
        ];
        $cartId = $this->cartRepository->createNewCart($cart);
       }
       
       $items = $this->addItems($cartId,$data);
       
    }

    public function addItems($cartId,$data){
      
      $item = [
        'cart_id' => $cartId,
        'product_id' => $data['product_id'],
        'quantity' => $data['quantity'],
        'price' => $data["price"],
        'total_item' => $data['totalPrice'],
        'selected_color' => $data['color'] ?? null,
        'selected_size' => $data['size'] ?? null,
      ];
     $item_id =$this->cartRepository->addCartItem($item);
      if($item_id){
        dump('item add to cart  succussful');
      }
    // Mettre à jour le tota
    }
  

    public function update(Request $request){
      $data = $request->getbody();
      $cartId =$data[0]['cart_id'];
      $total = $data[0]['total'];
     
      foreach ($data as &$item) {
        unset($item["total"]);
        unset($item["cart_id"]);
    }
      
      $cart = $this->cartRepository->updateCart($cartId,$total);
      if($cart){
        $this->updateItem($data);
      }
    }

    public function updateItem($data){
      foreach($data as $item){
        $id = $item['id'];
        unset($item["id"]);
        $cartItem = $this->cartRepository->updateCartItem($id,$item);
      }
    }


}