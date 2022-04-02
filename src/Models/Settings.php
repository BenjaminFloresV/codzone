<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class Settings
{
    private int $setting_id;
    private string $name;
    private string $value;
    private static bool|\PDO $conn;
    private static LoggerInterface $log;

    public function __construct()
    {
        self::$conn = Connection::dbConnection();
        self::$log = NewLogger::newLogger('SETTINGS_MODEL', 'FirePHPHandler');
    }

    public static function getAll(): bool|array
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{

            $sql = "SELECT * FROM settings";

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();


            if ($query) {
                $result = $st->fetchAll();
            }

        } catch (Exception $exception){
            self::$log->error('Something went wrong', array('exception'=> $exception));
        }
        return $result;
    }

    public function getOneSetting( string $name )
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{

            $sql = "SELECT * FROM settings WHERE name=:name";

            $st = self::$conn->prepare( $sql );
            $st->bindValue(':name', $name, PDO::PARAM_STR);
            $query = $st->execute();


            if ($query) {
                $result = $st->fetch();
            }

        } catch (Exception $exception){
            self::$log->error('Something went wrong', array('exception'=> $exception));
        }
        return $result;
    }

    public function updateSetting( $id, $value ): bool
    {
        $result = false;
        if( !self::$conn ) return $result;
        try {
            $sql = "UPDATE settings SET value=:value WHERE setting_id=:setting_id";
            $st = self::$conn->prepare($sql);

            $st->bindValue(':value', $value, PDO::PARAM_STR);
            $st->bindValue(':setting_id', $id, PDO::PARAM_INT);

            $result = $st->execute();

            if( $st->rowCount() === 0 ){
                $result = false;
            }

        } catch (Exception $exception){
            self::$log->error('Something went wrong', array('exception'=>$exception));
        }

        return $result;
    }
}












