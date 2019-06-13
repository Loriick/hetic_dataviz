<?php
header("Access-Control-Allow-Origin: *");

require_once __DIR__.'/../vendor/autoload.php';

use App\router\Router;

$router = new Router();

if(isset($_GET['to'])) {
    Router::RouteTo($_GET);
}
else{
    $args['to'] = '/';
    Router::RouteTo( $args );
}

