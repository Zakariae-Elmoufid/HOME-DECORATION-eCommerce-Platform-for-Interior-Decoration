<?php 

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use PDO;

class ProductRepository extends BaseRepository {

    private $table = "products";

 
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
                    'id', pv.id,
                    'size_name', pv.size_name,
                    'color_name', pv.color_name,
                    'color_code', pv.color_code,
                    'price_adjustment', pv.price_adjustment,
                    'stock_quantity', pv.stock_quantity
                )
            )
            FROM product_variants pv 
            WHERE pv.product_id = p.id
         ) AS variants,
         (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'id', pi.id,
                    'image_path', pi.image_path,
                    'is_primary', pi.is_primary
                )
            )
            FROM product_images pi 
            WHERE pi.product_id = p.id
         ) AS images
            FROM 
                products p
            LEFT JOIN 
                categories c ON p.category_id = c.id
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
                $categories =  $this->getAll("categories");
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
                $stmt = $this->query(" SELECT COUNT(id) as total_categories FROM categories ");
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                return $result->total_categories;
    }


    public function insertProduct($data) {
        try {
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
                throw new Exception("Failed to insert product");
            }
    
            $product["id"] = $product_id;
    
            $variants = [];
            $images = [];
    
            // Handle product variants
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
    
                    $variantId = $this->insert("product_variants", $variant);
                    if (!$variantId) {
                        throw new Exception("Failed to insert variant");
                    }
    
                    $variants[] = $variant;
                }
            }
    
            // Handle product images
            if (!empty($data["images"])) {
                foreach ($data["images"] as $image) {
                    $imageData = [
                        "is_primary" => $image["is_primary"],
                        "product_id" => $product_id,
                        "image_path" => $image["path"],
                    ];
    
                    $imageId = $this->insert("product_images", $imageData);
                    if (!$imageId) {
                        throw new Exception("Failed to insert image");
                    }
    
                    $images[] = $imageData;
                }
            }
    
            $productData = array_merge($product, [
                'variants' => $variants,
                'images' => $images
            ]);
    
            return new Product($productData);
        } catch (Exception $e) {
            return false;
        }
    }




    public function fetchById($id) {
        try {
            $stmt = $this->query("SELECT 
                p.id,
                p.title,
                p.description,
                p.stock,
                p.base_price,
                p.isAvailable,
                p.category_id,
                c.title AS category_name
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id 
                WHERE p.id = :id", ['id' => $id]);
            
            $product = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$product) {
                throw new \Exception("Product not found");
            }
    
            $stmt = $this->query("SELECT 
                id AS image_id,
                image_path,
                is_primary
                FROM product_images
                WHERE product_id = :id", ['id' => $id]);
            
            $product->images = $stmt->fetchAll(PDO::FETCH_OBJ);
            
    
            $stmt = $this->query("SELECT 
                id AS variant_id,
                size_name,
                color_name,
                color_code,
                price_adjustment,
                stock_quantity
                FROM product_variants
                WHERE product_id = :id", ['id' => $id]);
            
            $product->variants = $stmt->fetchAll(PDO::FETCH_OBJ);

       
            return $product;
    
        } catch (\Exception $e) {
             return null;
        }
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
         foreach ($data as $key => $value) {
            if (preg_match('/^size_name\[(\d+)\]$/', $key, $matches)) {
                $sizeNames[$matches[1]] = $value;
            }
            if (preg_match('/^color_name\[\s*(\d+)\s*\]$/', $key, $matches)) {
                $colorNames[$matches[1]] = $value;
            }
            if (preg_match('/^color_code\[(\d+)\]$/', $key, $matches)) {
                $colorCodes[$matches[1]] = $value;
            }
            if (preg_match('/^price_adjustment\[(\d+)\]$/', $key, $matches)) {
                $priceAdjustments[$matches[1]] = $value;
            }
            if (preg_match('/^stock_quantity\[(\d+)\]$/', $key, $matches)) {
                $stockQuantities[$matches[1]] = $value;
            }
            if (preg_match('/^variant_id\[(\d+)\]$/', $key, $matches)) {
                $variantIds[$matches[1]] = $value;
            }
        }
        foreach ($sizeNames as $index => $sizeName) {
            $variant = [
                "product_id" => $data["id"],
                "size_name" => $sizeName ?? null,
                "color_name" => $colorNames[$index] ?? null,
                "color_code" => $colorCodes[$index] ?? null,
                "price_adjustment" => $priceAdjustments[$index] ?? null,
                "stock_quantity" => $stockQuantities[$index] ?? null,
            ];
        
            if (!empty($variantIds[$index])) {
                $this->update("product_variants", $variantIds[$index], $variant);
            } else {
                $this->insert("product_variants", $variant);
            }
        }
        return true;
    }


   public function deleteProductVariant($id){
       return $this->delete("product_variants" ,$id);
   }



    public function remove($id){
        return $this->delete($this->table ,$id);
    }

    public function productId($id){
      $stmt = $this->query("SELECT  p.id  , p.title, p.description,
       c.title as category_name , pi.image_path as primary_image
      from products p
       inner join product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
       inner join categories c on c.id = p.category_id
       
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
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id 
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
                FROM product_images
                WHERE product_id = ?", [$product->id]);
            $product->images = $stmt->fetchAll(PDO::FETCH_OBJ);
            $articles[] = new Product($product);
        }
        
        return $articles;
    }

    public function getProductsByCategory($idCategories){
        $stmt = $this->query("SELECT  p.id  , p.title, p.description ,p.stock,p.base_price, p.isAvailable, ROUND(AVG(r.rating),2) AS average_rating,
        COUNT(DISTINCT r.id) AS review_count,
        c.title as category_name , pi.image_path as primary_image
       from products p
        inner join product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categories c on c.id = p.category_id
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
       from products p
        inner join product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categories c on c.id = p.category_id
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
       from products p
        inner join product_images pi on p.id  = pi.product_id and pi.is_primary = 1 
        inner join categories c on c.id = p.category_id
        LEFT JOIN  reviews r ON r.product_id = p.id
        where p.isAvailable = 1 
        GROUP BY 
        p.id, p.title, p.description, p.stock, p.base_price, p.isAvailable, c.title, p.isAvailable , pi.image_path
        LIMIT $itemsPerPage OFFSET $offset");
        $productsData = $stmt->fetchAll(PDO::FETCH_OBJ);
        return $productsData;
    }

    public function getImages($product_id){
         $stmt = $this->query("SELECT * from  product_images where product_id = ?",[$product_id]);
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
                    $this->insert("product_images", $imageData);
                    $results['success'][] = "Image at index $index added successfully.";
                } catch (Exception $e) {
                    $results['errors'][] = "Error at index $index: " . $e->getMessage();
                }
            }
        }
    
        return $results;
    }
    

    public function deleteImage($id){
      return  $this->delete("product_images",$id);
    }

    public function setPrimaryImage($id) {
        $image = $this->findById("product_images", $id);
    
        if (!$image) {
            throw new Exception("Image not found.");
        }
    
        $newPriority = ($image->is_primary == 1) ? 0 : 1;
    
        $updateSuccess = $this->update("product_images", $id, ["is_primary" => $newPriority]);
    
        if (!$updateSuccess) {
            throw new Exception("Failed to update status.");
        }
    
        return true;
    }
    
    
}


