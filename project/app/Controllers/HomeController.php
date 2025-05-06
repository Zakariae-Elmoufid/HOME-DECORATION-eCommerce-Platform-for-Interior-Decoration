<?php
namespace App\Controllers;
use App\core\Request;
use App\core\Response;
use App\core\Validator;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;

class HomeController {


    private $CategoryService;
    private $ProductService;
    private $productRepository;
    private $reviewRepository;
    private $response;

    public function __construct(){
        $this->CategoryService = new CategoryService();
        $this->reviewRepository = new ReviewRepository(); 
        $this->ProductService = new ProductService() ; 
        $this->productRepository = new ProductRepository();
        $this->response = new Response();
    }

    public function index()
    {
        $categories =  $this->CategoryService->fechAll();
        $products = $this->ProductService->fetchAll();
        $newProducts =   $this->productRepository->getNewProducts();
        

        return $this->response->render('home', [
            'categories' => $categories,
            'products' => $products,
            'newProducts' =>$newProducts
        ]);
    }

    public function about(){
        $reviews = $this->reviewRepository->topThreeReviews();
        return $this->response->render('about',["reviews" => $reviews]);
    }

    public function contact(){
        return $this->response->render('contact');
    }

    public function product()
    {
        $products = $this->ProductService->fetchAll();
        return $this->response->render('products', [
            'products' => $products
        ]);
    }
    
    public function search(Request $request){
        $body = $request->getbody();
        $products = $this->productRepository->fechByKeyWord($body['keyword']);
         $this->response->jsonEncode(["products" => $products]);
    }

    public function getProductsByCategory(Request $request){
        $data = $request->getbody();
        $id = $data['category'];
        $products = $this->productRepository->getProductsByCategory($id);
        return $this->response->render('customer/productsByCategory', ['products' => $products ]);
    }

    public function productsPaginator(Request $request){
     $data =  $request->getbody();
     $page = $data['page'];
     $products = $this->productRepository->paginationProduct($page);
     if(!$products){
        $this->response->jsonEncode(['errors' => 'error']);
     }
     $this->response->jsonEncode(['products' => $products , 'totalPages' => $page ]);
    }

    







}