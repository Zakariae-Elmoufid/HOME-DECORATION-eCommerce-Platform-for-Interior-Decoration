<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;

class ProductController extends Controller{

    public function index(){
        $this->render('admin/products/index');
    }
}