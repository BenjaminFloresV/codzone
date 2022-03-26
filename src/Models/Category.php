<?php

namespace CMS\Models;

use CMS\Helpers\Connection;
use CMS\Helpers\NewLogger;
use Exception;
use PDO;

class Category
{

    private static bool|PDO $conn;
    protected int $category_id;
    protected  string $category_name;



    public function __construct()
    {

        self::$conn = Connection::dbConnection();
    }


    public static function getAllCategories()
    {
        
        try {


            $sql = "SELECT * FROM news_category";

            $st = self::$conn->prepare($sql);
            $query = $st->execute();

            if ($query) {
                $newsCat = $st->fetchAll();

                return $newsCat;
            }


            return false;


        } catch (Exception $exception) {

            return false;
        }

    }


    public static function getCategoryById( $id )
    {
        try{

            $sql = "SELECT * FROM news_category WHERE category_id = :category_id";
            $st = self::$conn->prepare( $sql );
            $st->bindValue(":category_id", $id, PDO::PARAM_INT );
            $query = $st->execute();


            if($query){
                $news = $st->fetch();


                if ( !$news  ){

                    return false;
                }

                return $news;
            }

        } catch (Exception $exception) {

            return false;
        }

        return false;

    }


    public function updateCategory(): bool
    {

        if ( !self::$conn ) return false;
        try{


            $firstSql = "UPDATE news_category SET name=:category_name WHERE category_id=:category_id";

            $db = self::$conn;
            $firstSt = $db->prepare($firstSql);

            $firstSt->bindValue( ':category_id', $this->category_id,  PDO::PARAM_INT);
            $firstSt->bindValue( ':category_name', $this->category_name,  PDO::PARAM_STR);

            $query = $firstSt->execute();

            return $query;


        }catch (Exception $exception){

            return false;
        }

    }


    public function deleteCategory(): bool
    {
        if ( !self::$conn ) return false;
        try {

            $sql = "DELETE FROM news_category WHERE category_id=:category_id";
            $st = self::$conn->prepare($sql);

            $st->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);

            $query = $st->execute();


            return $query;

        } catch (Exception $exception){

            return false;
        }

    }


    public function insertCategory(): bool
    {
        if ( !self::$conn ) return false;
        try{

            $sql = "INSERT INTO news_category VALUES( NULL, :category_name )";
            $st = self::$conn->prepare($sql);
            $st->bindValue(':category_name', $this->category_name, PDO::PARAM_STR);

            $query = $st->execute();

            if ( $query ){

                return true;
            }

            return false;

        }catch (Exception $exception){
            return false;
        }

    }


}