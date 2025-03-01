<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Core\Application;
use App\Controllers\HomeController;

$app = new Application(dirname(__DIR__));

$app->router->get('/','HomeController@index');
$app->router->post('/createCategory',[HomeController::class,'create']);
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
// $app->get('/dashboard', [DashboardController::class, 'index'])->middleware('/dashboard', AuthMiddleware::class);
// $app->get('/login', [AuthController::class, 'login']);