<?php

namespace App\Services;

use App\Config\Database;
use App\Repositories\OrderRepository;
use App\Repositories\CartRepository;
use App\Repositories\PaymentRepository;
use PDO;
use Exception;

class CheckoutService
{
    private PDO $pdo;
    private  $orderRepository;
    private  $cartRepository;
    private  $paymentRepository;

    public function __construct() {
        $this->pdo = Database::getConnection();
        $this->orderRepositoryo = new OrderRepository();
        $this->cartRepository = new CartRepository();
        $this->paymentController = new PaymentController();
        $this->paymentRepository = new PaymentRepository();
    }

    public function checkout(array $orderData): bool
    {
        try {
            $this->pdo->beginTransaction();

            $user_id = Session::get("id");
            $items = $this->cartRepository->getcartItems($user_id,null);

            $user_addresse = $this->orderRepository->createUserAddresse([
                'user_id' => $user_id,
                'first_name' => $orderData['first_name'],
                'last_name' => $orderData['last_name'],
                'email' => $orderData['email'],
                'phone' => $orderData['phone'],
                'address' => $orderData['address'],
                'city' => $orderData['city'],
                'postal_code' => $orderData['postal_code'],
                'country' => $orderData['country'],
            ]);


            $order = $this->orderRepository->create([
                'shipping_address_id' => $user_addresse->getId(),
                'user_id' => $user_addresse->getUserId(),
                'shipping' => $orderData['shipping_method'],
                'status' => 'pending',
                'subTotal' =>  $orderData['subTotal'],
                'totalAmount' =>  $orderData['totalAmount'],
                'orderDate' => date('Y-m-d'),
                'comment' => $orderData['comments'] ?? null,
            ]);
             

            foreach($orderData['items'] as $itemId) {

                $cartItem = null;
                foreach($items as $item) {
                    if($item->getCartItemId() == $itemId) {
                        $cartItem = $item;
                        break;
                    }
                }
                //  if the item is find ,create a order_item
             
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
               $deltedItem = $this->cartRepository->deleteCartItem($item->getCartItemId()) ;
            
            }

         
            $this->paymentController->createIntent([
                "order_id" => $order->getId(), 
                'email' => $orderData['email']
            ]);




 
            $payment =$this->paymentRepo->processPayment($order->id, $order->total);


            $this->pdo->commit();
            return $payment;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e; // tu peux logger ou gérer l’erreur ici
        }
    }
}
