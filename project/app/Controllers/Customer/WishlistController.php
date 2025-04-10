<?php


namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
// use App\Repositories\ProductRepository;


class WishlistController extends Controller {
    
    private $response;
    public function __construct(){
        $this->response = new Response();
    }
    public function index(){
        $this->response->render('customer/account/wishlist');
    }
}