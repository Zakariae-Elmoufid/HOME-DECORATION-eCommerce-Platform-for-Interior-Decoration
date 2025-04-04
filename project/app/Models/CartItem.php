<?php 

namespace App\Models;

class CartItem {
    private int $cart_id;
    private float $total;
    private float $total_items;
    private int $cart_item_id;
    private int $quantity;
    private int $product_id;
    private string $product_title;
    private float $product_price;
    private int $stock;
    private ?string $product_image; 
    private ?string $product_color; 
    private ?string $product_size; 
    private ?int $color_stock;
    private ?int $size_stock;

    public function __construct(array $data = []) {
        $this->cart_id = $data['cart_id'];
        $this->total = $data['total'];
        $this->total_items = $data['total_item'];
        $this->cart_item_id = $data['cart_item_id'];
        $this->quantity = $data['quantity'];
        $this->product_id = $data['product_id'];
        $this->product_title = $data['product_title'];
        $this->product_price = $data['product_price'];
        $this->stock = $data['stock'];
        $this->product_image = $data['product_image'] ?? null;
        $this->product_color = $data['color_name'] ?? null;
        $this->product_size = $data['size_name'] ?? null;
        $this->color_stock = $data['color_stock'] ?? null;
        $this->size_stock = $data['size_stock'] ?? null;

    }

    public function getCartId(): int {
        return $this->cart_id;
    }

    public function getTotal(): float {
        return $this->total;
    }

    public function getTotalItems(): float {
        return $this->total_items;
    }

    public function getCartItemId(): int {
        return $this->cart_item_id;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function getProductId(): int {
        return $this->product_id;
    }

    public function getProductTitle(): string {
        return $this->product_title;
    }

    public function getProductPrice(): float {
        return $this->product_price;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function getProductImage(): ?string {
        return $this->product_image;
    }

    public function getProductColor(): ?string {
        return $this->product_color;
    }

    public function getProductSize(): ?string {
        return $this->product_size;
    }

    public function getStockColor(){
        return $this->color_stock ;
    }

    public function getStockSize(){
        return $this->size_stock ;
    }

    


}
