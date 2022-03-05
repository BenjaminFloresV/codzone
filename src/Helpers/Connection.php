<?php

namespace CMS\Helpers;


use Exception;
use PDO;



class Connection
{

    private static $log;

    public static function dbConnection(): bool|PDO
    {
        self::$log = NewLogger::newLogger('DATABASE','FirePHPHandler');


        try {

            self::$log->info('Connecting to Database');
            $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);

            self::$log->info('Database connection established');
            return $conn;

        } catch (Exception $exception) {

            self::$log->emergency("Oh no, cannot connect to the Database", array('exception' => $exception));
            return false;
        }
    }
}

