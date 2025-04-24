<?php

namespace App\Controllers;
use App\Core\Session;
use App\Core\Response;

class ErrorController {

    private $response;

    public function __construct(){
        $this->response = new Response();
    }

    // public function notFound(){
    //     $this->response->render('admin/error/404');
    // }

    public function errorPermission(){
        $this->response->renderError(' Access denied');
    }
} 