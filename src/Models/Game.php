<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;

class Game
{
    private int $game_id;
    private int $company_id;
    private string $name;
    private string $short_name;
    private string $release_date;
    private string $description;
    private string $image;
    private static $conn;
    private static $log;

    public function setImage( string $image){
        $this->image = $image;
    }

    public function getName(){
        return $this->name;
    }

    public function setId($id){
        $this->game_id = $id;
    }

    public function __construct()
    {
        self::$log = NewLogger::newLogger('GAME_CLASS','FirePHPHandler');
        self::$conn = Connection::dbConnection();

        self::$log->info('Class created successfully');

    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues( array $data ): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to store form values...');
            if ( isset($data['game_id'] ) ) $this->game_id = (int) $data['game_id'];
            if ( isset($data['company_id'] ) ) $this->company_id = (int) $data['company_id'];
            if ( isset($data['name'] ) ) $this->name = (string) preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['name'] );
            if ( isset($data['shortName'] ) ) $this->short_name = (string) preg_replace ( "/[^\.\,\-\_\'\"\@\?\!\:\$ a-zA-Z0-9()]/", "", $data['shortName'] );
            if ( isset($data['releaseDate']) ) {
                $releaseDate = explode ( '/', $data['releaseDate'] );

                if ( count($releaseDate) == 3 ) {
                    list ( $d, $m, $y ) = $releaseDate;
                    $this->release_date = mktime ( 0, 0, 0, $m, $d, $y );
                }
            }

            if ( isset($data['description'] ) ) $this->description = $data['description'];
            if ( isset($data['image'] ) ) $this->image = (string) $data['image'];


            self::$log->info('Form values have been stored successfully', array('formData' => array($this->name, $this->description, $this->release_date, $this->company_id)));
            return true;


        }catch (Exception $exception){
            self::$log->error('Cannot store form values');
            return false;
        }
    }

    public static function getAll( bool $join = false): bool|array
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to retrieve Games all data...');
            $sql = "SELECT *, UNIX_TIMESTAMP(release_date) AS releaseDate FROM game";
            if( $join ){
                $sql = "SELECT g.*, UNIX_TIMESTAMP(g.release_date) AS releaseData, c.name AS companyName FROM game g INNER JOIN company c ON g.company_id = c.company_id";
            }

            $st = self::$conn->prepare( $sql );
            $query = $st->execute();

            if ($query) {
                $games = $st->fetchAll();
                self::$log->notice('All Games data collected successfully', array( 'games_list' => $games ) );
                return $games;
            }

        } catch (Exception $exception){
            self::$log->error('All Games data cannot be collected', array( 'exception' => $exception ) );
            return false;
        }
        return false;
    }

    public static function getById( $id, bool $join = false ): bool|array
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to retrieve Loadout data by id...');
            $sql = "SELECT *, UNIX_TIMESTAMP(release_date) AS releaseDate FROM game WHERE game_id = :id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":id", $id, PDO::PARAM_INT );
            $query = $st->execute();


            if($query){
                $game = $st->fetch();

                if ( !$game ){
                    self::$log->notice("The Loadout with id: $id do not exists." );
                    return false;
                }

                self::$log->notice('The Loadout data was collected successfully', array( 'game' => $game ) );
                return $game;
            }

        } catch (Exception $exception) {
            self::$log->error('The Loadout cannot be collected', array( 'exception' => $exception ));
            return false;
        }

        return false;

    }

    public static function getByShortName( string $shortName )
    {
        self::$log->info('Trying to collect Loadout data by shortName');
        if( !self::$conn ) return false;
        try {
            self::$log->info('Trying to collect Loadout data by shortName');
            $sql = "SELECT * FROM game WHERE short_name=:short_name";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(':short_name', $shortName, PDO::PARAM_STR);
            $query = $st->execute();

            if($query){
                $game = $st->fetch();

                if ( !$game ){
                    self::$log->notice("The Loadout with short name: $shortName do not exists." );
                    return false;
                }

                self::$log->notice('The Loadout data was collected successfully', array( 'game' => $game ) );
                return $game;
            }



        }catch ( Exception $exception ) {
            self::$log->error('Something went wrong while collecting data.', array( 'execption' => $exception ) );

        }

        return false;
    }

    public function insert(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->info('Trying to save data...');
            $sql = "INSERT INTO game ( game_id, company_id, name, short_name, release_date, description, image ) VALUES(NULL, :company_id, :game_name, :short_name , FROM_UNIXTIME(:releaseDate), :description, :image )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(":company_id", $this->company_id, PDO::PARAM_INT );
            $st->bindValue(":game_name", $this->name, PDO::PARAM_STR);
            $st->bindValue(":short_name", $this->short_name, PDO::PARAM_STR);
            $st->bindValue(":releaseDate", $this->release_date, PDO::PARAM_INT);
            $st->bindValue(":description", $this->description, PDO::PARAM_STR);
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);
            $query = $st->execute();

            if ( $query){
                self::$log->info("A Loadout has been created successfully: $this->name");
                return true;
            }

            return true;

        } catch (Exception $exception){
            self::$log->error('Loadout cannot be created', array( 'exception' => $exception ) );
            return false;
        }
    }

    public function update(): bool {

        try{
            if( !self::$conn ) return false; // Verify database connection
            self::$log->info('Trying to update game data...');
            $sql = "UPDATE game SET  company_id = :company_id, name = :name, release_date = FROM_UNIXTIME(:releaseDate), description = :description, image = :image WHERE game_id = :game_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':game_id', $this->game_id, PDO::PARAM_INT );
            $st->bindValue(':company_id', $this->company_id, PDO::PARAM_INT);
            $st->bindValue(":name", $this->name, PDO::PARAM_STR);
            $st->bindValue(":releaseDate", $this->release_date, PDO::PARAM_INT);
            $st->bindValue(":description", $this->description, PDO::PARAM_STR);
            $st->bindValue(":image", $this->image, PDO::PARAM_STR);

            $query = $st->execute();

            if ( $query ){
                self::$log->info("Loadout data with id {$this->game_id} has been updated successfully");
                return true;
            }

        } catch (Exception $exception){
            self::$log->error('Loadout cannot be updated', array( 'exception' => $exception ) );
            return false;
        }

        return false;
    }

    public function delete(): bool
    {
        if( !self::$conn ) return false; // Verify database connection
        try{
            self::$log->notice("Trying to delete the game...");
            $sql = "DELETE FROM game WHERE game_id = :game_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue( ':game_id', $this->game_id, PDO::PARAM_INT );
            $query = $st->execute();

            if ( $query ){
                self::$log->notice("Loadout with id $this->game_id has been deleted");
                return true;
            }

        } catch (Exception $exception){
            self::$log->error("Loadout with id $this->game_id cannot be deleted", array('exception' => $exception ) );
            return false;
        }

        return false;
    }

}