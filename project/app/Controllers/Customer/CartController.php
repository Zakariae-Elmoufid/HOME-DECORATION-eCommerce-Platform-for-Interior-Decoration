<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Services\CartService;
use App\Repositories\CartRepository;

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
        $this->render("customer/cart");
    }

    public function addToCart(Request $request){
      $data = $request->getbody();
  
    
      
       $isConnected =  $this->cartService->isConnected();
       if($isConnected){
          $user_id = $isConnected;
          $guestId = null;
       }else{
        $guestId = $this->cartService->getOrCreateGuestIdentifier();
        $user_id = null;
       }

       $cartId = $this->cartRepository->searchCartExisting($userId, $sessionId);

    }
}