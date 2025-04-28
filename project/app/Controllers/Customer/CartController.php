<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
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
  
    
       $cart_id =  $this->cartService->cartId();
       $items = $this->addItems($cart_id,$data);
       
    }

    public function addItems($cartId,$data){
      
      $item = [
        'cart_id' => $cartId,
        'product_id' => $data['product_id'],
        'quantity' => $data['quantity'],
        'price' => $data["price"],
        'total_item' => $data['totalPrice'],
        'variant_id' => $data['variant'] ?? null,
      ];
     $item_id =$this->cartRepository->addCartItem($item);
 
      if($item_id){
        $this->response->jsonEncode([ "success" => "item add to cart  succussful" ]);
      }
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
        $cartItem[] = $this->cartRepository->updateCartItem($id,$item);
      }
      $this->countItem();
      $this->response->jsonEncode([ "success" => "item update to cart  succussful" ]);

   }

   public function delete(Request $request){
    $data = $request->getbody();
    $id = $data['id'];
    $cartItem = $this->cartRepository->deleteCartItem($id);
    $this->response->jsonEncode([ "success" => "item delete to cart  succussful"]);

   }

   public function countItem(){
    $cart_id =  $this->cartService->cartId();
   $result= $this->cartService->countItem($cart_id);
   $this->response->jsonEncode(["count" => $result ]);
   }


 



}