<?php 

namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;
use App\Repositories\ReviewRepository;
use App\Core\Session;
use App\Core\validator;
Session::start();

class ReviewController extends Controller {

    private $productRepostory;
    private $reviewRepository;
    private $response;

    public function __construct(){
        $this->response = new Response();
        $this->productRepostory = new ProductRepository;
        $this->reviewRepository = new ReviewRepository;
     }


   public function  create(Request $request){
    $body = $request->getbody();
    $id = isset($body['id']) ? (int) $body['id'] : null;
    $product =  $this->productRepostory->fetchById($id);
    $this->render('customer/account/review' ,['product' => $product]);
    }

    public function store(Request $request){
        $data =  $request->getbody();
        $user_id = Session::get('id');

        $validator  = new validator($data);

        $validator->setRules([
            'rating' => 'required|numeric',
            'content' => 'required|string|min:10|max:200',
        ]);
        
        $isValid = $validator->validate();
         if(!$isValid){
            $errors = $validator->getErrors();
            return $this->response->render("customer/account/review",["errors" => $errors , "old" => $data]);
        }
        $data['user_id'] =  $user_id ;
         $review = $this->reviewRepository->createReview($data);
        if($review){
             $this->response->redirect("/customer/account/order");
        }
    }


    // public function reviewByProductId(Request $request){
    //     $body = $request->getbody();
    //     $id = isset($body['id']) ? (int) $body['id'] : null;
    // }

    public function reviewByUserId(){
        $user_id = Session::get('id');
        $reviews = $this->reviewRepository->getReviewByUserId($user_id);
        $products = [] ;
        foreach( $reviews as $review ){
            dump($review->getProductId());
            $products[] = $this->productRepostory->productId($review->getProductId());
        }

        $this->render('customer/account/myReview' ,['reviews' => $reviews , 'products' => $products]);

    }
    public function update(Request $request){
        $data = $request->getbody();
        $id = $data['review_id'];
        unset($data['review_id']);
       $review =  $this->reviewRepository->updateReview($id,$data);
       if(!$review){
       return  $this->response->jsonEncode(['error' => "don't update this review"]); 
       }
       return $this->response->jsonEncode(['success' => "review update succussful"]); 
    }

}