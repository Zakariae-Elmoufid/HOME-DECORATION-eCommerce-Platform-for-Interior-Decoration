<?php 

namespace App\Modeles;

class CartItem {
    public  $cart;
    public  $product;
    public int $quantity;

    public function __construct($data) {
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->cart = $cart;
        $this->product = $product;
        $this->quantity = $quantity;
    }
}
