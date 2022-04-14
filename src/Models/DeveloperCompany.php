<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use \Psr\Log\LoggerInterface;
use CMS\Models\Singleton\Singleton;

class DeveloperCompany extends Singleton
{
    private int $company_id;
    private string $name;
    private int $employees;
    private string $foundationDate;
    private string $description;
    private string $image;
    private static bool|PDO $conn;
    private static LoggerInterface $log;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->company_id;
    }

    public function setId( int $id){
        $this->company_id = $id;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage( string $image){
        $this->image = $image;
    }

    protected function __construct()
    {

        self::$log = NewLogger::newLogger('COMPANY_CLASS', 'FirePHPHandler');
        self::$conn = Connection::dbConnection();

        // Si no se instancia la clase, no se emitira ningun mensaje desde el log a FirePHP

        self::$log->info('The class has been created');
    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }


    public function storeFormValues(array $data): bool
    {
        $result = true;
        try {
            self::$log->info('Trying to store data from the form');
            //self::$log->info("Datos: ", array('data' => $data));
            if ( isset($data['company_id'] ) ) $this->company_id = (int) $data['company_id'];
            if ( isset($data['name'] ) ) $this->name = (string) preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['name'] );
            if ( isset($data['employees'] ) ) $this->employees = (int) $data['employees'];

            // Parse and store the foundation date
            if ( isset($data['foundationDate']) ) {
                $foundationDate = explode ( '/', $data['foundationDate'] );

                if ( count($foundationDate) == 3 ) {
                    list ( $d, $m, $y ) = $foundationDate;
                    $this->foundationDate = mktime ( 0, 0, 0, $m, $d, $y );
                }
            }
            if ( isset($data['description'] ) ) $this->description = (string) $data['description'];
            if ( isset($data['image'] ) ) $this->image = (string) $data['image'];


            self::$log->info("Data recieved.");

        }catch (Exception $exception){
            self::$log->error('Cannot save data from the form', array('exception'=>$exception));
            $result = false;
        }

        return $result;
    }

    public static function getAll(): bool|array
    {
        $result = false;
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to retrieve Companies data...');
            $sql = "SELECT *, UNIX_TIMESTAMP(foundation) AS foundationDate FROM company";

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();


            if ($query) {
                $result = $st->fetchAll();
                self::$log->notice('Companies data collected successfully', array( 'company_list' => $result ) );
            }

        } catch (Exception $exception){
            self::$log->error('Companies data cannot be collected', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public static function getById( $id, bool $join = false )
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            self::$log->info('Trying to retrieve Company data...');
            $sql = "SELECT *, UNIX_TIMESTAMP(foundation) AS foundationDate FROM company WHERE company_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":id", $id, PDO::PARAM_INT );
            $query = $st->execute();

            if($query){
                $result = $st->fetch();

                if ( $result  ){
                    self::$log->notice('One Company data collected successfully', array( 'company' => $result ) );
                }

            }

        } catch (Exception $exception) {
            self::$log->error('One Company cannot be collected', array( 'exception' => $exception ));
        }
        return $result;
    }

    public function insert(): bool
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            self::$log->info('Trying to save data...');
            $sql = "INSERT INTO company ( company_id, name, employees, foundation, description, image ) VALUES(NULL, :company_name, :employees, FROM_UNIXTIME(:foundation), :description, :image )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(":company_name", $this->name, PDO::PARAM_STR );
            $st->bindValue(":employees", $this->employees, PDO::PARAM_INT);
            $st->bindValue(":foundation", $this->foundationDate, PDO::PARAM_INT);
            $st->bindValue(":description", $this->description, PDO::PARAM_STR);
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $result = $st->execute();

            if ( $result){
                self::$log->info("A Company has been created successfully: $this->name");
            }

        } catch (Exception $exception){
            self::$log->error('Company cannot be created', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public function update(): bool {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        self::$log->info("Company data with id {$this->company_id} is updating...");
        try{
            $sql = "UPDATE company SET name = :company_name, employees = :employees, foundation = FROM_UNIXTIME(:foundation), description = :description, image = :image WHERE company_id = :company_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':company_id', $this->company_id, PDO::PARAM_INT );
            $st->bindValue(':company_name', $this->name, PDO::PARAM_STR);
            $st->bindValue(":employees", $this->employees, PDO::PARAM_INT);
            $st->bindValue(":foundation", $this->foundationDate, PDO::PARAM_INT);
            $st->bindValue(":description", $this->description, PDO::PARAM_STR);
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $result = $st->execute();

            if ( $result ){
                self::$log->info("Company data with id {$this->company_id} has been updated successfully");
            }


        } catch (Exception $exception){
            self::$log->error('Company cannot be updated', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public function delete(): bool
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            self::$log->notice("Trying to delete the company...");
            $sql = "DELETE FROM company WHERE company_id = :company_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':company_id', $this->company_id, PDO::PARAM_INT );
            $result = $st->execute();

            if ( $result ){
                self::$log->notice("Company with id $this->company_id has been deleted");
            }

        } catch (Exception $exception){
            self::$log->error("Company with id $this->company_id cannot be deleted", array('exception' => $exception ) );
        }
        return $result;
    }

}