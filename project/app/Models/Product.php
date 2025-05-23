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
    private $primaryImage ;
    private $variants = [];
    private $images = [];
    private $reviews ;



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
        $this->primaryImage = $dataArray['primary_image'] ?? null;
        
        if (isset($dataArray['variants'])) {
            $this->variants = is_string($dataArray['variants']) ? json_decode($dataArray['variants'], true) : $dataArray['variants'];
            $this->variants = $this->variants ?: [];
        }
        
    
        
        if (isset($dataArray['images'])) {
            $this->images = is_string($dataArray['images']) ? json_decode($dataArray['images'], true) : $dataArray['images'];
            $this->images = $this->images ?: [];
        }
    }


    public function addReview(Review $review) {
        $this->reviews = $review;
    }

    // public function toArray() {
    //     return [
    //         'id' => $this->id,
    //         'title' => $this->title,
    //         'description' => $this->description,
    //         'category_id' => $this->category_id,
    //         'base_price' => $this->base_price,
    //         'stock' => $this->stock,
    //         'sizes' => $this->sizes,
    //         'colors' => $this->colors,
    //         'images' => $this->images
    //         ];
    // }

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

    public function getVariants() {
        return $this->sizes;
    }

  

    public function getImages() {
        return $this->images;
    }

    public function getPrimaryImage(){
        return $this->primaryImage;
    }

   
}