<?php 

namespace App\Models;

class Role {
  
    private $id ;
    private $title;
    
    public function __construct($id,$title){
        $this->id = $id;
        $this->title = $title;
    }

    public function getId() {
        return $this->id;
    }
    public function getTitle() {
        return $this->title;
    }
}