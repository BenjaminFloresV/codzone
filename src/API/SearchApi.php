<?php

namespace CMS\API;

use CMS\Helpers\Connection;
use Exception;
use PDO;

header("Access-Control-Allow-Origin: http://127.0.0.1:6379"); // With this header, we are telling php that any request is valid.
header('Access-Control-Allow-Credentials: true');

class SearchApi
{
    private function verifyApiKey( string $apiKey, PDO $conn )
    {

        $keyParts = json_decode($apiKey);
        $userProvidedKey = $keyParts[1].$keyParts[3].$keyParts[2].$keyParts[5].$keyParts[0].$keyParts[4];
        $userProvidedKey = $conn->quote($userProvidedKey);
        $result = $conn->query("SELECT * FROM  users WHERE api_key=$userProvidedKey");

        return $result->fetchColumn();

    }

    private function accessActor($conn, $sql){

        try {

            $result = $conn->query( $sql );

            $array = array();

            foreach ( $result as $row){
                if( !in_array($row, $array) ){
                    $array[] = $row;
                }

            }

            if( empty($array) ){
                echo json_encode(['status'=>'No hay resultados para tu busqueda']);
                exit();
            }

            echo json_encode($array);

        } catch (Exception $exception){
            echo $exception;
        }

        exit();

    }


