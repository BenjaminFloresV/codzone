<?php

namespace CMS\Helpers;

use CMS\Models\Settings;
use Exception;
use JetBrains\PhpStorm\NoReturn;


class Helpers
{

    #[NoReturn] public static function manageRedirect(string $view = '' ){
        header("Location:".BASE_URL."/admin/$view");
        exit();
    }



    public static function updateDirectory($oldDir, $newDir): bool
    {
        return rename($oldDir, $newDir);
    }



    public static function verifySelects( $action ): bool
    {
        return match ($action) {
            'update', 'insert', 'home' => true,
            default => false,
        };
    }


    public static function verifyAction( $action ): string
    {
        return match ($action) {
            'editar' => 'update',
            'crear' => 'insert',
            'delete' => 'delete',
            'home' => 'home',
            default => 'read',
        };
    }

    public static function retrieveObjectData(string $action, object $object, $id = '', $join = false, $getObjectCategories = false ): array|bool
    {
        $result = array();

        if( !empty($_GET) && $action == 'read'){
            $result = $object->getAllFiltered($_GET);
        }else {

            if ($action == 'read') {
                if( $getObjectCategories ){

                    $result = $object::getAllCategories();
                } else {

                    $result = $object::getAll($join);
                    if (empty($result)) {
                        $result = $object::getAll();
                    }

                }

            } elseif ($action == 'update') {

                if( $getObjectCategories ){
                    $result = $object::getCategoryById($id);
                }else {
                    $result = $object::getById($id, true);
                }
            } elseif( $action == 'insert') {
                $result = true;
            }
        }



        return $result;

    }

    public static function verifyUriRequest()
    {
        if( str_ends_with($_SERVER['REQUEST_URI'], '/') ) {
            header("Location:".BASE_URL.rtrim($_SERVER['REQUEST_URI'], '/'));
        }
    }

    public static function retrieveSelectsData( $objects, $getCategories = false, array $getSettings = null ): bool|array
    {

        $objectsData = array();
        $result = false;
        try{

            foreach ($objects as $object){
                $objectNamespace = explode('\\',get_class($object) );
                $name = $objectNamespace[2];


                if( $getCategories ) {
                    $objectsData += [$name => $object::getAllCategories()];
                }else {
                    $objectsData += [$name => $object::getAll()];
                }
            }

            if( is_null($getSettings) !== true){
                $settingObject = Settings::getInstance();
                $settings = array();
                foreach ($getSettings as $setting){
                    $settings += [$setting => $settingObject->getOneSetting($setting)];
                }
                $objectsData['Settings'] = $settings;
            }

            $result = $objectsData;

        } catch (Exception){
        }

        return $result;

    }


    public static function isAdmin()
    {
        if( !isset($_SESSION['admin'])) {
            header("Location:".BASE_URL);
            exit();
        }
    }

    public static function deleteSession( string $name )
    {
        if(isset($_SESSION[$name])){
            $_SESSION[$name] = null;
            unset($_SESSION[$name]);
        }
    }

}