<?php 

namespace App\Repositories;
use PDO;
use App\Models\Review;
use App\Models\Product;
class ReviewRepository extends BaseRepository {

    private $table  = "reviews"; 

    public function createReview($data){
      return $this->insert($this->table,$data );
    }

    public function getReviewByProduct($product_id){
        $stmt = $this->query("
        SELECT r.id, r.rating, r.content, r.created_at, s.username
        FROM reviews r
        JOIN users s ON s.id = r.user_id
        WHERE r.product_id = ?
    ", [$product_id]);
    
    $reviewData = $stmt->fetchAll(PDO::FETCH_OBJ);
    

       $reviews = [];
       foreach($reviewData as $review){
          $reviews[] = new Review((array) $review);
       }
       return $reviews;
    }

    public function getReviewByUserId($user_id){
        $stmt = $this->query("
        SELECT r.id, r.rating, r.content, r.created_at ,
        r.product_id  
        FROM reviews r
        JOIN users s ON s.id = r.user_id
        JOIN Products p on p.id = r.product_id 
        WHERE r.user_id = ?
     ", [$user_id]);

     $reviewData = $stmt->fetchAll(PDO::FETCH_OBJ);
     $reviews = [];

     foreach($reviewData as $row){
        $reviews[] = new Review((array) $row);
     } 
     
     return $reviews;
    }

    public function avgAndCountReview($product_id){
        $stmt =  $this->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?",[$product_id]);
        $review = $stmt->fetch(PDO::FETCH_OBJ);
        return $review;
    }

    public function updateReview($id,$data){

       return $this->update($this->table,$id,$data);
    }

    




}