<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use \Psr\Log\LoggerInterface;
use CMS\Models\Singleton\Singleton;

class Loadout extends Singleton
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
    private static bool|PDO $conn;
    private static  LoggerInterface $log;


    public function getGameId(): int
    {
        return $this->game_id;
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getId(): int
    {
        return $this->loadout_id;
    }

    public function setId( int $id ){
        $this->loadout_id = $id;
    }

    public function setImage( string $img){
        $this->image = $img;
    }

    protected function __construct()
    {
        self::$conn = Connection::dbConnection();
        self::$log = NewLogger::newLogger('LOADOUT_CLASS', 'FirePHPHandler');

        self::$log->info('Class has been instancied');


    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues( array $data): bool
    {
        $result = true;
        try {
            if (isset($data['loadout_id'])) $this->loadout_id = (int)$data['loadout_id'];
            if (isset($data['game_id'])) $this->game_id = (int)$data['game_id'];
            if (isset($data['weapon_id'])) $this->weapon_id = (int)$data['weapon_id'];
            if (isset($data['wpcategory_id'])) $this->wpcategory_id = (int)$data['wpcategory_id'];
            if (isset($data['title'])) $this->title = (string)$data['title'];
            if (isset($data['description'])) $this->description = (string)$data['description'];
            if (isset($data['attachments'])) $this->attachments = (string)$data['attachments'];
            if (isset($data['perks'])) $this->perks = (string)$data['perks'];

            // Parse and store the foundation date
            if (isset($data['creation_date'])) {
                self::$log->info('Trying to insert creation_date');
                $creationDate = explode('/', $data['creation_date']);

                if (count($creationDate) == 3) {
                    list ($d, $m, $y) = $creationDate;
                    $this->creation_date = mktime(0, 0, 0, $m, $d, $y);
                    $this->date_update = mktime(0, 0, 0, $m, $d, $y);
                }
            }

            if (isset($data['date_update'])) {
                $dateUpdate = explode('/', $data['date_update']);

                if (count($dateUpdate) === 3) {
                    list ($d, $m, $y) = $dateUpdate;
                    $this->date_update = mktime(0, 0, 0, $m, $d, $y);
                }
            } else {
                $creationDate = explode('/', $data['creation_date']);

                if (count($creationDate) == 3) {
                    list ($d, $m, $y) = $creationDate;
                    $this->date_update = mktime(0, 0, 0, $m, $d, $y);
                }
            }
            if (isset($data['image'])) $this->image = (string)$data['image'];
            if (isset($data['isWarzone'])) {
                $data['isWarzone'] == "true" ? $this->isWarzone = true : $this->isWarzone = false;
            }
            self::$log->info('Data stored successfully');

        } catch (Exception $exception){
            self::$log->error('Data could not be stored', array('exception' => $exception));
            $result = false;
        }

        return $result;


    }

    public static function getAll($join = false, int $limit = null, bool $orderById = false, int $byGame = null, int $except = null ): bool|array
    {
        $result = false;

        if ( !self::$conn ) return $result;
        try{
            self::$log->info('Trying to collect Loadouts data...');
            if ( $join ){
                $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name AS gameName, g.short_name as shortName, w.name AS weaponName, wcat.name AS weaponcatName, w.image as weaponImage FROM loadout l ";
                $sql .= "INNER JOIN game g ON g.game_id = l.game_id ";
                $sql .= "INNER JOIN weapon w ON w.weapon_id = l.weapon_id ";
                $sql .= "INNER JOIN weapon_category wcat ON wcat.wpcategory_id = l.wpcategory_id";

                if( $orderById ) $sql .= " ORDER BY l.loadout_id DESC";
                if( !is_null($byGame) ) $sql .= " WHERE g.game_id = :gameId";
                if( !is_null($except) ) $sql .= " AND l.loadout_id <> :except";
                if( $limit !== null ) $sql .= " LIMIT :limit";


            }else{
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate, UNIX_TIMESTAMP(date_update) AS dateUpdate FROM loadout";
            }

            $st = self::$conn->prepare($sql);
            if( !is_null($byGame) ) $st->bindValue(':gameId', $byGame, PDO::PARAM_INT);
            if( $limit !== null ) $st->bindValue(':limit', $limit, PDO::PARAM_INT);
            if( !is_null($except) ) $st->bindValue(':except', $except, PDO::PARAM_INT);
            $query = $st->execute();

            if ( $query ){
                $result = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $result ));
            }

        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));
        }
        return $result;
    }

    public static function getById( int $id, bool $join = false)
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{

            self::$log->info("Trying to collect loadout data with id: $id");
            if ( $join ){
                $sql = "SELECT l.*, UNIX_TIMESTAMP(l.creation_date) AS creationDate, UNIX_TIMESTAMP(l.date_update) AS dateUpdate, g.name AS gameName, g.short_name AS shortName,  wp.name AS weaponName FROM loadout l ";
                $sql .= "INNER JOIN game g ON l.game_id = g.game_id ";
                $sql .= "INNER JOIN weapon wp ON l.weapon_id=wp.weapon_id ";
                $sql .= "WHERE loadout_id = :loadout_id";
            }else{
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate, UNIX_TIMESTAMP(date_update) AS dateUpdate FROM loadout WHERE loadout_id = :loadout_id";
            }

            $st = self::$conn->prepare($sql);
            $st->bindValue(':loadout_id', $id, PDO::PARAM_INT);
            $query = $st->execute();

            if ( $query ){
                $result = $st->fetch();
                self::$log->info("Loadout data has been collected successfuly.", array('data' => $result));
            }

        }catch (Exception $exception){
            self::$log->error("Loadout data could not be collected", array('exception' => $exception));

        }
        return $result;
    }


    public static function getByGames( array $games = null): bool|array
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try {
            $result = array();
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
                    self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $result ));
                    array_push($result,$loadout);
                }else {
                    array_push($result, array());
                }
                $st->closeCursor();
            }

            if( empty($result) ) $result = false;
            self::$log->info("Loadouts data could not be collected");


        } catch ( Exception $exception){
            self::$log->error('Random News data cannot be collected', array( 'exception' => $exception ));
        }
        return $result;
    }

    public static function getAllByGame( int $gameId ): bool|array
    {
        $result = false;
        if ( !self::$conn ) return $result;
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
                $result = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $result ));
            }

        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));
        }
        return $result;
    }

    public static function getByNameCategory( int $gameId, int $wpcategoryId): bool|array
    {
        $result = false;
        if( !self::$conn ) return $result;
        try {
            self::$log->info( 'Trying to collect Loadouts Data.' );
            $sql = "SELECT l.loadout_id,wp.name AS weaponName, wp.image AS weaponImage, l.wpcategory_id, g.name AS gameName  FROM loadout l ";
            $sql .= "INNER JOIN game g ON g.game_id=:game_id ";
            $sql .= "INNER JOIN weapon wp ON l.weapon_id=wp.weapon_id WHERE l.game_id = :game_id AND l.wpcategory_id = :wpcategory_id";

            $st = self::$conn->prepare($sql);
            $st->bindValue(':game_id', $gameId, PDO::PARAM_INT);
            $st->bindValue( ':wpcategory_id', $wpcategoryId, PDO::PARAM_INT );
            $query = $st->execute();

            if( $query ) {
                $result = $st->fetchAll();
                self::$log->info( 'Loadout data collected successfully' );
            }
        } catch ( Exception $exception ) {
            self::$log->error( 'Something went wrong, cannot collect data.', array( 'exception' => $exception ) );
        }

        return $result;
    }

    public function insert(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            self::$log->info("Trying to insert Loadout data...");
            $sql = "INSERT INTO loadout VALUES( NULL, :game_id, :weapon_id, :wpcategory_id, :title, :description, :attachments, :perks, FROM_UNIXTIME(:creation_date) + INTERVAL 1 HOUR, FROM_UNIXTIME(:date_update) + INTERVAL 1 HOUR, :image, :is_warzone )";
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
            $st->bindValue(':is_warzone', $this->isWarzone ?? false, PDO::PARAM_BOOL);
            $result = $st->execute();

            if ( $result ){
                self::$log->info("Loadout data has been inserted successfully.");
            }

        }catch (Exception $exception){
            self::$log->error("Loadout could not be inserted", array( 'exception' => $exception ));
        }
        return $result;
    }

    public function update(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            self::$log->info("Trying to update Loadout data...");
            $sql = "UPDATE loadout SET game_id = :game_id, weapon_id = :weapon_id, wpcategory_id = :wpcategory_id, title = :title, description = :description, attachments = :attachments, perks = :perks, date_update = FROM_UNIXTIME(:date_update) + INTERVAL 1 HOUR, image = :image WHERE loadout_id = :loadout_id";
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
            $result = $st->execute();

            if ( $result ){
                self::$log->info("Loadout data has been updated successfully.");;
            }

        }catch (Exception $exception){
            self::$log->error("Loadout could not be updated", array( 'exception' => $exception ));
        }

        return $result;
    }

    public function delete(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            self::$log->info("Trying to delete the Loadout with id: $this->loadout_id");
            $sql = "DELETE FROM loadout WHERE loadout_id = :loadout_id";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':loadout_id', $this->loadout_id, PDO::PARAM_INT);
            $result = $st->execute();

            if ( !$result ){
                self::$log->info("The Loadout with id: $this->loadout_id do not exists");
            }

        } catch (Exception $exception){
            self::$log->info('Loadout may not updated', array( 'exception' => $exception ));
        }
        return $result;
    }

    public static function getLastByGame( int $id, bool $join = false)
    {
        $result = false;
        if ( !self::$conn ) return $result;
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

            if ( $query ){
                $result = $st->fetch();
                self::$log->info("Loadout data has been collected successfuly.", array('data' => $result));

            }

        }catch (Exception $exception){
            self::$log->error("Loadout data could not be collected", array('exception' => $exception));
        }
        return $result;
    }


    public function getAllFiltered( array $data): bool|array
    {
        $result = false;
        if ( !self::$conn ) return $result;
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

            $sql.= " ORDER BY l.loadout_id DESC";

            $st = self::$conn->prepare($sql);
            $query = $st->execute();

            if ( $query ){
                $result = $st->fetchAll();
                self::$log->info("Loadouts data has been collected successfully.", array( 'data' => $result ));

            }


        }catch (Exception $exception){
            self::$log->error('Loadouts data could not be collected.', array( 'exception' => $exception ));
        }

        return $result;
    }
}