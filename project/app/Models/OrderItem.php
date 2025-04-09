<?php 

namespace App\Models;


class OrderItem {
    private $id;
    private $orderId;
    private $productId;
    private $quantity;
    private $price;
    private $selectedColor;
    private $selectedSize;
    private $productTitle; 
    private $productImage; 
    private $total_item; 

    public function __construct($data = []) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->id = $dataArray['id'] ?? null;
        $this->orderId = $dataArray['order_id'] ?? null;
        $this->productId = $dataArray['productId'] ?? null;
        $this->quantity = $dataArray['quantity'] ?? 1;
        $this->price = $dataArray['price'] ?? 0;
        $this->selectedColor = $dataArray['selectedColor'] ?? null;
        $this->selectedSize = $dataArray['selectedSize'] ?? null;
        $this->productTitle = $dataArray['productTitle'] ?? null;
        $this->productImage = $dataArray['productImage'] ?? null;
        $this->total_item = $dataArray['total_item'] ?? null;
    }

    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getOrderId() {
        return $this->orderId;
    }
    
    public function getProductId() {
        return $this->productId;
    }
    
    public function getQuantity() {
        return $this->quantity;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function getSelectedColor() {
        return $this->selectedColor;
    }
    
    public function getSelectedSize() {
        return $this->selectedSize;
    }
    public function getProductTitle() {
        return $this->productTitle;
    }
    public function getProductImage() {
        return $this->productImage;
    }
    public function getTotalItem() {
        return $this->total_item;
    }
 
    

    

    
    // Calculer le sous-total de cet élément
    public function getSubtotal() {
        return $this->price * $this->quantity;
    }
}