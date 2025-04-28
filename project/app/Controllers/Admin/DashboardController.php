<?php 

namespace App\Controllers\Admin;

use App\Core\Response;
use App\Core\Request;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
class DashboardController {

    private $orderRepository;
    private $customerRepository;
    private $response;

    public function __construct(){
        $this->orderRepository = new OrderRepository;
        $this->customerRepository = new CustomerRepository;
        $this->response = new Response;
    }

    public function index(){
        $popularProducts = $this->orderRepository->getPopularProducts('current');
        $this->response->render('admin/index',['popularProducts' => $popularProducts,
        'sales' => $this->getSalesStats(),
        'orders' => $this->getOrderStats(),
        'customers' => $this->getCustomerStats(),
        'avgCart' => $this->getAvgCartStats()
        ]);

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

    public function getPopularProducts(Request $request) {
        $body = $request->getbody();
        $period  = isset($body['period']) ? $body['period'] : 'current';
        $popularProducts = $this->orderRepository->getPopularProducts($period);
        $this->response->jsonEncode(['products' => $popularProducts]);
    }

    private function getSalesStats(){
      $total = $this->orderRepository->totalRevenue();
      $currentMonth = $this->orderRepository->currentMonth();
      $previousMonth=$this->orderRepository->prevMonth();

      $percentChange = $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth) * 100 : 0;
      return [
        'total' => number_format($total, 2),
        'percentChange' => round($percentChange, 1),
        'direction' => $percentChange >= 0 ? 'up' : 'down'
    ];
    }

    private function getOrderStats(){
        $total = $this->orderRepository->completedOrders();
        $currentMonth = $this->orderRepository->currentMonthOrder();
        $previousMonth = $this->orderRepository->prevMonthOrder();
        $percentChange = $previousMonth > 0 ? (($currentMonth - $previousMonth) / $prevMonth) * 100 : 0;
        return [
            'total' => $total,
            'percentChange' => round($percentChange, 1),
            'direction' => $percentChange >= 0 ? 'up' : 'down'
        ];
    }

    private function getCustomerStats(){
       $total = $this->customerRepository->total_customers();
       $currentMonth = $this->customerRepository->currentMonth();
       $previousMonth = $this->customerRepository->prevMonth();

       $percentChange = $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth) * 100 : 0;
        
       return [
           'total' => $total,
           'percentChange' => round($percentChange, 1),
           'direction' => $percentChange >= 0 ? 'up' : 'down'
       ];
    }

    private function getAvgCartStats(){
        $total = $this->orderRepository->getAvgOrder();
        $currentMonthAvg = $this->orderRepository->currentMonthAvg();
        $prevMonthAvg = $this->orderRepository->prevMonthAvg();
        $percentChange = $prevMonthAvg > 0 ? (($currentMonthAvg - $prevMonthAvg) / $prevMonthAvg) * 100 : 0;

           return [
            'total' => number_format($total, 2),
            'percentChange' => round($percentChange, 1),
            'direction' => $percentChange >= 0 ? 'up' : 'down'
        ];

    }


}