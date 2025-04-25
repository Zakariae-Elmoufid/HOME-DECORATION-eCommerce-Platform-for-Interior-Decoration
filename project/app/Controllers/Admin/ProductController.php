<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\response;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;

class ProductController extends Controller {

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

    public function show(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $data = $this->ProductService->show($id);
        $this->response->render('customer/pageProduct', [
        "product" => $data['product'] ,
        'reviews' => $data['reviews'] , 
        'count' => $data['count'],
        'average' => $data['average'],
         'products' => $data['p'],
         ]);
    }

    public function getProduct(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $data = $this->ProductService->show($id);
        $this->response->render("admin/products/edit",["categories" => $data['categories'] ,"product" => $data['product']]);
    }
    

    public function update(Request $request){
        $data = $request->getbody();
       
        $this->ProductService->update($data);
    }

    public function editImages(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $images  = $this->productRepository->getImages($id);
        $this->response->render("admin/products/editImages",['images'=>$images]);

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
        $this->jsonEncode(["success" => "Images add successuf "]); 
    }



    public function delete(Request $request){
        $data = $request->getbody();
        $id = $data['id'];
        $this->ProductService->delete($id);
    }

}