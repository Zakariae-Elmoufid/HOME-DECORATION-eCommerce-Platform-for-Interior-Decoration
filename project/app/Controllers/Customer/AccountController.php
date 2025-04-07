<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\OrderRepository;

class AccountController extends Controller {
    
    private $orderRepository;

    public function __construct(){
       $this->orderRepository = new OrderRepository();
    }
    
    public function index(){
        Session::start();
        $customer = [
        'username' => Session::get('username'),
        'email' => Session::get('email'),
        ];
        $orders = $this->orderRepository->getOrderByUserId(Session::get('id'));
       
        $this->render('customer/account' ,['customer' => $customer , 'orders' => $orders ]);

       
    }


}

