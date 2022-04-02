<?php


namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;


class WeaponCategory
{
    private int $wpcategory_id;
    private string $name;
    private string $image;
    private static bool|PDO $conn;
    private static LoggerInterface $log;

    public function getId(): int
    {
        return $this->wpcategory_id;
    }

    public function setId(int $wpcategory_id)
    {
        $this->wpcategory_id = $wpcategory_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName( string $name)
    {
        $this->name = $name;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image)
    {
        $this->image = $image;
    }

    public function __construct()
    {
        self::$log = NewLogger::newLogger('WP_CAT_CLASS', 'FirePHPHandler');
        self::$conn = Connection::dbConnection();
    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues ( array $data ): bool
    {
        // Store all the parameters
        $result = true;
        try {
            if ( isset( $data['wpcategory_id'] ) ) $this->wpcategory_id = (int) $data['wpcategory_id'];
            if ( isset( $data['name'] ) ) $this->name = (string) preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['name'] );
            if ( isset( $data['image'] ) ) $this->image = (string) preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['image'] );
        } catch ( Exception $exception) {
            self::$log->error('Something went wrong while trying to store form data');
            $result = false;
        }
        return $result;
    }



    public static function getAll(): bool|array
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            $sql = "SELECT * FROM weapon_category";

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();

            if ($query) {
                $result = $st->fetchAll();
                self::$log->notice('Weapon Categories data collected successfully', array( 'weapon_categories' => $result ) );
            }

        } catch (Exception $exception){
            self::$log->error('Weapon Category data cannot be collected', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public static function getById( int $id )
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            $sql = "SELECT * FROM weapon_category WHERE wpcategory_id = :id";

            $st = self::$conn->prepare( $sql );
            $st->bindValue(":id", $id, PDO::PARAM_INT );
            $query = $st->execute();

            if($query){
                $result = $st->fetch();

                if ( !$result  ){
                    self::$log->notice("The Weapon Category with id: $id do not exists." );
                }
                self::$log->notice('One Weapon Category data collected successfully', array( 'weaponcategory' => $result ) );
            }

        } catch (Exception $exception) {
            self::$log->error('One Weapon Category cannot be collected', array( 'exception' => $exception ));
        }
        return $result;

    }

    public function insert(): bool
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            $sql = "INSERT INTO weapon_category ( wpcategory_id, name, image ) VALUES(NULL, :name, :image)";
            $st = self::$conn->prepare($sql);
            $st->bindValue(":name", $this->name, PDO::PARAM_STR );
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $result = $st->execute();

            if ( !$result ){
                self::$log->notice("Weapon Category has not been created");
            }


        } catch (Exception $exception){
            self::$log->error('Weapon Category cannot be created', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public function update(): bool
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            $sql = "UPDATE weapon_category SET name = :name, image = :image WHERE wpcategory_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':id', $this->wpcategory_id, PDO::PARAM_INT );
            $st->bindValue(':name', $this->name, PDO::PARAM_STR);
            $st->bindValue( 'image', $this->image, PDO::PARAM_STR );
            $result = $st->execute();

            if ( $result ){
                self::$log->info("Weapon Category data with id {$this->wpcategory_id} has been updated successfully");
            }

        } catch (Exception $exception){
            self::$log->error('Weapon Category cannot be updated', array( 'exception' => $exception ) );
        }
        return $result;
    }

    public function delete(): bool
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            $sql = "DELETE FROM weapon_category WHERE wpcategory_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':id', $this->wpcategory_id, PDO::PARAM_INT );
            $result = $st->execute();

            if ( $result ){
                self::$log->notice("Weapon Category with id $this->wpcategory_id has been deleted");
            }

        } catch (Exception $exception){
            self::$log->error("Weapon Category with id $this->wpcategory_id cannot be deleted", array('exception' => $exception ) );
        }

        return $result;
    }


}

