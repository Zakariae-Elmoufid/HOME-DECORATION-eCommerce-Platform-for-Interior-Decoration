<?php 

namespace App\Models;

class Order {
    private $id;
    private $userId;
    private $orderDate;
    private $status;
    private $totalAmount;
    private $shippingAddress;
    private $phone;
    private $paymentMethod;
    private $paymentDate;
    private $items = [];

    public function __construct($data = []) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->id = $dataArray['id'] ?? null;
        $this->userId = $dataArray['user_id'] ?? null;
        $this->orderDate = $dataArray['orderDate'] ?? null;
        $this->status = $dataArray['status'] ?? 'pending';
        $this->totalAmount = $dataArray['totalAmount'] ?? 0;
        $this->shippingAddress = $dataArray['shippingAddress'] ?? null;
        $this->phone = $dataArray['phone'] ?? null;
        $this->paymentMethod = $dataArray['paymentMethod'] ?? null;
        $this->paymentDate = $dataArray['paymentDate'] ?? null;
        
        if (isset($dataArray['items'])) {
            foreach ($dataArray['items'] as $itemData) {
                $this->items[] = new OrderItem($itemData);
            }
        }
    }

    // Getters & Setters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function getOrderDate() {
        return $this->orderDate;
    }
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getTotalAmount() {
        return $this->totalAmount;
    }
    
    public function getShippingAddress() {
        return $this->shippingAddress;
    }
    
    public function getPhone() {
        return $this->phone;
    }
    
    public function getPaymentMethod() {
        return $this->paymentMethod;
    }
    
    public function getPaymentDate() {
        return $this->paymentDate;
    }
    
    public function setPaymentDate($paymentDate) {
        $this->paymentDate = $paymentDate;
    }
    
    public function getItems() {
        return $this->items;
    }
    
    public function addItem(OrderItem $item) {
        $this->items[] = $item;
    }
}