    public function index()
    {
        $conn = Connection::dbConnection();
        header('Access-Control-Allow-Headers: X-Api-Key');
        header('Access-Control-Allow-Headers: Search-Input');
        header('Access-Control-Allow-Headers: Last-Search-Id');


        switch ( $_SERVER['REQUEST_METHOD'] ){
            case 'POST':

                $email = $conn->quote($_POST['email']);
                $apiKey = password_hash($email, PASSWORD_DEFAULT);

                $st = $conn->prepare("INSERT INTO users VALUES(NULL,NULL,NULL, :email, :apikey)");
                $st->bindValue(':email', $email, PDO::PARAM_STR);
                $st->bindValue(':apikey', $apiKey, PDO::PARAM_STR);

                $result = $st->execute();

                if( $result ){
                    echo json_encode(['status'=>'success', 'apikey' => $apiKey]);
                    exit();
                }
                echo json_encode(['status'=>'failure', 'apikey']);

                break;
            case 'GET':

                $userProvidedKey = $_SERVER['HTTP_X_API_KEY'];
                $searchInput = $_SERVER['HTTP_SEARCH_INPUT'];
                $lastSearchId = $_SERVER['HTTP_LAST_SEARCH_ID'];

                $allowMinorId = false;
                if( $lastSearchId != null ){
                    $lastSearchParsed = json_decode($lastSearchId);
                    $allowMinorId = true;
                }

                $keyParts = json_decode($userProvidedKey);
                $userProvidedKey = $keyParts[1].$keyParts[3].$keyParts[2].$keyParts[5].$keyParts[0].$keyParts[4];

                if( $userProvidedKey === 'null' ){
                    echo json_encode(['status'=>'No access allowed without API Key']);
                    exit();
                }

                if( $searchInput !== null ) {

                    if( empty( $searchInput ) ){
                        echo json_encode(['status'=>'No hay resultados para tu búsqueda']);
                        exit();
                    }

                    $array = array();
                    try {
                        $searchInput = trim($searchInput);
                        $fullSearch = $conn->quote('%'.$searchInput.'%');
                        //CLASES
                        $sql = "SELECT l.title, 'clases' AS startUri, 'loadout' AS startImgUri, 'loadout-article' AS articleType, UNIX_TIMESTAMP(l.creation_date) AS creationDate, l.loadout_id AS id, l.description, g.name AS imgDirectory, g.short_name AS shortNameUri, g.short_name AS catName, l.image AS image FROM ( SELECT * FROM loadout l ORDER BY l.loadout_id DESC ) AS l";
                        $sql .= " INNER JOIN game g ON g.game_id=l.game_id WHERE CONCAT(l.title,' ', g.short_name,' ', l.description) LIKE $fullSearch";

                        if( $allowMinorId ) {
                            $loadoutId = $lastSearchParsed[0]->loadout;
                            $sql .= " AND l.loadout_id < $loadoutId";
                        }

                        //NOTICIAS
                        $sql .= " UNION ALL SELECT n.title, 'noticias' AS startUri, 'news' AS startImgUri, 'news-article' AS articleType, UNIX_TIMESTAMP(n.creation_date) AS creationDate, n.news_id AS id, n.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, nimages.image_desc AS image FROM ( SELECT * FROM news n ORDER BY n.news_id DESC ) AS n";
                        $sql .= " INNER JOIN news_category ncat ON ncat.category_id=n.category_id";
                        $sql .= " INNER JOIN news_images nimages ON nimages.images_id=n.images_id WHERE CONCAT(n.title,' ',ncat.name, ' ', n.description) LIKE $fullSearch";

                        if( $allowMinorId ) {
                            $newsId = $lastSearchParsed[0]->news;
                            $sql .= " AND n.news_id < $newsId";
                        }

                        //TUTORIALES
                        $sql .= " UNION ALL SELECT t.title, 'tutoriales' AS startUri, 'tutorial' AS startImgUri, 'tutorial-article' AS articleType, UNIX_TIMESTAMP(t.creation_date) AS creationDate, t.tutorial_id AS id, t.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, timages.image_title AS image FROM ( SELECT * FROM tutorial t ORDER BY t.tutorial_id DESC ) AS t";
                        $sql .= " INNER JOIN news_category ncat ON ncat.category_id=t.category_id";
                        $sql .= " INNER JOIN tutorial_images timages ON timages.images_id=t.images_id WHERE CONCAT(t.title,' ',ncat.name, ' ', t.description) LIKE $fullSearch";

                        if( $allowMinorId ) {
                            $tutorialId = $lastSearchParsed[0]->tutorial;
                            $sql .= " AND t.tutorial_id < $tutorialId";
                        }

                        $sql .= " ORDER BY id DESC LIMIT 8";

                        $result = $conn->query( $sql );

                        if( $result->rowCount() > 0 ){

                            foreach ( $result as $row){
                                if ( !in_array($row, $array) ){
                                    $array[] = $row;
                                }
                            }

                        } else {
                            $searchParts = explode(" ", $searchInput);
                            $searchParts = array_reverse($searchParts);
                            foreach ( $searchParts as $value ) {
                                $searchText = $conn->quote('%'.$value.'%');

                                $sql = "SELECT l.title, 'clases' AS startUri, 'loadout' AS startImgUri, 'loadout-article' AS articleType, UNIX_TIMESTAMP(l.creation_date) AS creationDate, l.loadout_id AS id, l.description, g.name AS imgDirectory, g.short_name AS shortNameUri, g.short_name AS catName, l.image AS image FROM ( SELECT * FROM loadout l ) AS l";
                                $sql .= " INNER JOIN game g ON g.game_id=l.game_id WHERE CONCAT(l.title,' ', g.short_name,' ', l.description) LIKE $searchText";

                                if( $allowMinorId ) {
                                    $loadoutId = $lastSearchParsed[0]->loadout;
                                    $sql .= " AND l.loadout_id < $loadoutId";
                                }

                                //NOTICIAS
                                $sql .= " UNION ALL SELECT n.title, 'noticias' AS startUri, 'news' AS startImgUri, 'news-article' AS articleType, UNIX_TIMESTAMP(n.creation_date) AS creationDate, n.news_id AS id, n.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, nimages.image_desc AS image FROM ( SELECT * FROM news n ORDER BY n.news_id DESC ) AS n";
                                $sql .= " INNER JOIN news_category ncat ON ncat.category_id=n.category_id";
                                $sql .= " INNER JOIN news_images nimages ON nimages.images_id=n.images_id WHERE CONCAT(n.title,' ',ncat.name, ' ', n.description) LIKE $searchText";

                                if( $allowMinorId ) {
                                    $newsId = $lastSearchParsed[0]->news;

                                    $sql .= " AND n.news_id < $newsId";
                                }

                                //TUTORIALES
                                $sql .= " UNION ALL SELECT t.title, 'tutoriales' AS startUri, 'tutorial' AS startImgUri, 'tutorial-article' AS articleType, UNIX_TIMESTAMP(t.creation_date) AS creationDate, t.tutorial_id AS id, t.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, timages.image_title AS image FROM ( SELECT * FROM tutorial t ORDER BY t.tutorial_id DESC ) AS t";
                                $sql .= " INNER JOIN news_category ncat ON ncat.category_id=t.category_id";
                                $sql .= " INNER JOIN tutorial_images timages ON timages.images_id=t.images_id WHERE CONCAT(t.title,' ',ncat.name, ' ', t.description) LIKE $searchText";


                                if( $allowMinorId ) {
                                    $tutorialId = $lastSearchParsed[0]->tutorial;
                                    $sql .= " AND t.tutorial_id < $tutorialId";
                                }

                                $sql .= " LIMIT 5";

                                $result = $conn->query($sql);

                                foreach ($result as $row) {
                                    if ( !in_array($row, $array) ){
                                        $array[] = $row;
                                    }
                                }

                            }

                        }

                        if( empty($array) ){
                            echo json_encode(['status'=>'No hay resultados para tu busqueda']);
                            exit();
                        }

                        echo json_encode($array);
                        exit();

                    } catch ( Exception $exception){
                        echo $exception;
                    }



                }


                break;


        }

        exit();
    }

