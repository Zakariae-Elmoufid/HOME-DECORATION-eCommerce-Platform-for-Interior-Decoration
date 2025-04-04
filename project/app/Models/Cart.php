<?php 

namespace App\Models;


class Cart {
    public int $id;
    public int $user_id;  
    public string $session_id;
    private float $total;
    public array $items = [];

    public function __construct($data = []){
        $dataArray = is_object($data) ? get_object_vars($data) : $data;

        $this->id = $dataArray['id'] ?? null;
        $this->user_id = $dataArray['user_id'] ?? null;
        $this->session_id = $dataArray['session_id'] ?? null;
        $this->status = $dataArray['status'] ?? 'pending';
        $this->total = $dataArray['total'] ?? 0;
     
        
        if (isset($dataArray['items'])) {
            foreach ($dataArray['items'] as $itemData) {
                $this->items[] = new CartItem($itemData);
            }
        }

    }

  
}
