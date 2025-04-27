<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Response;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;

class ProductService {
    
   private $productRepository;
   private $reviewRepository;
   private $response;

   public function __construct(){
       $this->productRepository = new ProductRepository();
       $this->reviewRepository = new ReviewRepository();
       $this->response = new Response();
   } 

   public function fetchAll(){
    $products = $this->productRepository->selectAll();

    $categories = $this->productRepository->selectCategories();
    
    $countProducts = $this->productRepository->countProducts();
    $countAvailable = $this->productRepository->countAvailable();
    $countCategories = $this->productRepository->countCategories();
     
    return $data =  [
        'products' => $products,
        'categories' => $categories ,
        "countProducts" => $countProducts,
        "countAvailable" => $countAvailable ,
        "countCategories" => $countCategories
    ];
    
     
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
        'stock' => 'required|numeric'
    ]);
    
    $isValid = $validator->validate();
   
 

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

    
    if (!$isValid) {
        $errors = $validator->getErrors();
        return $this->response->jsonEncode(["errors" => $errors , "oldData" => $oldData]);
    }  
        $result =$this->productRepository->insertProduct($data);
        
        if($result){
            return $this->response->jsonEncode(["success" => "create produt is succusful"]);
        }
    }
    

    public function show($id){
        $result = $this->productRepository->fetchById($id);
        $categories = $this->productRepository->selectCategories();
        $reviews = $this->reviewRepository->getReviewByProduct($id);
        $AvgandCountReviews = $this->reviewRepository-> avgAndCountReview($id);
        $products = $this->productRepository->selectAll();
        
        $data = [
            "p" => $products,
            "categories" => $categories,
            "product" => $result,
            "reviews" => $reviews,
            "count"   =>  $AvgandCountReviews->total_reviews,
            "average" => $AvgandCountReviews->avg_rating,
        ];
        return $data;
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
        if (!$isValid) {
            $errors = $validator->getErrors();
             $this->response->jsonEncode(["errors" => $errors ]);

        }   
            
            $id = $data["id"];
            
            $result =$this->productRepository->updatProduct($id ,$data);
            if($result){
                $this->response->jsonEncode(["success" => "update produt is succusful"]);
            }
    }


    

   





}