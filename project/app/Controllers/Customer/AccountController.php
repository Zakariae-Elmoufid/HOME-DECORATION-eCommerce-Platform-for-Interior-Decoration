<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Repositories\OrderRepository;
use App\Repositories\AccountRepository;
Session::start();

class AccountController extends Controller {
    
    private $orderRepository;
    private $AccountRepository;

    public function __construct(){
       $this->orderRepository = new OrderRepository();
       $this->AccountRepository = new AccountRepository();
    }
    
    public function index(){
        $customer = [
        'username' => Session::get('username'),
        'email' => Session::get('email'),
        ];
        $orders = $this->orderRepository->getOrderByUserId(Session::get('id'));
        $user_Address  = $this->AccountRepository->getUserAdress(Session::get('id'));
        $this->render('customer/account/index' ,['customer' => $customer , 'orders' => $orders , 'userAddress' => $user_Address]);
    }

    public function order(){
        $orders = $this->orderRepository->getOrderItemByUserId(Session::get('id'));
        dump($orders);
        $this->render('customer/account/order' ,['orders' => $orders]);

    }


}

