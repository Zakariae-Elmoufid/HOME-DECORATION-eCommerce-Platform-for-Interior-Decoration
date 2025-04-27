<?php

namespace App\Controllers\Customer;
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Repositories\PaymentRepository;
use App\Services\OrderService;
use App\Services\StripeService;
use App\Core\Request;
use App\Core\Response;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentController
{
    private $paymentRepository;
    private $orderService;
    private $stripeService;
    private $response;

    public function __construct()
    {
        $this->stripeService = new StripeService();
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
            'payment_intent_id' => $session->payment_intent,
            'stripe_session_id' => $session->id,
            'currency' => 'usd',
             'status' => 'unpaid',

        ]);
            


        return [
            'success' => true,
            'url' => $session->url,
        ];
        

    }




public function webhook()
{
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;
    $endpoint_secret = 'whsec_vrUElqs57kvEltW7UxoNgrXBBTIA1uWM'; 

    if (!$sig_header) {
        http_response_code(400);
        exit('Missing Stripe signature.');
    }

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\UnexpectedValueException $e) {
        // Mauvais payload
        http_response_code(400);
        exit('Invalid payload.');
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // Signature non vérifiée
        http_response_code(400);
        exit('Invalid signature.');
    }

    if ($event->type === 'checkout.session.completed') {
        // Tu peux loguer l'événement ou effectuer des actions légères ici si nécessaire
        dump($event);
    }

    http_response_code(200); // Important de répondre 200 à Stripe
}



    public function success(Request $request)
    {
// Récupérer l'id de la session Stripe passé dans l'URL
   $data = $request->getbody();
    $session_id = $data['session_id'];
    
if ($session_id) {
    \Stripe\Stripe::setApiKey('sk_test_51RAgELH2nPPbXqXkVPvJUTDRyYguZujueuEi1dbXrR6NA7IrOyKldiiEKdnBpKMA7SrVFElluLNiZOpmsnh8o6F800mpbbJnDJ'); // Remplace par ta clé API privée

    try {
        // Récupérer la session Stripe avec l'ID de la session
        $session = \Stripe\Checkout\Session::retrieve($session_id);
        
        // Vérifier si le paiement est effectué
        if ($session->payment_status === 'paid') {
            
            // Récupérer l'ID du paiement dans ta base de données
            $payment = $this->paymentRepository->findByStripeSessionId($session->id);
            
            if ($payment) {
                // Mettre à jour le statut du paiement à "paid"
                $this->paymentRepository->updatePayment($payment->getId(), [
                    'status' => 'paid',
                ]);
                
                // Décrémenter le stock associé à la commande
                $this->orderService->decrementStockAfterOrder($payment->getOrderId());
            }

            // Afficher un message de succès
            echo "Payment Successful!";
        } else {
            echo "Payment Failed.";
        }
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Gestion des erreurs Stripe
        echo "Error retrieving session: " . $e->getMessage();
    }
} else {
    echo "Session ID is missing.";
}    }

    public function cancel()
    {
        echo "Payment canceled!";
    }
}