    public function getLoadouts()
    {
        if( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            header('Access-Control-Allow-Headers: X-Api-Key');
            header('Access-Control-Allow-Headers: X-Last-Loadout-Id');

            $conn = Connection::dbConnection();

            if( $this->verifyApiKey($_SERVER['HTTP_X_API_KEY'], $conn) === false ) {
                echo json_encode(['status'=>'Access Denied, invalid API Key']);
                exit();
            }


            $lastId = $conn->quote( $_SERVER['HTTP_X_LAST_LOADOUT_ID'] );

            $sql = "SELECT l.title, 'clases' AS startUri, 'loadout' AS startImgUri, 'loadout-article' AS articleType, UNIX_TIMESTAMP(l.creation_date) AS creationDate, l.loadout_id AS id, l.description, g.name AS imgDirectory, g.short_name AS shortNameUri, g.short_name AS catName, l.image AS image FROM ( SELECT * FROM loadout l ORDER BY l.loadout_id DESC ) AS l";
            $sql .= " INNER JOIN game g ON g.game_id=l.game_id WHERE l.loadout_id < $lastId LIMIT 5";

            $this->accessActor($conn, $sql);

        }else {
            echo json_encode(['status'=>'Access Denied']);
        }
        exit();


    }

    public function getNews()
    {

        if( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            header('Access-Control-Allow-Headers: X-Api-Key');
            header('Access-Control-Allow-Headers: X-Last-Loadout-Id');
            header('Access-Control-Allow-Headers: Subcategory-Name');



            $conn = Connection::dbConnection();

            if( $this->verifyApiKey($_SERVER['HTTP_X_API_KEY'], $conn) === false ) {
                echo json_encode(['status'=>'Access Denied, invalid API Key.']);
                exit();
            }

            if( $_SERVER['HTTP_SUBCATEGORY_NAME'] !== 'null' ){
                $subcategoryName =$conn->quote( $_SERVER['HTTP_SUBCATEGORY_NAME'] );
            }


            $lastId =$conn->quote( $_SERVER['HTTP_X_LAST_LOADOUT_ID'] );

            //NOTICIAS
            $sql = "SELECT n.title, 'noticias' AS startUri, 'news' AS startImgUri, 'news-article' AS articleType, UNIX_TIMESTAMP(n.creation_date) AS creationDate, n.news_id AS id, n.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, nimages.image_title AS image FROM ( SELECT * FROM news n ORDER BY n.news_id DESC ) AS n";
            $sql .= " INNER JOIN news_category ncat ON ncat.category_id=n.category_id";
            $sql .= " INNER JOIN news_images nimages ON nimages.images_id=n.images_id WHERE n.news_id < $lastId";
            if( $_SERVER['HTTP_SUBCATEGORY_NAME'] !== 'null' ) {
                $sql .= " AND ncat.name = $subcategoryName";
            }
            $sql .= " ORDER BY n.news_id DESC LIMIT 1";


            $this->accessActor($conn, $sql);

        }else {
            echo json_encode(['status'=>'Access Denied']);
        }
        exit();
    }

    public function getTutorials()
    {

        if( $_SERVER['REQUEST_METHOD'] === 'GET' ){

            header('Access-Control-Allow-Headers: X-Api-Key');
            header('Access-Control-Allow-Headers: X-Last-Loadout-Id');
            header('Access-Control-Allow-Headers: Subcategory-Name');



            $conn = Connection::dbConnection();

            if( $this->verifyApiKey($_SERVER['HTTP_X_API_KEY'], $conn) === false ) {
                echo json_encode(['status'=>'Access Denied, invalid API Key.']);
                exit();
            }
            if( $_SERVER['HTTP_SUBCATEGORY_NAME'] !== 'null' ){
                $subcategoryName =$conn->quote( $_SERVER['HTTP_SUBCATEGORY_NAME'] );
            }


            $lastId =$conn->quote( $_SERVER['HTTP_X_LAST_LOADOUT_ID'] );

            //NOTICIAS
            $sql = "SELECT t.title, 'tutoriales' AS startUri, 'tutorial' AS startImgUri, 'tutorial-article' AS articleType, UNIX_TIMESTAMP(t.creation_date) AS creationDate, t.tutorial_id AS id, t.description, ncat.name AS imgDirectory, ncat.name AS shortNameUri, ncat.name AS catName, timages.image_title AS image FROM ( SELECT * FROM tutorial t ORDER BY t.tutorial_id DESC ) AS t";
            $sql .= " INNER JOIN news_category ncat ON ncat.category_id=t.category_id";
            $sql .= " INNER JOIN tutorial_images timages ON timages.images_id=t.images_id WHERE t.tutorial_id < $lastId";
            if( $_SERVER['HTTP_SUBCATEGORY_NAME'] !== 'null' ) {
                $sql .= " AND ncat.name = $subcategoryName";
            }
            $sql .= " ORDER BY t.tutorial_id DESC LIMIT 1";


            $this->accessActor($conn, $sql);

        }else {
            echo json_encode(['status'=>'Access Denied']);
        }
        exit();

    }


}