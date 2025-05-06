<?php

namespace App\Models;

class Category {
   
    private $title;
    private $icon;
    private $id;

    public function __construct($data = []){
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

         $this->title = $dataArray['title'] ?? null ;
         $this->icon = $dataArray['icon'] ?? null;
         $this->id = $dataArray['id'] ?? null ;
    }
    
    public function getId(){
        return $this->id;
    }

    public function getTitle() {
        return $this->title; 
    }
  
   public function getIcon() {
        return $this->icon;
    }

}