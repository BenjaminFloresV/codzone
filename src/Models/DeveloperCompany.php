<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;


class DeveloperCompany
{
    private int $company_id;
    private string $name;
    private int $employees;
    private string $foundationDate;
    private string $description;
    private string $image;
    private static $conn;
    private static $log;

    public function getName(){
        return $this->name;
    }

    public function setName(string $name){
        $this->name = $name;
    }

    public function getId(){
        return $this->company_id;
    }

    public function setId( int $id){
        $this->company_id = $id;
    }

    public function getImage(){
        return $this->image;
    }

    public function setImage( string $image){
        $this->image = $image;
    }

    public function __construct()
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


    public function storeFormValues(array $data)
    {
        if( !self::$conn ) return false; // Verify database connection
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
        }


    }

    public static function getAll(): bool|array
    {
        if( !self::$conn ) return false; // Verify database connection

        try{
            self::$log->info('Trying to retrieve Companies data...');
            $sql = "SELECT *, UNIX_TIMESTAMP(foundation) AS foundationDate FROM company";

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();


            if ($query) {
                $companies = $st->fetchAll();
                self::$log->notice('Companies data collected successfully', array( 'company_list' => $companies ) );
                return $companies;
            }

        } catch (Exception $exception){
            self::$log->error('Companies data cannot be collected', array( 'exception' => $exception ) );
            return false;
        }
        return false;
    }

    public static function getById( $id, bool $join = false )
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to retrieve Company data...');
            $sql = "SELECT *, UNIX_TIMESTAMP(foundation) AS foundationDate FROM company WHERE company_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":id", $id, PDO::PARAM_INT );
            $query = $st->execute();



            if($query){
                $company = $st->fetch();
                self::$log->info($company);

                if ( !$company  ){
                    self::$log->notice("The Company with id: $id do not exists." );
                    return false;
                }
                self::$log->notice('One Company data collected successfully', array( 'company' => $company ) );
                return $company;
            }

        } catch (Exception $exception) {
            self::$log->error('One Company cannot be collected', array( 'exception' => $exception ));
            return false;
        }

        return false;

    }

    public function insert(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to save data...');
            $sql = "INSERT INTO company ( company_id, name, employees, foundation, description, image ) VALUES(NULL, :company_name, :employees, FROM_UNIXTIME(:foundation), :description, :image )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(":company_name", $this->name, PDO::PARAM_STR );
            $st->bindValue(":employees", $this->employees, PDO::PARAM_INT);
            $st->bindValue(":foundation", $this->foundationDate, PDO::PARAM_INT);
            $st->bindValue(":description", $this->description, PDO::PARAM_STR);
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $query = $st->execute();

            if ( $query){
                self::$log->info("A Company has been created successfully: $this->name");
                return true;
            }

            return true;

        } catch (Exception $exception){
            self::$log->error('Company cannot be created', array( 'exception' => $exception ) );
            return false;
        }
    }

    public function update(): bool {
        if( !self::$conn ) return false; // Verify database connection
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
            $query = $st->execute();

            if ( $query ){
                self::$log->info("Company data with id {$this->company_id} has been updated successfully");
                return true;
            }

            self::$log->info("Company could not be updated");
            return false;

        } catch (Exception $exception){
            self::$log->error('Company cannot be updated', array( 'exception' => $exception ) );
            return false;
        }

        return false;
    }

    public function delete(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->notice("Trying to delete the company...");
            $sql = "DELETE FROM company WHERE company_id = :company_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':company_id', $this->company_id, PDO::PARAM_INT );
            $query = $st->execute();

            if ( $query ){
                self::$log->notice("Company with id $this->company_id has been deleted");
                return true;
            }

        } catch (Exception $exception){
            self::$log->error("Company with id $this->company_id cannot be deleted", array('exception' => $exception ) );
            return false;
        }

        return false;
    }

}