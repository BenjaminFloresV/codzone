<?php

namespace CMS\Helpers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;

class NewLogger
{
    // This method creates a news logger for debugging tasks
    public static function newLogger($name , $handler) : LoggerInterface
    {
        $logger = new Logger($name);
        $handlerName = "\Monolog\Handler\\"."$handler";
        $logger->pushHandler(new $handlerName);

        return $logger;
    }
}