<?php 

namespace App\Repositories;

use App\Models\Category;
use PDO;

class ProductRepository extends BaseRepository {

    private $table = "Products";

    public function selectAll(){
        $stmt = $this->query("SELECT 
                p.id,
                p.title,
                p.description,
                p.stock,
                p.base_price,
                p.isAvailable,
                c.title AS category_name,
                c.icon AS category_icon,
                (
                    SELECT JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', ps.id,
                            'size_name', ps.size_name,
                            'price_adjustment', ps.price_adjustment,
                            'stock_quantity', ps.stock_quantity
                        )
                    )
                    FROM Product_sizes ps 
                    WHERE ps.product_id = p.id
                ) AS sizes,
                (
                    SELECT JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', pc.id,
                            'color_name', pc.color_name,
                            'price_adjustment', pc.price_adjustment,
                            'stock_quantity', pc.stock_quantity
                        )
                    )
                    FROM Product_colors pc 
                    WHERE pc.product_id = p.id
                ) AS colors,
                (
                    SELECT JSON_ARRAYAGG(
                        JSON_OBJECT(
                            'id', pi.id,
                            'image_path', pi.image_path,
                            'is_primary', pi.is_primary
                        )
                    )
                    FROM Product_images pi 
                    WHERE pi.product_id = p.id
                ) AS images
            FROM 
                Products p
            LEFT JOIN 
                categorys c ON p.category_id = c.id
            WHERE 
                p.deleted_at IS NULL; ");
         return $stmt->fetchAll(PDO::FETCH_OBJ);

       }

       public function selectCategories(){
        return  $this->getAll('categorys');
       }

       public function countProducts(){
        $stmt = $this->query(" SELECT COUNT(id) as total_products FROM $this->table WHERE deleted_at IS NULL");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total_products;
       }

       public function countAvailable(){
        $stmt = $this->query(" SELECT COUNT(id) as total_available FROM $this->table WHERE isAvailable = 1 ");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total_available;
       }

       public function countCategories(){
        $stmt = $this->query(" SELECT COUNT(id) as total_categories FROM categorys ");
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total_categories;
       }


    public function  insertProduct($data){
         $product = [
            "title" => $data["title"],
            "category_id" => $data["category_id"],
            "description" => $data["description"],
            "base_price" => $data["base_price"],
            "stock" => $data["stock"],
            "isAvailable" => $data["isAvailable"]  == 'on' ? 1 : 0,
         ] ;
         
         dump($data);
         $product_id = $this->insert($this->table, $product );
        //  $product_color = [

        //  ]
        if (!empty($data["size_name"])) {
            foreach ($data["size_name"] as $index => $sizeName) {
                $size = [
                    "size_name" => $sizeName,
                    "size_price_adjustment" =>  $data["size_price_adjustment"][$index]
                ]
            }
        }
    
    }   


}


