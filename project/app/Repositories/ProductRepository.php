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
         
         $product_id = $this->insert($this->table, $product );
    
         if (!$product_id) {
            return false;
        }
    
        if (!empty($data["size_name"])) {
             foreach ($data["size_name"] as $index => $sizeName) {
                 $size= [
                     "product_id" => $product_id,
                     "size_name" => $sizeName,
                     "price_adjustment" =>  $data["size_price_adjustment"][$index],
                     "stock_quantity" => $data["stock_quantity_size"][$index],
                    ];
                    $this->insert("Product_sizes",$size);
                }
        }

        if(!empty($data["color_name"])){
            foreach($data["color_name"] as $index => $colorName){
                
                $color = [
                        "product_id" => $product_id,
                        "color_name" => $colorName,
                        "color_code" => $data["color_code"][$index],
                        "price_adjustment" => $data["color_price_adjustment"][$index],
                        "stock_quantity" => $data["stock_quantity_color"][$index],
                    ];
                    $this->insert("Product_colors",$color);
                }
            }
        
        if(!empty($data["images"])){
            foreach($data["images"] as  $image){
                $image = [
                    "is_primary" => $image["is_primary"],
                    "product_id" => $product_id,
                    "image_path" => $image["path"],
                ];
                $this->insert("Product_images",$image);
            }
        }
        return true;
    }
    
    
    public function fetchById($id){
        $stmt = $this->query("SELECT 
        p.id,
        p.title,
        p.description,
        p.stock,
        p.base_price,
        p.isAvailable,
        p.category_id
        FROM Products p
        WHERE p.id = $id");
        $product = $stmt->fetch(PDO::FETCH_OBJ);

        $stmt = $this->query("SELECT 
        id as image_id,
        image_path,
        is_primary
        FROM Product_images
        WHERE product_id = $id");
        $product->images = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stmt = $this->query("SELECT 
        id as color_id,
        color_code,
        color_name,
        price_adjustment,
        stock_quantity
        FROM Product_colors
        WHERE product_id = $id");
        $product->colors = $stmt->fetchAll(PDO::FETCH_OBJ);

        $stmt = $this->query("SELECT 
        id as size_id,
        size_name,
        stock_quantity,
        price_adjustment
        FROM Product_sizes
        WHERE product_id = $id");
        $product->sizes = $stmt->fetchAll(PDO::FETCH_OBJ);

        return $product;

    }

    public function updatProduct($id , $data){

        $product = [
            "title" => $data["title"],
            "category_id" => $data["category_id"],
            "description" => $data["description"],
            "base_price" => $data["base_price"],
            "stock" => $data["stock"],
            "isAvailable" => $data["isAvailable"]  == 'on' ? 1 : 0,
         ] ;
         
         $this->update($this->table,$id, $product );
    
        
        if (!empty($data["size_name"])) {
            foreach ($data["size_name"] as $index => $sizeName) {
                $size= [
                    "product_id" => $id,
                    "size_name" => $sizeName,
                    "price_adjustment" =>  $data["size_price_adjustment"][$index],
                    "stock_quantity" => $data["stock_quantity_size"][$index],
                   ];
                   $this->update("Product_sizes",$data["size_id"][$index],$size);
                }
            }
            
            if (!empty($data["color_name"])) {
                foreach ($data["color_name"] as $index => $colorName) {
                    $color= [
                        "product_id" => $id,
                        "color_name" => $colorName,
                        "price_adjustment" =>  $data["color_price_adjustment"][$index],
                        "stock_quantity" => $data["stock_quantity_color"][$index],
                    ];
               $this->update("Product_colors",$data["color_id"][$index],$color);
           }
        }

         if(!empty($data["images"])){
            foreach($data["images"] as  $image){
                $image = [
                    "is_primary" => $image["is_primary"],
                    "product_id" => $id,
                    "image_path" => $image["path"],
                ];
                $this->insert("Product_images",$image);
            }
        }
        return true;



    }


}


