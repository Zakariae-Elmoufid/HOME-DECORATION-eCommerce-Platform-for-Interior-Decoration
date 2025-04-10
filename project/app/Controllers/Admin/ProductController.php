<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\response;
use App\Services\ProductService;
use App\Repositories\ReviewRepository;

class ProductController extends Controller {

    private $ProductService ;
    private $reviewRepository ;
    private $response ;

    public function __construct(){
        $this->ProductService = new ProductService() ; 
        $this->reviewRepository = new ReviewRepository();
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

    public function delete(Request $request){
        $data = $request->getbody();
        $id = $data['id'];
        $this->ProductService->delete($id);
    }

}