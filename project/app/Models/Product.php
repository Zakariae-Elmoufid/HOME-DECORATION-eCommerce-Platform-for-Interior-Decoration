<?php 

namespace App\Models;

class Product {
    private $id;
    private $title;
    private $description;
    private  $category_name;
    private $base_price;
    private $stock;
    private $isAvailable;
    private $total_reviews;
    private $average_rating;
    private $sizes = [];
    private $colors = [];
    private $images = [];



    public function __construct($data = []) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->id = $dataArray['id'] ?? null;
        $this->title = $dataArray['title'] ?? null;
        $this->description = $dataArray['description'] ?? null;
        $this->category_name = $dataArray['category_name'] ?? null;
        $this->isAvailable = $dataArray['isAvailable'] ?? null;
        $this->base_price = $dataArray['base_price'] ?? null;
        $this->stock = $dataArray['stock'] ?? null;
        $this->total_reviews = $dataArray['review_count'] ?? null;
        $this->average_rating = $dataArray['average_rating'] ?? null;
        
        if (isset($dataArray['sizes'])) {
            $this->sizes = is_string($dataArray['sizes']) ? json_decode($dataArray['sizes'], true) : $dataArray['sizes'];
            $this->sizes = $this->sizes ?: [];
        }
        
        if (isset($dataArray['colors'])) {
            $this->colors = is_string($dataArray['colors']) ? json_decode($dataArray['colors'], true) : $dataArray['colors'];
            $this->colors = $this->colors ?: [];
        }
        
        if (isset($dataArray['images'])) {
            $this->images = is_string($dataArray['images']) ? json_decode($dataArray['images'], true) : $dataArray['images'];
            $this->images = $this->images ?: [];
        }
    
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'base_price' => $this->base_price,
            'stock' => $this->stock,
            'sizes' => $this->sizes,
            'colors' => $this->colors,
            'images' => $this->images
            ];
    }

     public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategoryName() {
        return $this->category_name;
    }

    public function getBasePrice() {
        return $this->base_price;
    }

    public function getStock() {
        return $this->stock;
    }

    public function getIsAvailable(){
        return $this->isAvailable;
    }

    public function getTotalReviews(){
        return $this->total_reviews;
    }

    public function getAverageRating(){
        return $this->average_rating;
    }

    public function getSizes() {
        return $this->sizes;
    }

    public function getColors() {
        return $this->colors;
    }

    public function getImages() {
        return $this->images;
    }

   
}