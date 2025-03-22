<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Response;
use App\Repositories\ProductRepository;

class ProductService {
    
   private $productRepository;
   private $response;

   public function __construct(){
       $this->productRepository = new ProductRepository();
       $this->response = new Response();
   } 

   public function fetchAll(){
    $products = $this->productRepository->selectAll();
    foreach ($products as &$product) {
        $product->sizes = !is_null($product->sizes) ? json_decode($product->sizes, true) : [];
        $product->images = !is_null($product->images) ?  json_decode($product->images, true) : [];
        $product->colors = !is_null($product->colors) ?  json_decode($product->colors, true) : [];
    }
    $categories = $this->productRepository->selectCategories();
    $countProducts = $this->productRepository->countProducts();
    $countAvailable = $this->productRepository->countAvailable();
    $countCategories = $this->productRepository->countCategories();
    
    $this->response->render('admin/products/index', [
        'products' => $products ,
        'categories' => $categories ,
        "countProducts" => $countProducts,
        "countAvailable" => $countAvailable ,
        "countCategories" => $countCategories]
    );
   }

   public function fetchCategory(){
    $categories = $this->productRepository->selectCategories();
    $this->response->render('admin/products/create', [
        'categories' => $categories ]); 
   }



   public function store($data){

    $errors = [];
    $validator = new Validator($data);
    $validator->setRules([
        'title' => 'required|string|min:2|max:50',
        'category_id' => 'required',
        'description' => 'required|string|min:10|max:200',
        'base_price' => 'required|numeric',
        'stock' => 'required'
    ]);
    
    $isValid = $validator->validate();
    
    if (isset($data['size_name']) && is_array($data['size_name'])) {
        foreach ($data['size_name'] as $index => $sizeName) {
            $sizeValidator = new Validator(['size' => $sizeName]);
            $sizeValidator->setRules(['size' => 'required|string|min:2|max:50']);
            
            if (!$sizeValidator->validate()) {
                $errors = $sizeValidator->getErrors();
                if (!empty($errors['size'])) {
                    foreach ($errors['size'] as $error) {
                        $validator->addError("size_name[$index]", str_replace('size', "size_name[$index]", $error));
                    }
                }
            }
        }
    }
    
    if (isset($data['size_price_adjustment']) && is_array($data['size_price_adjustment'])) {
        foreach ($data['size_price_adjustment'] as $index => $price) {
            $priceValidator = new Validator(['price' => $price]);
            $priceValidator->setRules(['price' => 'required|numeric']);
            
            if (!$priceValidator->validate()) {
                $errors = $priceValidator->getErrors();
                if (!empty($errors['price'])) {
                    foreach ($errors['price'] as $error) {
                        $validator->addError("size_price_adjustment[$index]", str_replace('price', "size_price_adjustment[$index]", $error));
                    }
                }
            }
        }
    }
    
    if (isset($data['stock_quantity_size']) && is_array($data['stock_quantity_size'])) {
        foreach ($data['stock_quantity_size'] as $index => $quantity) {
            $quantityValidator = new Validator(['quantity' => $quantity]);
            $quantityValidator->setRules(['quantity' => 'required|integer']);
            
            if (!$quantityValidator->validate()) {
                $errors = $quantityValidator->getErrors();
                if (!empty($errors['quantity'])) {
                    foreach ($errors['quantity'] as $error) {
                        $validator->addError("stock_quantity_size[$index]", str_replace('quantity', "stock_quantity[$index]", $error));
                    }
                }
            }
        }
    }

    if (isset($data['color_name']) && is_array($data['color_name'])) {
        foreach ($data['color_name'] as $index => $color) {
            $priceValidator = new Validator(['color' => $color]);
            $priceValidator->setRules(['color' => 'required|string']);
            if (!$priceValidator->validate()) {
                $errors = $priceValidator->getErrors();
                if (!empty($errors['color'])) {
                    foreach ($errors['color'] as $error) {
                        $validator->addError("color_name[$index]", str_replace('color', "color_name[$index]", $error));
                    }
                }
            }
        }
    }    

    if (isset($data['color_code']) && is_array($data['color_code'])) {
        foreach ($data['color_code'] as $index => $color) {
            $priceValidator = new Validator(['color' => $color]);
            $priceValidator->setRules(['color' => 'required|string']);
            if (!$priceValidator->validate()) {
                $errors = $priceValidator->getErrors();
                if (!empty($errors['color'])) {
                    foreach ($errors['color'] as $error) {
                        $validator->addError("color_code[$index]", str_replace('color', "color_code[$index]", $error));
                    }
                }
            }
        }
    }

    if (isset($data["stock_quantity_color"]) && is_array($data['stock_quantity_color'])) {
        foreach ($data['stock_quantity_color'] as $index => $quantity_color) {
            $priceValidator = new Validator(['quantity_color' => $quantity_color]);
            $priceValidator->setRules(['quantity_color' => 'required|string']);
            if (!$priceValidator->validate()) {
                $errors = $priceValidator->getErrors();
                if (!empty($errors['quantity_color'])) {
                    foreach ($errors['quantity_color'] as $error) {
                        $validator->addError("stock_quantity_color[$index]", str_replace('quantity_color', "stock_quantity_color[$index]", $error));
                    }
                }
            }
        }
    }

    if (isset($data["color_price_adjustment"]) && is_array($data['color_price_adjustment'])) {
        foreach ($data['color_price_adjustment'] as $index => $price_color) {
            $priceValidator = new Validator(['price_color' => $price_color]);
            $priceValidator->setRules(['price_color' => 'required|string']);
            if (!$priceValidator->validate()) {
                $errors = $priceValidator->getErrors();
                if (!empty($errors['price_color'])) {
                    foreach ($errors['price_color'] as $error) {
                        $validator->addError("color_price_adjustment[$index]", str_replace('price_color', "color_price_adjustment[$index]", $error));
                    }
                }
            }
        }
    }
 

    $uploadedImages = [];

    if (!empty($data["images"]["name"])) {
        foreach ($data["images"]["name"] as $index => $fileName) {
            $tmpPath = $data["images"]["tmp_name"][$index];
            $destinationPath = dirname(__DIR__)."/../public/uploads/" . $fileName; 
            move_uploaded_file($tmpPath, $destinationPath) ;
                $uploadedImages[] = [
                    "path" =>  $fileName,
                    "is_primary" => $index === 0 ? 1 : 0, 
                ];
         
            
        }
    }
    
    $data["images"] = $uploadedImages;

  
    $oldData = $data;

    $isValid = $isValid && empty($validator->getErrors());
    
    if (!$isValid) {
        $errors = $validator->getErrors();
        return $this->response->jsonEncode(["errors" => $errors , "oldData" => $oldData ]);

    }  
        $result =$this->productRepository->insertProduct($data);
        if($result){
             $this->response->jsonEncode(["success" => "create produt is succusful"]);
        }
    }
    

