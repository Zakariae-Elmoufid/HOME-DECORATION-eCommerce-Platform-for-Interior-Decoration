<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Core\Application;

$app = new Application(dirname(__DIR__));

$app->router->get('/home','HomeController@index');

$app->run();
