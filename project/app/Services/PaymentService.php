<?php

namespace App\Services;

use App\repositories\PaymentRepository;
use App\repositories\OrderRepository;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentService {
    private $paymentRepository;
    private $orderRepository;
    private $stripeSecretKey;

    public function __construct() {
        $this->paymentRepository = new PaymentRepository();
        $this->orderRepository = new OrderRepository();
        $this->stripeSecretKey = 'sk_test_51RAgELH2nPPbXqXkVPvJUTDRyYguZujueuEi1dbXrR6NA7IrOyKldiiEKdnBpKMA7SrVFElluLNiZOpmsnh8o6F800mpbbJnDJ'; // Replace with your actual secret key
    }

    /**
     * Create a payment intent with Stripe
     * 
     * @param int $orderId The order ID
     * @param float $amount The amount to charge
     * @param string $currency The currency code (default: usd)
     * @param array $metadata Additional metadata for the payment
     * @return array The payment intent details
     */
    public function createPaymentIntent(int $orderId, float $amount, string $currency = 'usd', array $metadata = []): array {
        try {
            // Initialize Stripe
            Stripe::setApiKey($this->stripeSecretKey);
            
            // Get order details
            $order = $this->orderRepository->getOrderById($orderId);
            
            if (!$order) {
                return [
                    'success' => false,
                    'error' => 'Order not found'
                ];
            }
            
            // Amount in cents (Stripe requires amount in cents)
            $amountInCents = round($amount * 100);
            
            // Set base metadata
            $baseMetadata = [
                'order_id' => $orderId
            ];
            
            // Merge with custom metadata
            $allMetadata = array_merge($baseMetadata, $metadata);
            
            // Create a payment intent
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => $currency,
                'metadata' => $allMetadata
            ]);
            
            // Save payment information
            $this->paymentRepository->create([
                'order_id' => $orderId,
                'payment_method' => 'credit_card',
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $paymentIntent->status
            ]);
            
            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id
            ];
            
        } catch (ApiErrorException $e) {
            // Log the error
            error_log('Stripe API Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Log the error
            error_log('Payment Service Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'An unexpected error occurred'
            ];
        }
    }

    /**
     * Retrieve a payment intent from Stripe
     * 
     * @param string $paymentIntentId The payment intent ID
     * @return array The payment intent details
     */
    public function retrievePaymentIntent(string $paymentIntentId): array {
        try {
            // Initialize Stripe
            Stripe::setApiKey($this->stripeSecretKey);
            
            // Retrieve the payment intent
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            
            return [
                'success' => true,
                'payment_intent' => $paymentIntent
            ];
            
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'An unexpected error occurred'
            ];
        }
    }

    /**
     * Update payment status based on Stripe webhook event
     * 
     * @param string $paymentIntentId The payment intent ID
     * @param string $status The new status
     * @param string|null $errorMessage Optional error message
     * @return bool Success or failure
     */
    public function updatePaymentStatus(string $paymentIntentId, string $status, ?string $errorMessage = null): bool {
        try {
            // Get payment by payment intent ID
            $payment = $this->paymentRepository->getByPaymentIntentId($paymentIntentId);
            
            if (!$payment) {
                error_log('Payment not found for payment intent: ' . $paymentIntentId);
                return false;
            }
            
            // Update payment status
            $this->paymentRepository->update($payment->getId(), [
                'status' => $status,
                'error_message' => $errorMessage
            ]);
            
            // Get order ID
            $orderId = $payment->getOrderId();
            
            // Update order status based on payment status
            $orderStatus = 'pending';
            
            if ($status === 'succeeded') {
                $orderStatus = 'processing';
            } else if ($status === 'failed' || $status === 'canceled') {
                $orderStatus = 'payment_failed';
            }
            
            $this->orderRepository->updateOrder($orderId, [
                'status' => $orderStatus
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            error_log('Error updating payment status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle Stripe webhook events
     * 
     * @param array $payload The webhook payload
     * @return bool Success or failure
     */
    public function handleWebhook(array $payload): bool {
        $event = $payload;
        
        try {
            // Verify the event (in production, you should validate the event signature)
            
            // Handle the event
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event['data']['object'];
                    return $this->updatePaymentStatus($paymentIntent['id'], 'succeeded');
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event['data']['object'];
                    $errorMessage = $paymentIntent['last_payment_error']['message'] ?? 'Payment failed';
                    return $this->updatePaymentStatus($paymentIntent['id'], 'failed', $errorMessage);
                    
                case 'payment_intent.canceled':
                    $paymentIntent = $event['data']['object'];
                    return $this->updatePaymentStatus($paymentIntent['id'], 'canceled', 'Payment was canceled');
                    
                default:
                    // Unexpected event type
                    error_log('Unhandled event type: ' . $event['type']);
                    return true;
            }
        } catch (\Exception $e) {
            error_log('Error handling webhook: ' . $e->getMessage());
            return false;
        }
    }
}