    public function show($id){
        $result = $this->productRepository->fetchById($id);
        $categories = $this->productRepository->selectCategories();
        return $this->response->render("admin/products/edit",["categories" => $categories,"product" => $result]);
    }

    public function update($data){
        $errors = [];
        $validator = new Validator($data);
        $validator->setRules([
            'title' => 'required|string|min:2|max:50',
            'category_id' => 'required',
            'description' => 'required|string|min:10|max:200',
            'base_price' => 'required|numeric',
            'stock' => 'required'
        ]);
        
        $isValid = $validator->validate();
        
        if (isset($data['size_name']) && is_array($data['size_name'])) {
            foreach ($data['size_name'] as $index => $sizeName) {
                $sizeValidator = new Validator(['size' => $sizeName]);
                $sizeValidator->setRules(['size' => 'required|string|min:2|max:50']);
                
                if (!$sizeValidator->validate()) {
                    $errors = $sizeValidator->getErrors();
                    if (!empty($errors['size'])) {
                        foreach ($errors['size'] as $error) {
                            $validator->addError("size_name[$index]", str_replace('size', "size_name[$index]", $error));
                        }
                    }
                }
            }
        }
        
        if (isset($data['size_price_adjustment']) && is_array($data['size_price_adjustment'])) {
            foreach ($data['size_price_adjustment'] as $index => $price) {
                $priceValidator = new Validator(['price' => $price]);
                $priceValidator->setRules(['price' => 'required|numeric']);
                
                if (!$priceValidator->validate()) {
                    $errors = $priceValidator->getErrors();
                    if (!empty($errors['price'])) {
                        foreach ($errors['price'] as $error) {
                            $validator->addError("size_price_adjustment[$index]", str_replace('price', "size_price_adjustment[$index]", $error));
                        }
                    }
                }
            }
        }
        
        if (isset($data['stock_quantity_size']) && is_array($data['stock_quantity_size'])) {
            foreach ($data['stock_quantity_size'] as $index => $quantity) {
                $quantityValidator = new Validator(['quantity' => $quantity]);
                $quantityValidator->setRules(['quantity' => 'required|integer']);
                
                if (!$quantityValidator->validate()) {
                    $errors = $quantityValidator->getErrors();
                    if (!empty($errors['quantity'])) {
                        foreach ($errors['quantity'] as $error) {
                            $validator->addError("stock_quantity_size[$index]", str_replace('quantity', "stock_quantity[$index]", $error));
                        }
                    }
                }
            }
        }

        if (isset($data['color_name']) && is_array($data['color_name'])) {
            foreach ($data['color_name'] as $index => $color) {
                $priceValidator = new Validator(['color' => $color]);
                $priceValidator->setRules(['color' => 'required|string']);
                if (!$priceValidator->validate()) {
                    $errors = $priceValidator->getErrors();
                    if (!empty($errors['color'])) {
                        foreach ($errors['color'] as $error) {
                            $validator->addError("color_name[$index]", str_replace('color', "color_name[$index]", $error));
                        }
                    }
                }
            }
        }    

        if (isset($data['color_code']) && is_array($data['color_code'])) {
            foreach ($data['color_code'] as $index => $color) {
                $priceValidator = new Validator(['color' => $color]);
                $priceValidator->setRules(['color' => 'required|string']);
                if (!$priceValidator->validate()) {
                    $errors = $priceValidator->getErrors();
                    if (!empty($errors['color'])) {
                        foreach ($errors['color'] as $error) {
                            $validator->addError("color_code[$index]", str_replace('color', "color_code[$index]", $error));
                        }
                    }
                }
            }
        }

        if (isset($data["stock_quantity_color"]) && is_array($data['stock_quantity_color'])) {
            foreach ($data['stock_quantity_color'] as $index => $quantity_color) {
                $priceValidator = new Validator(['quantity_color' => $quantity_color]);
                $priceValidator->setRules(['quantity_color' => 'required|string']);
                if (!$priceValidator->validate()) {
                    $errors = $priceValidator->getErrors();
                    if (!empty($errors['quantity_color'])) {
                        foreach ($errors['quantity_color'] as $error) {
                            $validator->addError("stock_quantity_color[$index]", str_replace('quantity_color', "stock_quantity_color[$index]", $error));
                        }
                    }
                }
            }
        }

        if (isset($data["color_price_adjustment"]) && is_array($data['color_price_adjustment'])) {
            foreach ($data['color_price_adjustment'] as $index => $price_color) {
                $priceValidator = new Validator(['price_color' => $price_color]);
                $priceValidator->setRules(['price_color' => 'required|string']);
                if (!$priceValidator->validate()) {
                    $errors = $priceValidator->getErrors();
                    if (!empty($errors['price_color'])) {
                        foreach ($errors['price_color'] as $error) {
                            $validator->addError("color_price_adjustment[$index]", str_replace('price_color', "color_price_adjustment[$index]", $error));
                        }
                    }
                }
            }
        }
    

        $uploadedImages = [];

        if (!empty($data["images"]["name"])) {
            foreach ($data["images"]["name"] as $index => $fileName) {
                $tmpPath = $data["images"]["tmp_name"][$index];
                $destinationPath = dirname(__DIR__)."/../public/uploads/" . $fileName; 
                move_uploaded_file($tmpPath, $destinationPath) ;
                    $uploadedImages[] = [
                        "path" => "uploads/". $fileName,
                        "is_primary" => $index === 0 ? 1 : 0, 
                    ];
            
                
            }
        }
        
        $data["images"] = $uploadedImages;

    
        $oldData = $data;

        $isValid = $isValid && empty($validator->getErrors());
        
        if (!$isValid) {
            $errors = $validator->getErrors();
            return $this->response->jsonEncode(["errors" => $errors , "oldData" => $oldData ]);

        }
            $id = $data["id"];
            $result =$this->productRepository->updatProduct($id ,$data);
            if($result){
                $this->response->jsonEncode(["success" => "update produt is succusful"]);
            }
    }


    public function delete($id){
        $result = $this->productRepository->remove($id);
        return $this->response->jsonEncode([ "message" => "product susscuful delete" ]);
    }

   





}