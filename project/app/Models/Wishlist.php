<?php

namespace App\Models;

class Wishlist {
    private $id;
    private $user_id;
    private $product_id;
    private $created_at;
    
    // Propriétés du produit
    private $title;
    private $description;
    private $stock;
    private $base_price;
    private $isAvailable;
    private $category_name;
    private $productImage;
    private $average_rating;
    private $review_count;
    
    /**
     * Constructeur qui initialise les propriétés via les setters
     * 
     * @param array|object $data Les données à assigner
     */
    public function __construct($data = null) {
        if ($data !== null) {
            // Utiliser les setters pour initialiser les propriétés
            foreach ($data as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->$method($value);
                } elseif (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }
    
    // Getters
    public function getId() {
        return $this->id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getProductId() {
        return $this->product_id;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getStock() {
        return $this->stock;
    }
    
    public function getBasePrice() {
        return $this->base_price;
    }
    
    public function getIsAvailable() {
        return $this->isAvailable;
    }
    
    public function getCategoryName() {
        return $this->category_name;
    }
    
    public function getProductImage() {
        return $this->productImage;
    }
    
    public function getAverageRating() {
        return $this->average_rating;
    }
    
    public function getReviewCount() {
        return $this->review_count;
    }
    
    // Setters
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    
    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }
    
    public function setProductId($product_id) {
        $this->product_id = $product_id;
        return $this;
    }
    
    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
        return $this;
    }
    
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function setStock($stock) {
        $this->stock = $stock;
        return $this;
    }
    
    public function setBasePrice($base_price) {
        $this->base_price = $base_price;
        return $this;
    }
    
    public function setIsAvailable($isAvailable) {
        $this->isAvailable = $isAvailable;
        return $this;
    }
    
    public function setCategoryName($category_name) {
        $this->category_name = $category_name;
        return $this;
    }
    
    public function setProductImage($productImage) {
        $this->productImage = $productImage;
        return $this;
    }
    
    public function setAverageRating($average_rating) {
        $this->average_rating = $average_rating;
        return $this;
    }
    
    public function setReviewCount($review_count) {
        $this->review_count = $review_count;
        return $this;
    }
}    