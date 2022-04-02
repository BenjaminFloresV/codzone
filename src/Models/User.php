<?php

namespace CMS\Models;
use CMS\Helpers\Connection;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use JetBrains\PhpStorm\NoReturn;
use PDO;
use Psr\Log\LoggerInterface;

class User
{
    private int $user_id;
    private bool $isAdmin;
    private string $username;
    private string $email;
    private string $password;
    private string $apiKey;
    private bool|PDO $conn;
    private LoggerInterface $log;

    public function __construct()
    {
        $this->log = NewLogger::newLogger('USER_MODEL', 'FirePHPHandler');
        $this->conn = Connection::dbConnection();
    }

    public function storeFormValues ( array $data ): bool
    {
        $result = true;
        try {
            if ( isset($data['username']) ) $this->email = (string) preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['username']);
            if ( isset($data['username']) ) $this->username = (string) preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "",$data['username']);
            if ( isset($data['password']) ) $this->password = ( string ) $data['password'];
        } catch ( \Exception $exception){
            $this->log->info('Data cannot be stored :(', array('exception'=>$exception));
            $result = false;
        }
        return $result;
    }

    public function login()
    {
        $result = false;
        try {
            $sql = "SELECT * FROM users WHERE email=:email OR username=:username";

            $st = $this->conn->prepare($sql);
            $st->bindValue(':email', $this->email, PDO::PARAM_STR);

            $st->bindValue(':username', $this->username, PDO::PARAM_STR);


            $query = $st->execute();

            if( $query ) {

                $userData = $st->fetch(PDO::FETCH_ASSOC);

                if( password_verify($this->password, $userData['password'] ) ){
                    $result = $userData;
                }
            }

        } catch ( \Exception $exception ){
            $this->log->error('Something went wrong while trying to login', array('exception'=> $exception));

        }
        return $result;
    }


    #[NoReturn] public static function logout()
    {
        if( isset($_SESSION['admin']) ){
            Helpers::deleteSession('admin');
            header("Location:".BASE_URL.'/admin-login');
            exit();

        }else if( isset($_SESSION['user']) ) {
            Helpers::deleteSession('user');
            header("Location:".BASE_URL);
            exit();
        }else {
            header("Location:".BASE_URL);
            exit();
        }
    }

}
























