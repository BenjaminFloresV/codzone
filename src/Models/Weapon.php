<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;

class Weapon
{
    private int $weapon_id;
    private int $wpcategory_id;
    private int $game_id;
    private string $name;
    private string $image;
    private static $conn;
    private static $log;

    public function getId(){
        return $this->weapon_id;
    }

    public function setId( int $id ){
        $this->weapon_id = $id;
    }

    public function getName(){
        return $this->name;
    }

    public function setImage( string $image ){
        $this->image =  $image;
    }

    public function __construct()
    {
        self::$log = NewLogger::newLogger('WEAPON_CLASS', 'FirePHPHandler');
        self::$conn = Connection::dbConnection();

        self::$log->info('Class has been instancied');
    }

    public static function verifyConnection(){
        return self::$conn != null;
    }

    public function storeFormValues( array $data ): bool
    {
        try{

            self::$log->info('Trying to store form data...');

            if ( isset( $data['weapon_id'] ) ) $this->weapon_id = (int) $data['weapon_id'];
            if ( isset( $data['wpcategory_id'] ) ) $this->wpcategory_id = (int) $data['wpcategory_id'];
            if ( isset( $data['game_id'] ) ) $this->game_id = (int) $data['game_id'];
            if ( isset( $data['name'] ) ) $this->name = (string) $data['name'];
            if ( isset( $data['image'] ) ) $this->name = (string) $data['image'];

            self::$log->info('Data has been stored successfully', array('data' => $data));

            return true;

        } catch (Exception $exception){
            self::$log->error('An error has occured.', array( 'exception' => $exception ) );
            return false;
        }



    }


    public static function getAll(bool $join = false): bool|array
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info('Trying to retrieve Weapons all data...');
            $sql = "SELECT * FROM weapon";
            if( $join ){
                $sql = "SELECT w.*, g.name AS gameName, wp.name AS wpCatName FROM weapon w ";
                $sql .= "INNER JOIN game g ON g.game_id = w.game_id ";
                $sql .= "INNER JOIN weapon_category wp ON wp.wpcategory_id = w.wpcategory_id";

            }
            $st = self::$conn->prepare( $sql );
            $query = $st->execute();

            if ($query) {
                $weapons = $st->fetchAll();
                self::$log->notice('All Weapons data collected successfully', array( 'games_list' => $weapons ) );
                return $weapons;
            }

        } catch (Exception $exception){
            self::$log->error('All Weapons data cannot be collected', array( 'exception' => $exception ) );
            return false;
        }
        return false;
    }

    public static function getById( int $id, bool $join = false )
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info("Trying to retrieve Weapon data with id: $id");
            if ( $join ){
                $sql = "SELECT w.*, g.name AS gameName FROM weapon w INNER JOIN game g ON w.game_id = g.game_id WHERE w.weapon_id = :id";
            }else {
                $sql = "SELECT * FROM weapon WHERE weapon_id = :id";
            }
            $st = self::$conn->prepare($sql);
            $st->bindValue(":id", $id, PDO::PARAM_INT);
            $query = $st->execute();

            if ( $query ){
                $weapon = $st->fetch();

                if ( !$weapon  ){
                    self::$log->notice("Weapon with id: $id do not exists." );
                    return false;
                }

                self::$log->info("Weapon data with id: $id has been retrieved successfully.", array('data'=> $weapon));
                return $weapon;
            }

        } catch (Exception $exception){
            self::$log->error("Cannot collect Weapon data with id: $id", array('exception' => $exception));
            return false;
        }
    }

    public function insert(): bool
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info("Trying to insert Weapon data...");
            $sql = "INSERT INTO weapon ( weapon_id, wpcategory_id, game_id, name, image) VALUES(NULL, :wpcategory_id, :game_id, :name, :image )";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(':wpcategory_id', $this->wpcategory_id, PDO::PARAM_INT);
            $st->bindValue( ':game_id', $this->game_id, PDO::PARAM_INT );
            $st->bindValue( ':name', $this->name, PDO::PARAM_STR );
            $st->bindValue( ':image', $this->image, PDO::PARAM_STR );
            $query = $st->execute();

            if ( $query ){

                self::$log->info('Weapon has been created');
                return true;
            }

            self::$log->info('Weapon cannot be created.');
            return false;

        }catch (Exception $exception){
            self::$log->error('Weapon cannot be inserted', array( 'exception' => $exception ));
            return false;
        }

    }

    public function update(): bool
    {
        if ( !self::$conn ) return false;
        try{

            self::$log->info("Trying to update Weapon data with id: $this->weapon_id");
            $sql = "UPDATE weapon SET wpcategory_id = :wpcategory_id, game_id = :game_id, name = :weapon_name, image = :image WHERE weapon_id = :weapon_id";
            $st = self::$conn->prepare($sql);
            $st->bindValue( ':weapon_id', $this->weapon_id, PDO::PARAM_INT );
            $st->bindValue(':wpcategory_id', $this->wpcategory_id, PDO::PARAM_INT);
            $st->bindValue( ':game_id', $this->game_id, PDO::PARAM_INT );
            $st->bindValue( ':weapon_name', $this->name, PDO::PARAM_STR );
            $st->bindValue( ':image', $this->image, PDO::PARAM_STR );
            $query = $st->execute();

            if ( !$query ){
                self::$log->info("Weapon with id: $this->weapon_id do not exists");
                return false;
            }

            self::$log->info("Weapon with id: $this->weapon_id has been updated");
            return true;

        }catch (Exception $exception){
            self::$log->error('Weapon cannot be updated');
            return false;
        }

    }

    public function delete(): bool
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->notice("Trying to delete the weapon...");
            $sql = "DELETE FROM weapon WHERE weapon_id = :weapon_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':weapon_id', $this->weapon_id, PDO::PARAM_INT );
            $query = $st->execute();

            if ( $query ){
                self::$log->notice("Weapon with id $this->weapon_id has been deleted");
                return true;
            }

        } catch (Exception $exception){
            self::$log->error("Weapon with id $this->weapon_id cannot be deleted", array('exception' => $exception ) );
            return false;
        }

        return false;
    }



}