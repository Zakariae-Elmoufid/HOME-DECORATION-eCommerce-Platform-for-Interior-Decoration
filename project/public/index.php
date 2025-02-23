<?php
require_once __DIR__.'/../vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// require dirname(__DIR__).'/app/router/web.php';
// $app = new Application(dirname(__DIR__));


use App\Core\Application;

$app = new Application(dirname(__DIR__));

$app->router->get('/home','HomeController@index');
$app->router->post('/createCategory',[HomeController::class,'create']);
$app->run();
