<?php 

namespace App\Repositories;

class ReviewRepository extends BaseRepository {

    private $table  = "reviews"; 

    public function createReview($data){
      return $this->insert($this->table,$data );
    }



    

}