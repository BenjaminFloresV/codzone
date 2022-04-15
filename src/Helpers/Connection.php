<?php

namespace CMS\Helpers;


use Exception;
use PDO;
use Psr\Log\LoggerInterface;


class Connection
{

    private static LoggerInterface $log;

    public static function dbConnection(): bool|PDO
    {
        self::$log = NewLogger::newLogger('DATABASE','FirePHPHandler');

        $result = false;

        try {

            self::$log->info('Connecting to Database');
            $result = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

            self::$log->info('Database connection established');


        } catch (Exception $exception) {
            self::$log->emergency("Oh no, cannot connect to the Database", array('exception' => $exception));
        }

        return $result;
    }
}

