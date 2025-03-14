<?php

namespace App\Models;

class Category {
   
    private $title;
    private $icon;
    private $id;

    public function __construct($title,$icon,$id = null){
         $this->title = $title;
         $this->icon = $icon;
         $this->id = $id;
    }
    
    public function getId(){
        return $this->id;
    }

    public function getTitle() {
        return $this->title; 
    }
  
   public function geticon() {
        return $this->icon;
    }

}