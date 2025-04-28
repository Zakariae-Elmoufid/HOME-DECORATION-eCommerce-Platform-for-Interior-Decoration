<?php

namespace App\Models;

class Payment {
    private $id;
    private $order_id;
    private $payment_method;
    private $payment_intent_id;
    public $stripe_session_id; // 👈 ajoute cette ligne !

    private $amount;
    private $currency;
    private $status;
    private $error_message;
    private $created_at;
    private $updated_at;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? null;
        $this->order_id = $data['order_id'] ?? null;
        $this->payment_method = $data['payment_method'] ?? 'credit_card';
        $this->payment_intent_id = $data['payment_intent_id'] ?? null;
        $this->stripe_session_id = $data['stripe_session_id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->currency = $data['currency'] ?? 'usd';
        $this->status = $data['status'] ?? 'pending';
        $this->error_message = $data['error_message'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getOrderId(): ?int {
        return $this->order_id;
    }

    public function getPaymentMethod(): string {
        return $this->payment_method;
    }

    public function getPaymentIntentId(): ?string {
        return $this->payment_intent_id;
    }
    public function getPaymentSessionId(): ?string {
        return $this->stripe_session_id;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function getCurrency(): string {
        return $this->currency;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getErrorMessage(): ?string {
        return $this->error_message;
    }

    public function getCreatedAt(): ?string {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string {
        return $this->updated_at;
    }

    // Helper method to determine if payment was successful
    public function isSuccessful(): bool {
        return $this->status === 'succeeded' || $this->status === 'paid';
    }

    // Helper method to determine if payment is still pending
    public function isPending(): bool {
        return $this->status === 'pending' || $this->status === 'processing';
    }

    // Helper method to determine if payment failed
    public function isFailed(): bool {
        return $this->status === 'failed' || $this->status === 'canceled';
    }

    // Helper method to get formatted amount with currency symbol
    public function getFormattedAmount(): string {
        $currencySymbol = '$'; // Default to USD
        
        if ($this->currency === 'eur') {
            $currencySymbol = '€';
        } else if ($this->currency === 'gbp') {
            $currencySymbol = '£';
        }
        
        return $currencySymbol . number_format($this->amount, 2);
    }
}