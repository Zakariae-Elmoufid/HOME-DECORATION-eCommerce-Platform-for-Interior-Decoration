<?php 

namespace App\Models;


class Cart {
    public int $id;
    public int $user_id;  
    public string $session_id;
    public array $items = [];

    public function __construct($id, $product_id, $user_id = null ,$session_id = null ){
         $this->id = $id;
         $this->user_id = $user_id;
         $this->session_id = $session_id;
    }

  
}
