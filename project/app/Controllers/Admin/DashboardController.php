<?php 

namespace App\Controllers\Admin;

use App\Core\Response;
use App\Core\Request;
use App\Repositories\OrderRepository;
class DashboardController {

    private $orderRepository;
    private $response;

    public function __construct(){
        $this->orderRepository = new OrderRepository;
        $this->response = new Response;
    }

    public function index(){
        $this->response->render('admin/index');
    }

    public function getSalesData(Request $request){
      $body = $request->getbody();
      $year  = $body['year'];
      $salesData = $this->orderRepository->getMonthlyData($year);
      $availableYears = $this->orderRepository->getAvailableYears();
      if($salesData  || $availableYears ){
         $this->response->jsonEncode([
            'data' => $salesData,
            'years' => $availableYears
         ]);
      }

      

    }
}