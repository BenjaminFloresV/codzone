<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class News extends Category
{
    private int $news_id;
    private string $images_id;
    private string $title;
    private string $description;
    private string $creation_date;
    private string $image_title;
    private string $image_desc;
    private ?string $image_footer;
    private ?string $image_extra;
    private static LoggerInterface $log;
    private static bool|PDO $conn;

    public function setNews_id( int $id)
    {
        $this->news_id = $id;
    }

    public function setCategoryName( string $name )
    {
        $this->category_name = trim( $name );
    }

    public function setNewsCategory( int $id){
        $this->category_id = $id;
    }

    public function getCategoryId(): int
    {
        return $this->category_id;
    }

    public function setImages_id( int $images_id ){
        $this->images_id = $images_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setImgTitle( string $image ){
        $this->image_title = $image;
    }

    public function getImgTitle(): string
    {
        return $this->image_title;
    }

    public function setImgDesc( string $image ){
        $this->image_desc = $image;
    }
    public function getImgDesc(): string
    {
        return $this->image_desc;
    }

    public function setImgFooter( string $image ){
        $this->image_footer = $image;
    }

    public function getImgFooter(): ?string
    {
        return $this->image_footer;
    }

    public function setImgExtra( string $image ){
        $this->image_extra = $image;
    }

    public function getImgExtra(): ?string
    {
        return $this->image_extra;
    }


    public function __construct()
    {
        parent::__construct();
        $this->image_extra = null;
        $this->image_footer = null;

        self::$log = NewLogger::newLogger('NEWS_CLASS', 'FirePHPHandler');
        self::$conn = Connection::dbConnection();

        self::$log->info('Class has been instancied');

    }

    public static function verifyConnection(): bool
    {
        return self::$conn != null;
    }

    public function storeFormValues( array $data): bool
    {
        self::$log->info('trying to store data.');
        $result = false;
        try {

            if (isset($data['news_id'])) $this->news_id = (int)$data['news_id'];
            if (isset($data['category_id'])) $this->category_id = (int)$data['category_id'];
            if (isset($data['images_id'])) $this->images_id = (int)$data['images_id'];
            if (isset($data['title'])) $this->title = (string)$data['title'];
            if (isset($data['description'])) $this->description = (string)$data['description'];

            if ( isset($data['image_title'])) $this->image_title = (string)$data['image_title'];
            if ( isset($data['image_desc'])) $this->image_desc = (string)$data['image_desc'];
            if ( isset($data['image_footer'])) $this->image_footer = (string)$data['image_footer'];
            if ( isset($data['image_extra'])) $this->image_extra = (string)$data['image_extra'];

            if ( isset($data['category_name'])) $this->category_name = (string)$data['category_name'];

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

            self::$log->info("Data was stored successfuly");


        } catch ( Exception $exception){
            self::$log->error('Something went wrong while storing data.', array('exception'=>$exception));
            $result = false;
        }

        return $result;

    }

    public function getAllFiltered( array $data ): bool|array
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try {
            self::$log->info('Trying to collect filtered News Data');
            $categoryId = $data['category'];

            $sql = "SELECT n.*, UNIX_TIMESTAMP(n.creation_date) AS creationDate, ni.image_title AS titleImage, ncat.name AS categoryName FROM news n ";
            $sql .="INNER JOIN news_category ncat ON ncat.category_id=n.category_id ";
            $sql .= "INNER JOIN news_images ni ON ni.images_id = n.images_id";

            $conditions = array();

            if( !empty($categoryId) ){
                $categoryId = self::$conn->quote($categoryId);
                $conditions[] = "ncat.category_id={$categoryId}";
            }

            if ( count($conditions) > 0 ) $sql .= " WHERE ".implode(' AND ', $conditions);

            $sql .= " ORDER BY n.news_id DESC";

            $st = self::$conn->prepare($sql);
            $query = $st->execute();

            if ( $query ){
                $result = $st->fetchAll();
                self::$log->info('Filtered news data was collected successfully');
            }

        } catch (Exception $exception){
            self::$log->error('Something went wrong while collecting filtered news data', array('exception' => $exception));
        }

        return $result;

    }

    public static function getAll($join = false, $limit = null, bool $byCatName = false, string $categoryName = null, bool $lastNew = false, int $except =  null): bool|array
    {
        $result = false;
        if (!self::$conn) return $result;
        try {
            self::$log->info('Trying to collect News data...');
            if ($join) {
                $sql = "SELECT n.*, UNIX_TIMESTAMP(n.creation_date) AS creationDate, ni.image_title AS titleImage, ncat.name AS categoryName FROM news n ";
                $sql .="INNER JOIN news_category ncat ON ncat.category_id=n.category_id ";
                $sql .= "INNER JOIN news_images ni ON ni.images_id = n.images_id";

                if( $byCatName ){
                    $sql .=" WHERE ncat.name=:categoryName";
                }

                if( !is_null($except) ){
                    $sql .=" AND news_id <> :except";
                }

            } else {
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate FROM news";
            }

            if( $lastNew ){
                $sql .= " ORDER BY n.news_id DESC";
            }

            if( !is_null($limit) ){
                $sql .= " LIMIT :limit";
            }

            $st = self::$conn->prepare($sql);
            if(!is_null($limit)) $st->bindValue(':limit', $limit, PDO::PARAM_INT);
            if( $byCatName ) $st->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            if( !is_null($except) ) $st->bindValue(':except', $except, PDO::PARAM_INT);

            $query = $st->execute();

            if ($query) {
                $result = $st->fetchAll();
                self::$log->info("News data has been collected successfully.", array('data' => $result));
            }

            self::$log->info("News data could not be collected");

        } catch (Exception $exception) {
            self::$log->error('News data could not be collected.', array('exception' => $exception));
        }

        return $result;
    }


    public static function getById( $id, bool $join = false )
    {
        $result = false;
        if( !self::$conn ) return $result; // Verify database connection
        try{
            self::$log->info('Trying to retrieve News data...');
            if ( $join ){
                $sql = "SELECT n.*, UNIX_TIMESTAMP(n.creation_date) AS creationDate, ncats.name AS categoryName, nimages.image_title, nimages.image_desc, nimages.image_footer, nimages.image_extra FROM news n ";
                $sql .= "INNER JOIN news_category ncats ON n.category_id=ncats.category_id ";
                $sql .= "INNER JOIN news_images nimages ON n.images_id=nimages.images_id ";
                $sql .= "WHERE n.news_id = :news_id";
            }else {
                $sql = "SELECT *, UNIX_TIMESTAMP(creation_date) AS creationDate FROM news WHERE news_id = :news_id";
            }

            $st = self::$conn->prepare( $sql );
            $st->bindValue(":news_id", $id, PDO::PARAM_INT );
            $query = $st->execute();

            if($query){
                $result = $st->fetch();
                self::$log->info($result);

                if ( !$result  ){
                    self::$log->notice("The News with id: $id do not exists." );
                }
                self::$log->notice('One News data was collected successfully', array( 'news' => $result ) );
            }

        } catch (Exception $exception) {
            self::$log->error('One News data cannot be collected', array( 'exception' => $exception ));
        }
        return $result;
    }



    public static function getAllImages(int $images_id)
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try {
            self::$log->info('Trying to retrieve images data.');
            $sql = "SELECT * FROM news_images WHERE images_id=:images_id";
            $st = self::$conn->prepare($sql);

            $st->bindValue(':images_id', $images_id, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ){

                self::$log->info('Image News was collected successfully');
                $result = $st->fetch();

            }

        } catch ( Exception $exception ){
            self::$log->error("Images data could not be collected", array( 'exception' => $exception ));
        }
        return $result;
    }

    public function insert(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            self::$log->info("Trying to insert News data...");

            $firstSql = "INSERT INTO news_images VALUES( NULL, :images_title, :images_desc, :images_footer, :images_extra )";
            $secondSql = "INSERT INTO news VALUES(NULL, :category_id, :images_id, :title, :description, FROM_UNIXTIME(:creation_date))";

            $db = self::$conn;
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,0);
            $firstSt = $db->prepare($firstSql);
            $secondSt = $db->prepare($secondSql);

            $firstSt->bindValue( ':images_title', $this->image_title,  PDO::PARAM_STR);
            $firstSt->bindValue( ':images_desc', $this->image_desc,  PDO::PARAM_STR);
            $firstSt->bindValue( ':images_footer', $this->image_footer,  PDO::PARAM_STR);
            $firstSt->bindValue( ':images_extra', $this->image_extra,  PDO::PARAM_STR);

            $firstSt->execute();

            $imagesId = $db->lastInsertId();

            $firstSt->closeCursor();

            $secondSt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
            $secondSt->bindValue(':images_id', $imagesId, PDO::PARAM_INT);
            $secondSt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $secondSt->bindValue(':description', $this->description, PDO::PARAM_STR);
            $secondSt->bindValue(':creation_date', $this->creation_date, PDO::PARAM_INT);
            // Second St
            $result = $secondSt->execute();

            self::$log->info("Trying to insert News data...2");

            if ( $result ){
                self::$log->info("News data has been inserted successfully.");
            }

        }catch (Exception $exception){
            self::$log->error("News could not be inserted", array( 'exception' => $exception ));
        }
        return $result;
    }


    public function update(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            self::$log->info("Trying to insert News data...");

            $firstSql = "UPDATE news_images SET image_title=:image_title, image_desc=:image_desc, image_footer=:image_footer, image_extra=:image_extra WHERE images_id=:images_id";
            $secondSql = "UPDATE news SET category_id=:category_id, title=:title, description=:description WHERE news_id=:news_id";

            $db = self::$conn;
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,0);
            $firstSt = $db->prepare($firstSql);
            $secondSt = $db->prepare($secondSql);

            $firstSt->bindValue( ':images_id', $this->images_id,  PDO::PARAM_INT);
            $firstSt->bindValue( ':image_title', $this->image_title,  PDO::PARAM_STR);
            $firstSt->bindValue( ':image_desc', $this->image_desc,  PDO::PARAM_STR);
            $firstSt->bindValue( ':image_footer', $this->image_footer,  PDO::PARAM_STR);
            $firstSt->bindValue( ':image_extra', $this->image_extra,  PDO::PARAM_STR);

            $firstSt->execute();

            $firstSt->closeCursor();

            $secondSt->bindValue(':news_id', $this->news_id, PDO::PARAM_INT);
            $secondSt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
            $secondSt->bindValue(':title', $this->title, PDO::PARAM_STR);
            $secondSt->bindValue(':description', $this->description, PDO::PARAM_STR);


            $result = $secondSt->execute();


            if ( $result ){
                self::$log->info("News data has been updated successfully.");
            }


        }catch (Exception $exception){
            self::$log->error("News could not be inserted", array( 'exception' => $exception ));
        }

        return $result;
    }



    public function delete(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try {
            self::$log->info("Trying to delete News data...");

            $firstSql = "DELETE FROM news WHERE news_id=:news_id";
            $secondSql = "DELETE FROM news_images WHERE images_id=:images_id";

            $db = self::$conn;
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES,0);

            $firstSt = $db->prepare($firstSql);
            $secondSt = $db->prepare($secondSql);

            $firstSt->bindValue( ':news_id', $this->news_id,  PDO::PARAM_INT);
            $firstSt->execute();
            $firstSt->closeCursor();

            $secondSt->bindValue(':images_id', $this->images_id, PDO::PARAM_INT);

            $result = $secondSt->execute();

            if ( !$result ){
                self::$log->info("The News with id: $this->news_id do not exists");
            }

        } catch ( Exception $exception){
            self::$log->error('News could not be deleted', array( 'exception' => $exception ));
        }

        return $result;
    }





}