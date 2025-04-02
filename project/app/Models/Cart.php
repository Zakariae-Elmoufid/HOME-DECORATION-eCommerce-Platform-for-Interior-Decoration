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

    public function addItem(Product $product, int $quantity = 1) {
        $this->items[] = new CartItem($this, $product, $quantity);
    }

    public function removeItem(Product $product) {
        $this->items = array_filter($this->items, fn($item) => $item->product->id !== $product->id);
    }
}
