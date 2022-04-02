<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class Category
{

    private static bool|PDO $conn;
    protected int $category_id;
    protected string $category_name;


    public function __construct()
    {

        self::$conn = Connection::dbConnection();
    }


    public static function getAllCategories(): bool|array
    {
        $result = false;
        try {
            $sql = "SELECT * FROM news_category";

            $st = self::$conn->prepare($sql);
            $query = $st->execute();

            if ($query) {
                $result = $st->fetchAll();
            }

        } catch (Exception $exception) {
        }
        return $result;
    }


    public static function getCategoryById( $id )
    {
        $result = false;
        try{
            $sql = "SELECT * FROM news_category WHERE category_id = :category_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":category_id", $id, PDO::PARAM_INT );
            $query = $st->execute();

            if($query){
                $result = $st->fetch();

                if ( !$result  ){
                   //
                }

            }

        } catch (Exception $exception) {
        }

        return $result;

    }


    public function updateCategory(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            $firstSql = "UPDATE news_category SET name=:category_name WHERE category_id=:category_id";

            $db = self::$conn;
            $firstSt = $db->prepare($firstSql);

            $firstSt->bindValue( ':category_id', $this->category_id,  PDO::PARAM_INT);
            $firstSt->bindValue( ':category_name', $this->category_name,  PDO::PARAM_STR);

            $result = $firstSt->execute();


        }catch (Exception $exception){
        }

        return $result;

    }


    public function deleteCategory(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try {

            $sql = "DELETE FROM news_category WHERE category_id=:category_id";
            $st = self::$conn->prepare($sql);

            $st->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);

            $result = $st->execute();

        } catch (Exception $exception){

        }
        return $result;

    }


    public function insertCategory(): bool
    {
        $result = false;
        if ( !self::$conn ) return $result;
        try{
            $sql = "INSERT INTO news_category VALUES( NULL, :category_name )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':category_name', $this->category_name, PDO::PARAM_STR);

            $result = $st->execute();

        }catch (Exception $exception){

        }
        return $result;
    }


}