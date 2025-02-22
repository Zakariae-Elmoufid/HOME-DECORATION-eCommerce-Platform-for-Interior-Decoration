<?php
require_once __DIR__.'/../vendor/autoload.php';
use App\Core\Application;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$app = new Application(dirname(__DIR__));

require dirname(__DIR__).'/app/router/web.php';

$app->run();
