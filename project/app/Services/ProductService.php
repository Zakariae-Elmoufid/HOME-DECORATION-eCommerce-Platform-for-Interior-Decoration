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
    
    dump(['products' => $products , 'categories' => $categories ,  $countProducts]);
    $this->response->render('admin/products/index', ['products' => $products , 'categories' => $categories , "countProducts" => $countProducts, "countAvailable" => $countAvailable  , "countCategories" => $countCategories]);
   }


   


 


}