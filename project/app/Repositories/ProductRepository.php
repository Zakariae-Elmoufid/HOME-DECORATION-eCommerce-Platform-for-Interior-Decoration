<?php 

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
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
        AVG(r.rating) AS average_rating,
        COUNT(DISTINCT r.id) AS review_count,
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
            LEFT JOIN 
                reviews r ON r.product_id = p.id
            WHERE 
                p.deleted_at IS NULL
            GROUP BY 
                p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, c.icon");
                $data =  $stmt->fetchAll(PDO::FETCH_OBJ);
                $products = [];
                
                foreach($data as $product){
                    $products[] = new Product($product);
                }
                return $products;
            }

    public function selectCategories(){
                $categories =  $this->getAll("categorys");
                $data = [];
                foreach( $categories as $category ){
                $data = new Category($category);
                }
         return $categories;
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


    public function insertProduct($data) {
        $product = [
            "title" => $data["title"],
            "category_id" => $data["category_id"],
            "description" => $data["description"],
            "base_price" => $data["base_price"],
            "stock" => $data["stock"],
            "isAvailable" => $data["isAvailable"] ? 1 : 0
        ];
         
        $product_id = $this->insert($this->table, $product);
    
        if (!$product_id) {
            return false;
        }
        
        $product["id"] = $product_id;
        
        $variant = [];
        $images = [];
        
        if (!empty($data["size_name"]) || !empty($data["color_name"])) {
            foreach ($data["size_name"] as $index => $sizeName) {
                $variant = [
                    "product_id" => $product_id,
                    "size_name" => $sizeName,
                    "color_name" => $data["color_name"][$index],
                    "color_code" => $data["color_code"][$index],
                    "price_adjustment" => $data["price_adjustment"][$index],
                    "stock_quantity" => $data["stock_quantity"][$index],
                ];
                
                $this->insert("Product_variants", $variant);
                
                $variants[] = $variant;
            }
        }
    

        if (!empty($data["images"])) {
            foreach ($data["images"] as $image) {
                $imageData = [
                    "is_primary" => $image["is_primary"],
                    "product_id" => $product_id,
                    "image_path" => $image["path"],
                ];
                
                $this->insert("Product_images", $imageData);
                
                $images[] = $imageData;
            }
        }
        
        $productData = array_merge($product, [
            'variants' => $variants,
            'images' => $images
        ]);
        
        $productObject = new Product($productData);
        
        
        
        
        return $productObject;
    }
    
    
    public function fetchById($id){
        $stmt = $this->query("SELECT 
        p.id,
        p.title,
        p.description,
        p.stock,
        p.base_price,
        p.isAvailable,
        p.category_id,
        c.title AS category_name
        FROM Products p
        inner join categorys c on c.id = p.category_id 
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
        id as variant_id,
        size_name,
        color_name,
        color_code,
        price_adjustment,
        stock_quantity
        FROM Product_variants
        WHERE product_id = $id");
        $product->variants = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $product;

    }

    public function updatProduct($id , $data){
    
        $product = [
            "title" => $data["title"],
            "category_id" => $data["category_id"],
            "description" => $data["description"],
            "base_price" => $data["base_price"],
            "stock" => $data["stock"],
            "isAvailable" => $data["isAvailable"] ? 1 : 0,
         ] ;
         
         $this->update($this->table,$id, $product );
    
         if (isset($data['size_name']) && is_array($data['size_name'])) {
        
            foreach ($data["size_name"] as $index => $sizeName) {
                $size= [
                    "product_id" => $id,
                    "size_name" => $sizeName ?? null,
                    "color_name" =>  $data["color_name"][$index]  ?? null,
                    "color_code" =>  $data["color_code"][$index]  ?? null,
                    "price_adjustment" =>  $data["price_adjustment"][$index]  ?? null,
                    "stock_quantity" => $data["stock_quantity"][$index]  ?? null,
                   ];
                if (!empty($data["variant_id"][$index])) {
                    $this->update("Product_variants", $data["variant_id"][$index], $size);
                } else {
                    $this->insert("Product_variants", $size);
                }
                
            }
         }    
        return true;
    }




    public function remove($id){
        return $this->delete($this->table ,$id);
    }

    public function productId($id){
      $stmt = $this->query("SELECT  p.id  , p.title, p.description,
       c.title as category_name , pi.image_path as primary_image
      from Products p
       inner join Product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
       inner join categorys c on c.id = p.category_id
       
       where p.id = ? ",[$id]);
      $product =  $stmt->fetch(PDO::FETCH_OBJ);
      return new Product((array)$product) ;
    }

    public function getNewProducts() {
        $stmt = $this->query("SELECT 
            p.id,
            p.title,
            p.description,
            p.stock,
            p.base_price,
            p.isAvailable,
            p.created_at,
            c.title AS category_name,
            AVG(r.rating) AS average_rating,
            COUNT(DISTINCT r.id) AS review_count
            FROM Products p
            INNER JOIN categorys c ON c.id = p.category_id 
            LEFT JOIN  reviews r ON r.product_id = p.id
             GROUP BY 
        p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, p.created_at

            ORDER BY p.created_at DESC
            LIMIT 5");
        $products = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        $articles = [];
        
        foreach($products as $product) {
            $stmt = $this->query("SELECT 
                id as image_id,
                image_path,
                is_primary
                FROM Product_images
                WHERE product_id = ?", [$product->id]);
            $product->images = $stmt->fetchAll(PDO::FETCH_OBJ);
            $articles[] = new Product($product);
        }
        
        return $articles;
    }

    public function getProductsByCategory($idCategories){
        $stmt = $this->query("SELECT  p.id  , p.title, p.description,p.stock,p.base_price,p.isAvailable, ROUND(AVG(r.rating),2) AS average_rating,
        COUNT(DISTINCT r.id) AS review_count,
        c.title as category_name , pi.image_path as primary_image
       from Products p
        inner join Product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categorys c on c.id = p.category_id
        LEFT JOIN  reviews r ON r.product_id = p.id
        where c.id = ?
        GROUP BY 
        p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, p.isAvailable , pi.image_path
         ",[$idCategories]);
       $productsData =  $stmt->fetchAll(PDO::FETCH_OBJ);
       return $productsData;
    }

    public function fechByKeyWord($title){
        $stmt = $this->query("SELECT  p.id  , p.title, p.description,p.stock,p.base_price,p.isAvailable, ROUND(AVG(r.rating),2) AS average_rating,
        COUNT(DISTINCT r.id) AS review_count,
        c.title as category_name , pi.image_path as primary_image
       from Products p
        inner join Product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categorys c on c.id = p.category_id
        LEFT JOIN  reviews r ON r.product_id = p.id
        where p.title LIKE  ?
        GROUP BY 
        p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, p.isAvailable , pi.image_path
         ",["%".$title."%"]);
         $productsData = $stmt->fetchAll(PDO::FETCH_OBJ);
        //  $products = [] ;
        //  foreach($productsData as $product){
        //     $products  = new Product($product);
        //  }
         return $productsData;
    }
    
    public function paginationProduct($page){
        $itemsPerPage = 8;
        $offset = ($page - 1) * $itemsPerPage;
        $stmt = $this->query("SELECT  p.id  , p.title, p.description,p.stock,p.base_price,p.isAvailable, ROUND(AVG(r.rating),2) AS average_rating,
        COUNT(DISTINCT r.id) AS review_count,
        c.title as category_name , pi.image_path as primary_image
       from Products p
        inner join Product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categorys c on c.id = p.category_id
        LEFT JOIN  reviews r ON r.product_id = p.id
        where p.isAvailable = 1 
        GROUP BY 
        p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, p.isAvailable , pi.image_path
        LIMIT $itemsPerPage OFFSET $offset");
        $productsData = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $productsData;
    }

    public function getImages($product_id){
         $stmt = $this->query("SELECT * from  Product_images where product_id = ?",[$product_id]);
         $images = $stmt->fetchAll(PDO::FETCH_OBJ);
         return $images;
    }
    
    public function addImages($data, $product_id) {
        $results = [
            'success' => [],
            'errors' => []
        ];
    
        if (!empty($data["images"])) {
            foreach ($data["images"] as $index => $image) {
                $imageData = [
                    "is_primary" => $image["is_primary"],
                    "product_id" => $product_id,
                    "image_path" => $image["path"],
                ];
                try {
                    $this->insert("Product_images", $imageData);
                    $results['success'][] = "Image at index $index added successfully.";
                } catch (Exception $e) {
                    $results['errors'][] = "Error at index $index: " . $e->getMessage();
                }
            }
        }
    
        return $results;
    }
    

    public function deleteImage($id){
      return  $this->delete("Product_images",$id);
    }

    public function setPrimaryImage($id) {
        $image = $this->findById("Product_images", $id);
    
        if (!$image) {
            throw new Exception("Image not found.");
        }
    
        $newPriority = ($image->is_primary == 1) ? 0 : 1;
    
        $updateSuccess = $this->update("Product_images", $id, ["is_primary" => $newPriority]);
    
        if (!$updateSuccess) {
            throw new Exception("Failed to update status.");
        }
    
        return true;
    }
    
    
}


