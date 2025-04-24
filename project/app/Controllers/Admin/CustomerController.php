<?php
namespace App\Controllers\Admin;

use App\Core\Request;
use App\Core\Response;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;


class CustomerController {
   
    private $response;
    private $customerRepository;
    private $orderRepository;

    public function __construct(){
        $this->response = new Response();
        $this->customerRepository = new CustomerRepository();
        $this->orderRepository = new OrderRepository();
    }

    public function index(){
        $customers = $this->customerRepository->fetchAllCustomer();
        $total_customers = $this->customerRepository->total_customers();
        $new_customers = $this->customerRepository->new_customers();
        $avg_order_value = $this->orderRepository->totalRevenue();
        
        $this->response->render('admin/customer/index' ,["customers" => $customers ,
         "total_customers" => $total_customers ,
        "new_customers" => $new_customers,
        "avg_order_value" => $avg_order_value]);
    }
}