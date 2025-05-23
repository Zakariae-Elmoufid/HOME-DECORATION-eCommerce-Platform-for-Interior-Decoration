<?php 

namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\response;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;

class ProductController  {

    private $ProductService ;
    private $productRepository ;
    private $reviewRepository ;
    private $response ;

    public function __construct(){
        $this->ProductService = new ProductService() ; 
        $this->reviewRepository = new ReviewRepository();
        $this->productRepository = new ProductRepository();
        $this->response = new Response();
    }

    public function index(){
       $products = $this->ProductService->fetchAll();
       $this->response->render('admin/products/index',  $products);
    }
    
    public function create(){
        $this->ProductService->fetchCategory();
    }


    public function store(Request $request){
        $data = $request->getbody();
       $this->ProductService->store($data);
    }

    public function show(Request $request) {
        try {
            $body = $request->getBody();
            $id = isset($body['id']) ? (int) $body['id'] : null;
    
            if (!$id || $id <= 0) {
                throw new \Exception("Invalid product ID.");
            }
    
            $data = $this->ProductService->show($id);
            $product = $data['product'];

            if (!$product) {
                throw new \Exception('this  product not found .');
            }
    
            $this->response->render('customer/pageProduct', [
                "product" => $data['product'],
                "reviews" => $data['reviews'],
                "count" => $data['count'],
                "average" => $data['average'],
                "products" => $data['p'],
            ]);
    
        } catch (\Exception $e) {
            $this->response->render('error', [
                'message' => $e->getMessage()
            ]);
        }
    }
    

    public function getProduct(Request $request){
        try{
            $body = $request->getbody();
            $id = isset($body['id']) ? (int) $body['id'] : null;
            if (!$id || $id <= 0) {
                throw new \Exception("Invalid product ID.");
            }
            
            $data = $this->ProductService->show($id);
            if (!$data['product'] || !$data['categories']) {
                throw new \Exception('this  product not found .');
            }
             
            $this->response->render("admin/products/edit",["categories" => $data['categories'] ,"product" => $data['product']]);
        }catch(\Exception $e){
            $this->response->render('error', [
                'message' => $e->getMessage()
            ]);
        }
    }
    

    public function update(Request $request){
        $data = $request->getbody();
        $result = $this->ProductService->update($data);
        if(isset($result['errors']) && $result['errors']){
            $this->response->jsonEncode(["errors" => $result['errors'] ]);   
        }
        $this->response->jsonEncode(["success" =>$result['success']]);
    }

    public function deleteProductVariant(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $result  = $this->productRepository->deleteProductVariant($id);
        if( $result){
            $this->response->jsonEncode(['succes' => 'delete product varaint succesful']);
        }

    }

    public function editImages(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $images  = $this->productRepository->getImages($id);
        $this->response->render("admin/products/editImages",['images'=>$images ,"product_id" => $id]);

    }

    public function uploadImages(Request $request){
        $data = $request->getbody();
        $uploadedImages = [];
        if (!empty($data["images"]["name"])) {
            foreach ($data["images"]["name"] as $index => $fileName) {
                $tmpPath = $data["images"]["tmp_name"][$index];
                $destinationPath = dirname(__DIR__)."/../../public/uploads/" . $fileName; 
                move_uploaded_file($tmpPath, $destinationPath) ;
                    $uploadedImages[] = [
                        "path" =>  $fileName,
                        "is_primary" => $index === 0 ? 1 : 0, 
                    ];
            }
        }
        $data["images"] = $uploadedImages;
      
        $result = $this->productRepository->addImages($data,$data['product_id']);
        if (!empty($results['errors'])) {
            foreach ($results['errors'] as $error) {
                $this->response->renderError($error);
            }
        }
        $this->response->jsonEncode(["success" => "Images add successuf "]); 
    }

    public function deleteImage(Request $request){
        $data = $request->getbody();
        $id = $data['id'];
        $isDelete =$this->productRepository->deleteImage($id);
        if($isDelete){
            $this->response->jsonEncode(["success" => "Image delete successuf "]); 
        }
        $this->response->jsonEncode(["error" => "this image d'ont delete "]); 

    }

    public function setPrimaryImage(Request $request){
        $data = $request->getbody();
        $id = $data['id'];
        $isUpdate = $this->productRepository->setPrimaryImage($id);
        if($isUpdate){
            $this->response->jsonEncode(["success" => "setPrimaryImage is successuf "]); 
        }
        $this->response->jsonEncode(["error" => "setPrimaryImage failed  "]); 
    }



    public function delete(Request $request){
        $data = $request->getbody();
        $id = $data['id'];        
        $result = $this->productRepository->remove($id);
        if($result){
            $this->response->jsonEncode([ "success" => "product susscuful delete" ]);
        }
        $this->response->jsonEncode([ "error" => "delete product failed"]);
    }

}