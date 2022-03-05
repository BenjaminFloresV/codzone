<?php

namespace CMS\Helpers;

use function CMS\Controllers\verifyUriMatch;

class DataConverter
{
    public static function convertLoadoutInfoFormat( string $text )
    {
        if( !strpos($text, ',') ){
            $objects = array( $text );
        }else{
            $objects = explode(',', $text);
        }

        foreach ( $objects as $key=>$object ){
            $newObject = array();

            $objectInfo = explode('_', $object); // Utilizamos guion bajo como separador en caso de usarse el guion normal para describir el accesorio

            $newObject['partOne'] = trim( $objectInfo[0] );
            $newObject['partTwo'] = trim( $objectInfo[1] );

            $objects[$key] = $newObject;

        }

        return $objects;


    }

    public static function addGameURL(array $data )
    {

        $newData = $data;

        if( $data['gameName'] != null ){

            $gameName = explode("Call of Duty", $data['gameName']);
            $newData['shortName'] = trim( $gameName[1] );

            $gameURL = str_replace( " ", "-",  strtolower($data['gameName']));
            $newData['gameURL'] = $gameURL;
        }


        return $newData;
    }

    public static function stringToUri(string $string)
    {
        $newString = strtolower($string);
        $stringParts = explode(' ', $newString);


        return implode('-', $stringParts);
    }

    public static function uriToString( string $uri )
    {

        if( !strpos($uri, '-') ){
            return ucfirst($uri);
        }


        $uriParts = explode('-', $uri);

        foreach ($uriParts as $key=>$value){
            if ( !is_numeric( $value ) ) {
                $uriParts[$key] = ucfirst( $value );
            }
        }

        return implode(' ', $uriParts);
    }


    public static function getBreadcrumbs()
    {

        $breadcrumbs = array(
            array(
                'word' => 'Inicio',
                'url' => BASE_URL
            )
        );

        $firstExp = "/\/[a-z]+\/[a-z\-]+\d+\/\d+\/[a-z\-\d+]+$/i";
        $thirdExp = "/\/[a-z]+\/[a-z]+\/\d+\/[a-z\-\d+]+$/i";
        $fourthExp = "/\/[a-z]+\/[a-z\-]+\/\d+\/[a-z\-\d+]+$/i";


        $secondExp = "/\/[a-z]+\/\d+\/[a-z\-]+/i";
        $fifthExp = "/\/[a-z]+\/\d+\/[a-z\-\d+]+/i";

        $sixthExp = "/\/[a-z]+\/[a-z\-\d+]+/i";
        $seventhExp = "/\/[a-z]+/i";


        $uriForCrumbs = explode('/', $_SERVER['REQUEST_URI']);

        $mainWord = '';
        $counter = 0;

        function UriMatch($expression ){
            return preg_match( $expression, $_SERVER['REQUEST_URI'] );
        }

        if( UriMatch( $firstExp ) || UriMatch( $thirdExp ) || UriMatch( $fourthExp ) ){

            foreach ($uriForCrumbs as $crumb){
                $crumbData = array();
                if( $crumb != "" ){
                    if( $counter >= 1 && ( !is_numeric( $crumb ) && !strpos($crumb, '-') )){
                        if( UriMatch($thirdExp) ){
                            $mainWord .= $crumb.'/';
                        }else{
                            $mainWord .= $crumb;
                        }

                    }
                    if( !is_numeric( $crumb ) && !strpos($crumb, '-') ){
                        if( $counter > 1 ){
                            $crumbData['word'] = ucfirst($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$mainWord;
                        }else {
                            $crumbData['word'] = ucfirst($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$crumb.'/';
                        }


                    }elseif ( !is_numeric( $crumb ) && ( strpos($crumb, '-') != false ) ){

                        if ( !isset($lastId) ) {
                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$mainWord.'/'.$crumb.'/';
                            if( $counter == 2 ){
                                $mainWord = $mainWord.'/'.$crumb.'/';

                            }

                        }else {

                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL."/$mainWord"."$lastId/$crumb/";

                            unset($lastId);

                        }


                    }elseif ( is_numeric( $crumb )){
                        $lastId = $crumb;
                    }

                    if( count($crumbData) == 0 ){
                        continue;
                    }

                    array_push($breadcrumbs, $crumbData);


                }

                $counter++;
            }


        }elseif ( UriMatch( $secondExp ) || UriMatch( $fifthExp ) || UriMatch( $sixthExp ) || UriMatch( $seventhExp ) ){
            foreach ($uriForCrumbs as $crumb){
                $crumbData = array();
                if( $crumb != "" ){

                    if( $counter >= 1 && $counter < 3 && ( !is_numeric( $crumb ) && !strpos($crumb, '-') )){
                        if( UriMatch($secondExp) || UriMatch($fifthExp)  ){
                            $mainWord .= $crumb.'/';
                        }else{
                            $mainWord .= $crumb;
                        }

                    }

                    if( !is_numeric( $crumb ) && !strpos($crumb, '-') ){

                        if ( !isset($lastId) ) {
                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$mainWord;
                            if( $counter == 2 ){
                                $mainWord = $mainWord.'/'.$crumb.'/';

                            }

                        }else {

                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL."/$mainWord"."$lastId/$crumb/";

                            unset($lastId);

                        }


                    }elseif ( !is_numeric( $crumb ) && ( strpos($crumb, '-') != false ) ){

                        if ( !isset($lastId) ) {
                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$mainWord.'/'.$crumb.'/';
                            if( $counter == 2 ){
                                $mainWord = $mainWord.'/'.$crumb.'/';

                            }

                        }else {

                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL."/$mainWord"."$lastId/$crumb/";

                            unset($lastId);

                        }


                    }elseif( !is_numeric( $crumb ) && !strpos($crumb, '-') ){

                        if ( !isset($lastId) ) {
                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL.'/'.$mainWord.'/'.$crumb.'/';
                            if( $counter == 2 ){
                                $mainWord = $mainWord.'/'.$crumb.'/';

                            }

                        }else {

                            $crumbData['word'] = DataConverter::uriToString($crumb);
                            $crumbData['url'] = BASE_URL."/$mainWord"."$lastId/$crumb/";

                            unset($lastId);

                        }

                    }elseif ( is_numeric( $crumb )){

                        $lastId = $crumb;
                    }

                    if( count($crumbData) == 0 ){
                        continue;
                    }
                    array_push($breadcrumbs, $crumbData);


                }

                $counter++;
            }

        }

        return $breadcrumbs;
    }

}












