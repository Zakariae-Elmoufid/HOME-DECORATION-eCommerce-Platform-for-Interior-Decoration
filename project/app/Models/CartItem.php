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
    private ?string $product_size; 
    private ?string $product_color; 
    private ?int $variant_id;
    private ?int $stock_quantity;

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
        $this->variant_id = $data['variant_id'] ?? null;
        $this->product_color = $data['color_name'] ?? null;
        $this->product_size = $data['size_name'] ?? null;
        $this->stock_quantity = $data['stock_quantity'] ?? null;

    }

    public function getCartId(): int {
        return $this->cart_id;
    }
    public function getVariantId() {
        return $this->variant_id;
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

    public function getStockQuantity(){
        return $this->stock_quantity ;
    }

    

    


}
