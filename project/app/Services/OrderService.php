<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Session;
use App\Core\Response;

use App\Repositories\CartRepository;
use App\Repositories\AccountRepository;
use App\Repositories\OrderRepository;

class OrderService {

  private $cartRepository;
  private $orderRepository;
  private $accountRepository;

  public function __construct(){
    $this->cartRepository = new CartRepository();
    $this->accountRepository = new AccountRepository();
    $this->orderRepository = new OrderRepository();
  }

  public function createOrder($data){
    
    $isValidet = $this->validetOrder($data);
    
    if(isset($isValidet["errors"])){
       return ["errors"  => $isValidet["errors"] , "olddata" => $isValidet['old']];
    }
    
    $user_id = Session::get("id");
    $items = $this->cartRepository->getcartItems($user_id,null);
    
    $checkUserAddress = $this->accountRepository->getUserAdress($user_id);

    if($checkUserAddress->getId() == null ){
        $user_address = $this->accountRepository->createUserAddresse([
            'user_id' => $user_id,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
        ]);
    }else{
       $this->accountRepository->updateAddress($data['id'],[

            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
        ]);
        $user_address = $this->accountRepository->getUserAdress($user_id); 

    }
    
    $order = $this->orderRepository->create([
        'shipping_address_id' => $user_address->getId(),
        'user_id' => $user_id ,
        'shipping' => $data['shipping_method'],
        'status' => 'pending',
        'subTotal' =>  $data['subTotal'],
        'totalAmount' =>  $data['totalAmount'],
        'orderDate' => date('Y-m-d'),
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
                'variant_id' => $cartItem->getVariantId(),
                'total_item' => $cartItem->getTotalItems()
            ]);
        }
        $this->cartRepository->deleteCartItem($item->getCartItemId()) ;
        
    }
    return ['success' => true,'order_id' => $order->getId(),'total' => $data['totalAmount']];

}








  public function validetOrder($data){
       $validator  = new Validator($data);
       $validator->setRules([
        'phone' => 'required|string|min:9|max:20',
        'address' => 'required|min:5|max:100',
        'city' => 'required|min:8|max:50',
        'country' => 'required|min:2|max:50',
        'shipping_method' => 'required',
    ]);

    $oldData = $data;

    if (!$validator->validate()) {
        $errors = $validator->getErrors();
        return  [
            'errors' => $errors,
            'old' => $oldData
        ]; 
    }    
  }


  public function decrementStockAfterOrder($order_id){
    $quantities = $this->orderRepository->getOrderItemQuantity($order_id);
    $countQauntity =  0;
    foreach($quantities as $item) {
        if ($item->variant_id) {
            $currentStock = $this->orderRepository->currentStock($item->variant_id);
            $newStock = $currentStock - $item->quantity;
            $this->orderRepository->updateQuantity($item->variant_id, ['stock_quantity' => $newStock]);
        } 
        $countQauntity += $item->quantity ;
    }
    $currentProductStock =  $this->orderRepository->productStock($quantities[0]->product_id);

    $newStock =$currentProductStock - $countQauntity;
   
        $this->orderRepository->updateProductStock($quantities[0]->product_id, ['stock' => $newStock]);
    
  

    $this->orderRepository->updateOrderStatus($order_id,['status'=>'shipped']);

    return true;   
  }

      
  

    

}










// class StripePaymentController extends Controller
// {

//     public function payment(Request $request)
//     {   
//         Stripe::setApiKey(env('STRIPE_SECRET'));

//         $session = Session::create([
//             'payment_method_types' => ['card'],
//             'line_items' => [
//                 [
//                     'quantity' => 1,
//                     'price_data' => [
//                         'currency' => 'eur',  
//                         'product_data' => [
//                             'name' => $request->titre
//                         ],
//                         'unit_amount' => $request->amount * 100,
//                     ],
//                 ],
//             ],
//             'mode' => 'payment',  
//             'success_url' => route('payment.success'),
//             'cancel_url' => route('payment.cancel'), 
//         ]);

//         payments::create([
//             'email' => $request->Email,
//             'amount' => $request->amount,
//             'currency' => 'Euro',
//             'stripe_session_id' => $session->id,
//             'payment_status' => 'unpaid',
//             'created_at' => now(),
//             'updated_at' => now()
//         ]);

//         return redirect($session->url);

//     }
// }