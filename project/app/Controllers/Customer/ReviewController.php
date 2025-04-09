<?php 

namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;

class ReviewController extends Controller {

    private $productRepostory;

    public function __construct(){
        $this->productRepostory = new ProductRepository;
     }


   public function  create(Request $request){
    $body = $request->getbody();
    $id = isset($body['id']) ? (int) $body['id'] : null;
    $product =  $this->productRepostory->fetchById($id);
    $this->render('customer/account/review' ,['product' => $product]);

    }

}