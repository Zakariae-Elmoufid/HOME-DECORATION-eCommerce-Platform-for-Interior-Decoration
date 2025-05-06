<?php

namespace App\Controllers\Customer;

class CustomerController  {

  public function index(){
    $this->response->render('customer/index');
  }

}