<?php

namespace App\Models;

class Review {

    private $id;
    private $content;
    private $rating;
    private $createdAt;
    private  $username;

 
    public function  __construct($data){
         $this->id = $data['id'] ?? null;
         $this->content = $data['content'] ?? null;
         $this->rating = $data['rating'] ?? null;
         $this->createdAt = $data['created_at'] ?? null;
         $this->username = $data['username'] ?? '';
    }


    public function getId(){
        return$this->id;
    }

    public function getContent(){
        return$this->content;
    }

    public function getRating(){
        return$this->rating;
    }

    public function getCreatedAt(){
        return$this->createdAt;
    }

    public function getUsername(){
        return$this->username;
    }

}
