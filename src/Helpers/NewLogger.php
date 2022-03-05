<?php

namespace CMS\Helpers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class NewLogger
{
    public static function newLogger($name , $handler) : LoggerInterface
    {
        $logger = new Logger($name);
        $handlerName = "\Monolog\Handler\\"."$handler";
        $logger->pushHandler(new $handlerName);

        return $logger;
    }
}