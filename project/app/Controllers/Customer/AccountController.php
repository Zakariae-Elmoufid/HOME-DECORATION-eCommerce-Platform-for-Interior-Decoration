<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Repositories\OrderRepository;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
Session::start();

class AccountController extends Controller {
    
    private $orderRepository;
    private $AccountRepository;
    private $userRepository;
    private $response;

    public function __construct(){
       $this->orderRepository = new OrderRepository();
       $this->AccountRepository = new AccountRepository();
       $this->userRepository = new UserRepository();
       $this->response = new Response();
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

    public function account(){
        $customer = $this->userRepository->getUserById(Session::get('id'));
        $user_Address  = $this->AccountRepository->getUserAdress(Session::get('id'));
        $this->render('customer/account/accountDetails' ,['customer' => $customer ,  'userAddress' => $user_Address->getId() ? $user_Address : null]);
    }

    public function order(){
        $orders = $this->orderRepository->getOrderItemByUserId(Session::get('id'));
        $this->render('customer/account/order' ,['orders' => $orders]);
    }

    public function update(Request $request){
       $data = $request->getbody();

       $errors = [];
       $validator = new Validator($data);
       
       $validator->setRules([
           'email' => 'required|email',
           'username' => 'required|min:4|max:20'
       ]);
       
       if (!$validator->validate()) {
           $errors = $validator->getErrors();
           $this->response->jsonEncode(['errorValidate' => $errors]);
       }

       $id = $data['user_id'];
       unset($data["user_id"]);
       $isUpdate = $this->userRepository->updateUser($id,$data);

       if(!$isUpdate){
        $this->response->jsonEncode(['errore' => "this information don't update"]);
       }
       $this->response->jsonEncode(['success' => "update success"]);
    }

    public function updateAddress(Request $request){
     $data = $request->getbody();
     $errors = [];
     $validator = new Validator($data);
     
     $validator->setRules([
         'email' => 'required|email',
         'first_name' => 'required|min:4|max:20',
         'last_name' => 'required|min:4|max:20',
         'postal_code' => 'required|min:4|numeric',
         'city' => 'required|min:4|max:50|string',
         'country' => 'required|min:2|max:50|string',
         'address' => '|min:8|max:50|string',
         'phone' => 'required|min:8|max:50|string',
     ]);
     
     if (!$validator->validate()) {
         $errors = $validator->getErrors();
         $this->response->jsonEncode(['errorValidate' => $errors,]);
     }
     $id = $data['id'];
     unset($data["id"]);
     $isUpdate = $this->AccountRepository->updateAddress($id,$data);
     if(!$isUpdate){
      $this->response->jsonEncode(['error' => "this information don't update"]);
     }
      $this->response->jsonEncode(['success' => "update success"]);
    } 

    public function  addAddress(Request $request){
         $data = $request->getbody();
        $user_addres =  $this->orderRepository->createUserAddresse($data);
        if(!$user_addres){
            $this->response->jsonEncode(['error' => "this information don't update"]);
           }
            $this->response->jsonEncode(['success' => "update success"]);
        
    }

}

