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
        dump($items);
        $this->render("customer/cart",$items);
    }

    public function addToCart(Request $request){
      $data = $request->getbody();
  
    
      
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
        $productPrice = $this->cartRepository->getProductPrice($data['product_id']); 

        $total = $productPrice * $data['quantity'];
        $cart = [
          'user_id '=> $user_id,
          'session_id' => $guest_id,
          'total' => $total,
          'quantity' => $data['quantity'],
        ];
        $cartId = $this->cartRepository->createNewCart($cart);
       }
       
       $items = $this->addItems($cartId,$data);
       
    }

    public function addItems($cartId,$data){
      $productPrice = $this->cartRepository->getProductPrice($data['product_id']);
      
      $totalItem = $productPrice * $data['quantity'];
      $item = [
        'cart_id' => $cartId,
        'product_id' => $data['product_id'],
        'quantity' => $data['quantity'],
        'price' => $productPrice,
        'total_item' => $totalItem,
        'selected_color' => $data['color'] ?? null,
        'selected_size' => $data['size'] ?? null,
      ];
     $item_id =$this->cartRepository->addCartItem($item);
      if($item_id){
        dump('item add to cart  succussful');
      }
    // Mettre à jour le tota
    }


}