<?php




ini_set( "display_errors", true );
error_reporting(0);
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use CMS\Helpers\Connection;
use Pecee\SimpleRouter\SimpleRouter;

require __DIR__.'/../config.php';
require __DIR__.'/../vendor/autoload.php';
require __DIR__ . '/../vendor/pecee/simple-router/helpers.php';

foreach ( glob(__DIR__ . '/../routes/*.php') as $filename){
    require $filename;
}

SimpleRouter::start();



















