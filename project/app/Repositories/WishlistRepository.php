<?php

namespace App\Repositories;
use App\Models\Wishlist;
use PDO;

class WishlistRepository extends BaseRepository {
      
     
    private $table = 'wishlists';

    public function fetchByUser($user_id) {
        $stmt = $this->query("SELECT 
            p.id as product_id,
            w.id,
            w.added_at as created_at,
            p.title,
            p.description, 
            p.stock, 
            p.base_price,
            p.isAvailable,
            c.title as category_name,
            pg.image_path as productImage,
            AVG(r.rating) AS average_rating,
            COUNT(DISTINCT r.id) AS review_count
        FROM products p
        INNER JOIN wishlists w ON w.product_id = p.id
        LEFT JOIN reviews r ON r.product_id = p.id
        INNER JOIN categories c ON c.id = p.category_id
        INNER JOIN product_images pg ON pg.product_id = p.id AND pg.is_primary = 1
        WHERE w.user_id = ?
        GROUP BY 
            p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, pg.image_path , w.id ,w.added_at ", [$user_id]);
          $WishlistData =  $stmt->fetchAll(PDO::FETCH_OBJ);
          $Wishlists = [];
          
          foreach($WishlistData as $Wishlist){
            $wishlistObject = new Wishlist((array)$Wishlist);
            $wishlistObject->setUserId($user_id);
            $Wishlists[] = $wishlistObject;
          }
          return $Wishlists;
    }
    public function fetchByUserAndProduct($data){
        $WishlistData =  $this->findBy($this->table, $data);
        $wishlistObject = new Wishlist((array)$WishlistData);
        return $wishlistObject;
    }

    public function create($data){
       return  $this->insert($this->table, $data);
    }

    public function deleteWishlist($id){
        return $this->delete($this->table, $id);
    }



}    