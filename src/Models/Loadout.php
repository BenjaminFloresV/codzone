<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;

class Loadout
{
    private int $loadout_id;
    private int $game_id;
    private int $weapon_id;
    private int $wpcategory_id;
    private string $title;
    private string $description;
    private string $attachments;
    private string $perks;
    private string $creation_date;
    private string $date_update;
    private string $image;
    private bool $isWarzone;
    private static $conn;
    private static $log;

    public function getGameId(){
        return $this->game_id;
    }

    public function getName(){
        return $this->title;
    }

    public function getId(){
        return $this->loadout_id;
    }

    public function setId( int $id ){
        $this->loadout_id = $id;
    }

    public function setImage( string $img){
        $this->image = $img;
    }

    public function __construct()
    {
        self::$conn = Connection::dbConnection();
        self::$log = NewLogger::newLogger('LOADOUT_CLASS', 'FirePHPHandler');

        self::$log->info('Class has been instancied');

    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues( array $data)
    {
        if ( isset($data['loadout_id'] ) ) $this->loadout_id = (int) $data['loadout_id'];
        if ( isset($data['game_id'] ) ) $this->game_id = (int) $data['game_id'];
        if ( isset($data['weapon_id'] ) ) $this->weapon_id = (int) $data['weapon_id'];
        if ( isset($data['wpcategory_id'] ) ) $this->wpcategory_id = (int) $data['wpcategory_id'];
        if ( isset($data['title'] ) ) $this->title = (string) $data['title'];
        if ( isset($data['description'] ) ) $this->description = (string) $data['description'];
        if ( isset($data['attachments'] ) ) $this->attachments = (string) $data['attachments'];
        if ( isset($data['perks'] ) ) $this->perks = (string) $data['perks'];

        // Parse and store the foundation date
        if ( isset($data['creation_date']) ) {
            self::$log->info('Trying to insert creation_date');
            $creationDate = explode ( '/', $data['creation_date'] );

            if ( count($creationDate) == 3 ) {
                list ( $d, $m, $y ) = $creationDate;
                $this->creation_date = mktime ( 0, 0, 0, $m, $d, $y );
                $this->date_update = mktime(0, 0, 0, $m, $d, $y);
            }
        }

        if ( isset($data['date_update'] ) ){
            $dateUpdate = explode('/', $data['date_update']);

            if ( count($dateUpdate) === 3 ){
                list (  $d, $m, $y ) = $dateUpdate;
                $this->date_update = mktime(0,0,0, $m, $d, $y);
            }
        }else{
            $creationDate = explode ( '/', $data['creation_date'] );

            if ( count($creationDate) == 3 ) {
                list (  $d, $m, $y ) = $creationDate;
                $this->date_update = mktime ( 0, 0, 0, $m, $d, $y );
            }
        }
        if ( isset($data['image'] ) ) $this->image = (string) $data['image'];
        if ( isset($data['isWarzone'] ) ) {
            $data['isWarzone'] == "true" ? $this->isWarzone = true : $this->isWarzone = false;
        }
        self::$log->info('Data stored successfully');

    }

    public static function getAll($join = false, int $limit = null, bool $orderByDate = false, int $byGame = null, int $except = null )
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info('Trying to collect Loadouts data...');
            if ( $join ){
                $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name AS gameName, g.short_name as shortName, w.name AS weaponName, wcat.name AS weaponcatName, w.image as weaponImage FROM loadout l ";
                $sql .= "INNER JOIN game g ON g.game_id = l.game_id ";
                $sql .= "INNER JOIN weapon w ON w.weapon_id = l.weapon_id ";
                $sql .= "INNER JOIN weapon_category wcat ON wcat.wpcategory_id = l.wpcategory_id";

                if( $orderByDate ){
                    $sql .= " ORDER BY l.loadout_id DESC";
                }

                if( !is_null($byGame) ){
                    $sql .= " WHERE g.game_id = :gameId";
                }

                if( !is_null($except) ){
                    $sql .= " AND l.loadout_id <> :except";
                }

                if( $limit !== null ){
                    $sql .= " LIMIT :limit";
                }


            }else{
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate, UNIX_TIMESTAMP(date_update) AS dateUpdate FROM loadout";
            }

            $st = self::$conn->prepare($sql);
            if( !is_null($byGame) ) $st->bindValue(':gameId', $byGame, PDO::PARAM_INT);
            if( $limit !== null ) $st->bindValue(':limit', $limit, PDO::PARAM_INT);
            if( !is_null($except) ) $st->bindValue(':except', $except, PDO::PARAM_INT);
            $query = $st->execute();

            if ( $query ){
                $loadouts = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $loadouts ));
                return  $loadouts;
            }

            self::$log->info("Loadouts data could not be collected");
            return false;


        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));
            return false;
        }
    }

    public static function getById( int $id, bool $join = false)
    {
        if ( !self::$conn ) return false;
        try{

            self::$log->info("Trying to collect loadout data with id: $id");
            if ( $join ){
                $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name AS gameName, wp.name AS weaponName FROM loadout l ";
                $sql .= "INNER JOIN game g ON l.game_id = g.game_id ";
                $sql .= "INNER JOIN weapon wp ON l.weapon_id=wp.weapon_id ";
                $sql .= "WHERE loadout_id = :loadout_id";
            }else{
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate, UNIX_TIMESTAMP(date_update) AS dateUpdate FROM loadout WHERE loadout_id = :loadout_id";
            }

            $st = self::$conn->prepare($sql);
            $st->bindValue(':loadout_id', $id, PDO::PARAM_INT);
            $query = $st->execute();

            if ( !$query ){
                self::$log->info("Loadout with id: $id do not exists");
                return false;
            }
            $loadout = $st->fetch();
            self::$log->info("Loadout data has been collected successfuly.", array('data' => $loadout));
            return $loadout;

        }catch (Exception $exception){
            self::$log->error("Loadout data could not be collected", array('exception' => $exception));
            return false;
        }

    }


    public static function getByGames( array $games = null)
    {
        if( !self::$conn ) return false; // Verify database connection
        try {
            $loadouts = array();
            foreach ( $games as $game) {
                $sql = "SELECT l.title,l.loadout_id, UNIX_TIMESTAMP(l.creation_date) AS creationDate, g.short_name AS shortName, g.name AS gameName, w.image AS weaponImage FROM loadout l ";
                $sql .= "INNER JOIN game g ON g.game_id=l.game_id ";
                $sql .= "INNER JOIN weapon w ON w.weapon_id=l.weapon_id ";
                $sql .= "WHERE g.game_id=:game ORDER BY l.loadout_id DESC LIMIT 1";

                $st = self::$conn->prepare($sql);
                $st->bindValue(':game', $game, PDO::PARAM_INT);
                $query = $st->execute();

                if ( $query ){
                    $loadout = $st->fetch();
                    self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $loadouts ));
                    array_push($loadouts,$loadout);
                }else {
                    array_push($loadouts, array());
                }
                $st->closeCursor();
            }

            if( empty($loadouts) ) $loadouts = false;
            self::$log->info("Loadouts data could not be collected");


        } catch ( Exception $exception){
            self::$log->error('Random News data cannot be collected', array( 'exception' => $exception ));
        }
        return $loadouts;

    }

    public static function getAllByGame( int $gameId )
    {

        if ( !self::$conn ) return false;
        try{
            self::$log->info('Trying to collect Loadouts data...');
            $sql = "SELECT l.loadout_id,l.title, wcat.wpcategory_id, g.name AS gameName, g.short_name AS  shortName,w.image AS weaponImage, w.name AS weaponName, wcat.name AS weaponcatName FROM loadout l ";
            $sql .= "INNER JOIN game g ON g.game_id = l.game_id ";
            $sql .= "INNER JOIN weapon w ON w.weapon_id = l.weapon_id ";
            $sql .= "INNER JOIN weapon_category wcat ON wcat.wpcategory_id = l.wpcategory_id WHERE l.game_id=:game_id";



            $st = self::$conn->prepare($sql);
            $st->bindValue(':game_id', $gameId, PDO::PARAM_INT);
            $query = $st->execute();

            if ( $query ){
                $loadouts = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $loadouts ));
                return  $loadouts;
            }

            self::$log->info("Loadouts data could not be collected");
            return false;


        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));
            return false;
        }


    }

    public static function getByNameCategory( int $gameId, int $wpcategoryId)
    {
        if( !self::$conn ) return false;
        try {
            self::$log->info( 'Trying to collect Loadouts Data.' );
            $sql = "SELECT l.loadout_id,wp.name AS weaponName, wp.image AS weaponImage, l.wpcategory_id, g.name AS gameName  FROM loadout l ";
            $sql .= "INNER JOIN game g ON g.game_id=:game_id ";
            $sql .= "INNER JOIN weapon wp ON l.weapon_id=wp.weapon_id WHERE l.game_id = :game_id AND l.wpcategory_id = :wpcategory_id";

            $st = self::$conn->prepare($sql);
            $st->bindValue(':game_id', $gameId, PDO::PARAM_INT);
            $st->bindValue( ':wpcategory_id', $wpcategoryId, PDO::PARAM_INT );
            $query = $st->execute();


            if( !$query ) {
                self::$log->info('Cannot collect the loadout data.');
                return $query;
            }

            $loadouts = $st->fetchAll();
            self::$log->info( 'Loadout data collected successfully' );
            return $loadouts;


        } catch ( Exception $exception ) {
            self::$log->error( 'Something went wrong, cannot collect data.', array( 'exception' => $exception ) );
            return false;
        }

    }

    public function insert(): bool
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info("Trying to insert Loadout data...");
            $sql = "INSERT INTO loadout VALUES( NULL, :game_id, :weapon_id, :wpcategory_id, :title, :description, :attachments, :perks, FROM_UNIXTIME(:creation_date), FROM_UNIXTIME(:date_update), :image, :is_warzone )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':game_id', $this->game_id, PDO::PARAM_INT);
            $st->bindValue(':weapon_id', $this->weapon_id, PDO::PARAM_INT);
            $st->bindValue(':wpcategory_id', $this->wpcategory_id, PDO::PARAM_INT);
            $st->bindValue(':title', $this->title, PDO::PARAM_STR);
            $st->bindValue(':description', $this->description, PDO::PARAM_STR);
            $st->bindValue(':attachments', $this->attachments, PDO::PARAM_STR);
            $st->bindValue(':perks', $this->perks, PDO::PARAM_STR);
            $st->bindValue(':creation_date', $this->creation_date, PDO::PARAM_INT);
            $st->bindValue(':date_update', $this->date_update, PDO::PARAM_INT);
            $st->bindValue(':image', $this->image, PDO::PARAM_STR);
            $st->bindValue(':is_warzone', $this->isWarzone, PDO::PARAM_BOOL);
            $query = $st->execute();

            if ( $query ){
                self::$log->info("Loadout data has been inserted successfully.");
                return true;
            }

            return false;


        }catch (Exception $exception){
            self::$log->error("Loadout could not be inserted", array( 'exception' => $exception ));
            return false;
        }

    }

    public function update(): bool
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info("Trying to update Loadout data...");
            $sql = "UPDATE loadout SET game_id = :game_id, weapon_id = :weapon_id, wpcategory_id = :wpcategory_id, title = :title, description = :description, attachments = :attachments, perks = :perks, date_update = FROM_UNIXTIME(:date_update), image = :image WHERE loadout_id = :loadout_id";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':loadout_id', $this->loadout_id, PDO::PARAM_INT);
            $st->bindValue(':game_id', $this->game_id, PDO::PARAM_INT);
            $st->bindValue(':weapon_id', $this->weapon_id, PDO::PARAM_INT);
            $st->bindValue(':wpcategory_id', $this->wpcategory_id, PDO::PARAM_INT);
            $st->bindValue(':title', $this->title, PDO::PARAM_STR);
            $st->bindValue(':description', $this->description, PDO::PARAM_STR);
            $st->bindValue(':attachments', $this->attachments, PDO::PARAM_STR);
            $st->bindValue(':perks', $this->perks, PDO::PARAM_STR);
            $st->bindValue(':date_update', $this->date_update, PDO::PARAM_INT);
            $st->bindValue(':image', $this->image, PDO::PARAM_STR);
            $query = $st->execute();

            if ( $query ){
                self::$log->info("Loadout data has been updated successfully.");
                return true;
            }

            return false;


        }catch (Exception $exception){
            self::$log->error("Loadout could not be updated", array( 'exception' => $exception ));
            return false;
        }

    }

    public function delete(): bool
    {
        if ( !self::$conn ) return false;
        try{
            self::$log->info("Trying to delete the Loadout with id: $this->loadout_id");
            $sql = "DELETE FROM loadout WHERE loadout_id = :loadout_id";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':loadout_id', $this->loadout_id, PDO::PARAM_INT);
            $query = $st->execute();

            if ( !$query ){
                self::$log->info("The Loadout with id: $this->loadout_id do not exists");
                return false;
            }

            self::$log->info("The Loadout with id: $this->loadout_id has been deleted successfully");
            return true;

        } catch (Exception $exception){
            self::$log->info('Loadout may not updated', array( 'exception' => $exception ));
            return false;
        }

    }

    public static function getLastByGame( int $id, bool $join = false)
    {
        if ( !self::$conn ) return false;
        try{

            self::$log->info("Trying to collect loadout data with id: $id");
            if ( $join ){
                $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name as gameName FROM loadout l INNER JOIN game g ON l.game_id = g.game_id WHERE l.game_id = :game_id";
            }else{
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate, UNIX_TIMESTAMP(date_update) AS dateUpdate FROM loadout WHERE game_id=:game_id LIMIT 1";
            }

            $st = self::$conn->prepare($sql);
            $st->bindValue(':game_id', $id, PDO::PARAM_INT);
            $query = $st->execute();

            if ( !$query ){
                self::$log->info("Loadout with id: $id do not exists");
                return false;
            }
            $loadout = $st->fetch();
            self::$log->info("Loadout data has been collected successfuly.", array('data' => $loadout));
            return $loadout;

        }catch (Exception $exception){
            self::$log->error("Loadout data could not be collected", array('exception' => $exception));
            return false;
        }

    }


    public function getAllFiltered( array $data)
    {
        if ( !self::$conn ) return false;
        try{
            $result = false;
            $gameId = $data['game'];
            $weaponCatId = $data['weaponcat'];

            self::$log->info('Trying to collect Loadouts data...');

            $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name AS gameName, g.short_name as shortName, w.name AS weaponName, wcat.name AS weaponcatName, w.image as weaponImage FROM loadout l ";
            $sql .= "INNER JOIN game g ON g.game_id = l.game_id ";
            $sql .= "INNER JOIN weapon w ON w.weapon_id = l.weapon_id ";
            $sql .= "INNER JOIN weapon_category wcat ON wcat.wpcategory_id = l.wpcategory_id";

            $conditions = array();
            // real escape
            if( !empty($gameId)) {
                $gameId = self::$conn->quote($gameId);
                $conditions[] = "g.game_id={$gameId}";
            }
            if( !empty($weaponCatId)) {
                $weaponCatId = self::$conn->quote($weaponCatId);
                $conditions[] = "wcat.wpcategory_id={$weaponCatId}";
            }

            if( count($conditions) > 0 ) $sql.= " WHERE ".implode(' AND ', $conditions);

            $st = self::$conn->prepare($sql);
            $query = $st->execute();

            if ( $query ){
                $loadouts = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $loadouts ));
                $result = $loadouts;
            }

            self::$log->info("Loadouts data could not be collected");

        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));

        }

        return $result;
    }
}