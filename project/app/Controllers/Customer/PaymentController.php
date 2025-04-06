<?php

namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Services\PaymentService;
use App\repositories\OrderRepository;

class PaymentController extends Controller {
    private $paymentService;
    private $orderRepository;
    private $response;

    public function __construct() {
        $this->paymentService = new PaymentService();
        $this->orderRepository = new OrderRepository();
        $this->response = new Response();
    }

    /**
     * Create a Stripe payment intent
     */
    public function createIntent(Request $request) {
        $data = $request->getbody();
        
        // Validate required fields
        if (empty($data['order_id'])) {
            return $this->response->jsonEncode([
                'success' => false,
                'error' => 'Order ID is required'
            ]);
        }
        
        $orderId = (int) $data['order_id'];
        
        // Get order details
        $order = $this->orderRepository->getOrderById($orderId);
        
        if (!$order) {
            return $this->response->jsonEncode([
                'success' => false,
                'error' => 'Order not found'
            ]);
        }
        
        // Create payment intent
        $result = $this->paymentService->createPaymentIntent(
            $orderId,
            $order->getTotalAmount(),
            'usd', // Change according to your currency
            [
                'customer_email' => $data['email'] ?? null,
                'payment_type' => 'order_payment'
            ]
        );
        
        return $this->response->jsonEncode($result);
    }

    /**
     * Update payment status
     */
    public function updateStatus(Request $request) {
        $data = $request->getbody();
        
        // Validate required fields
        if (empty($data['payment_intent_id']) || empty($data['status'])) {
            return $this->response->jsonEncode([
                'success' => false,
                'error' => 'Payment intent ID and status are required'
            ], 400);
        }
        
        // Update payment status
        $result = $this->paymentService->updatePaymentStatus(
            $data['payment_intent_id'],
            $data['status'],
            $data['error_message'] ?? null
        );
        
        return $this->response->jsonEncode([
            'success' => $result
        ]);
    }

    /**
     * Handle Stripe webhook
     */
    public function webhook(Request $request) {
        // Get the raw POST data
        $payload = $request->getRawBody();
        
        // Parse the payload
        $event = json_decode($payload, true);
        
        if (!$event) {
            return $this->response->jsonEncode([
                'success' => false,
                'error' => 'Invalid payload'
            ], 400);
        }
        
        // Handle the webhook
        $result = $this->paymentService->handleWebhook($event);
        
        return $this->response->jsonEncode([
            'success' => $result
        ]);
    }

    /**
     * Show payment confirmation page
     */
    public function confirmation(Request $request) {
        $body = $request->getbody();
        $orderId = isset($body['id']) ? (int) $body['id'] : null;
        // Get order details
        $order = $this->orderRepository->getOrderById($orderId);
        
        if (!$order) {
            return $this->response->redirect('/checkout');
        }
        
        // Get order items
        $orderItems = $this->orderRepository->getOrderItems($orderId);
        
        $this->response->render('customer/payment-confirmation', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    /**
     * Handle PayPal payment
     */
    public function paypal($orderId) {
        // This is a placeholder for PayPal integration
        // You would implement this based on PayPal's API requirements
        
        $this->response->render('customer/paypal-checkout', [
            'order_id' => $orderId
        ]);
    }

    /**
     * Error page for failed payments
     */
    public function error($orderId) {
        $this->response->render('customer/payment-error', [
            'order_id' => $orderId
        ]);
    }
}