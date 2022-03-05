<?php


namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;


class WeaponCategory
{
    private int $wpcategory_id;
    private string $name;
    private string $image;
    private static $conn;
    private static $logger;

    public function getId()
    {
        return $this->wpcategory_id;
    }

    public function setId(int $wpcategory_id)
    {
        $this->wpcategory_id = $wpcategory_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName( string $name)
    {
        $this->name = $name;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage(string $image)
    {
        $this->image = $image;
    }

    public function __construct()
    {


        self::$logger = NewLogger::newLogger('WP_CAT_CLASS', 'FirePHPHandler');
        self::$conn = Connection::dbConnection();

    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues ( array $data ) {

        // Store all the parameters
        if ( isset( $data['wpcategory_id'] ) ) $this->wpcategory_id = (int) $data['wpcategory_id'];
        if ( isset( $data['name'] ) ) $this->name =  preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['name'] );
        if ( isset( $data['image'] ) ) $this->image = preg_replace( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['image'] );
    }



    public static function getAll()
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            $sql = "SELECT * FROM weapon_category";

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();



            if ($query) {
                $catData = $st->fetchAll();
                self::$logger->notice('Weapon Categories data collected successfully', array( 'weapon_categories' => $catData ) );
                return $catData;
            }

        } catch (Exception $exception){
            self::$logger->error('Weapon Category data cannot be collected', array( 'exception' => $exception ) );
            return false;
        }
        return false;
    }

    public static function getById( $id, bool $join = false )
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            $sql = "SELECT * FROM weapon_category WHERE wpcategory_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":id", $id, PDO::PARAM_INT );
            $query = $st->execute();

            if($query){
                $one_cat_data = $st->fetch();

                if ( !$one_cat_data  ){
                    self::$logger->notice("The Weapon Category with id: $id do not exists." );
                    return false;
                }
                self::$logger->notice('One Weapon Category data collected successfully', array( 'weaponcategory' => $one_cat_data ) );
                return $one_cat_data;
            }

        } catch (Exception $exception) {
            self::$logger->error('One Weapon Category cannot be collected', array( 'exception' => $exception ));
            return false;
        }

        return false;

    }

    public function insert(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            $sql = "INSERT INTO weapon_category ( wpcategory_id, name, image ) VALUES(NULL, :name, :image)";
            $st = self::$conn->prepare($sql);
            $st->bindValue(":name", $this->name, PDO::PARAM_STR );
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $query = $st->execute();

            if ( $query ){
                self::$logger->notice("Weapon Category has been created successfully: $this->name");
                return true;
            }



        } catch (Exception $exception){
            self::$logger->error('Weapon Category cannot be created', array( 'exception' => $exception ) );
            return false;
        }
        return false;
    }

    public function update(): bool {
        if( !self::$conn ) return false; // Verify database connection
        try{
            $sql = "UPDATE weapon_category SET name = :name, image = :image WHERE wpcategory_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':id', $this->wpcategory_id, PDO::PARAM_INT );
            $st->bindValue(':name', $this->name, PDO::PARAM_STR);
            $st->bindValue( 'image', $this->image, PDO::PARAM_STR );
            $query = $st->execute();

            if ( $query ){
                self::$logger->info("Weapon Category data with id {$this->wpcategory_id} has been updated successfully");
                return true;
            }

        } catch (Exception $exception){
            self::$logger->error('Weapon Category cannot be updated', array( 'exception' => $exception ) );
            return false;
        }

        return false;
    }

    public function delete(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            $sql = "DELETE FROM weapon_category WHERE wpcategory_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':id', $this->wpcategory_id, PDO::PARAM_INT );
            $query = $st->execute();

            if ( $query ){
                self::$logger->notice("Weapon Category with id $this->wpcategory_id has been deleted");
                return true;
            }

        } catch (Exception $exception){
            self::$logger->error("Weapon Category with id $this->wpcategory_id cannot be deleted", array('exception' => $exception ) );
            return false;
        }

        return false;
    }


}

