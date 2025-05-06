<?php

namespace App\Controllers\Customer;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Repositories\PaymentRepository;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use App\Services\StripeService;
use App\Core\Request;
use App\Core\Response;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentController
{
    private $paymentRepository;
    private $orderRepository;
    private $orderService;
    private $stripeService;
    private $response;

    public function __construct()
    {
        $this->stripeService = new StripeService();
        $this->orderRepository = new OrderRepository();
        $this->response = new Response();
        $this->paymentRepository = new PaymentRepository();
        $this->orderService = new OrderService;
    }

    public function checkout(Request $request){
        $data =$request->getbody();
        $order = $this->orderService->createOrder($data);

        if(isset($order["errors"])){
            $this->response->jsonEncode( $order);
            return;
        }

        if(isset($order["success"])){
            $session = $this->createCheckoutSession($order);
            
        }

        if($session){
            $this->response->jsonEncode( $session);
        }
    
     

    }

    private function createCheckoutSession($order)
    {
      
        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order #' . $order['order_id'], 
                    ],
                    'unit_amount' => $order['total'] * 100, 
                ],
                'quantity' => 1,
            ]
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => 'http://localhost:8080/payment/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:8080/payment/cancel',
        ]);

        $create_payment = $this->paymentRepository->create([
            "order_id" => $order['order_id'],
            "amount" => $order['total'],
            'payment_method' => 'card',
            'stripe_session_id' => $session->id,
            'currency' => 'usd',
            'status' => 'unpaid',
        ]);
            


        return [
            'success' => true,
            'url' => $session->url,
        ];
        

    }








    public function success(Request $request)
    {
   $data = $request->getbody();
    $session_id = $data['session_id'];
    
    if ($session_id) {
    \Stripe\Stripe::setApiKey('sk_test_51RAgELH2nPPbXqXkVPvJUTDRyYguZujueuEi1dbXrR6NA7IrOyKldiiEKdnBpKMA7SrVFElluLNiZOpmsnh8o6F800mpbbJnDJ'); // Remplace par ta clé API privée

    try {
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        
        if ($session->payment_status === 'paid') {
            
            $payment = $this->paymentRepository->findByStripeSessionId($session->id);
            
            if ($payment) {
                $this->paymentRepository->updatePayment($payment->getId(), [
                    'status' => 'paid',
                ]);
                
                $this->orderService->decrementStockAfterOrder($payment->getOrderId());
            }
            $this->response->redirect('/payment/confirmation?id='.$payment->getOrderId());
        } else {
            echo "Payment Failed.";
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo "Error retrieving session: " . $e->getMessage();
    }
    } else {
         echo "Session ID is missing.";
    }    }

    public function cancel()
    {
        echo "Payment canceled!";
    }


    public function confirmation(Request $request){
        $body =  $request->getbody();
        $orderId = $body['id'];
        $order = $this->orderRepository->getOrderById($orderId);
        
        $shippingAddress = $this->orderRepository->getUserAddressById($order->getShippingAddress());
        $payment = $this->paymentRepository->getByOrderId($orderId);
 
        $orderItems = $this->orderRepository->getOrderItems($orderId);
        $this->response->render('customer/payment-confirmation', [
            'order' => $order,
            'orderItems' => $orderItems,
            'shippingAddress' => $shippingAddress,
            'payment' => $payment,
        ]);
      
    }
}

