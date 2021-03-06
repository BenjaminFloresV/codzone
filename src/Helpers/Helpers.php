<?php

namespace CMS\Helpers;

use CMS\Models\Settings;
use Exception;
use JetBrains\PhpStorm\NoReturn;


class Helpers
{
    // This method is used to do redirects at Administrator Views
    #[NoReturn] public static function manageRedirect(string $view = '' ){
        header("Location:".BASE_URL."/admin/$view");
        exit();
    }


    // This method update a directory's name if the name of a game or any other subject that changes, because
    // some subjects has a name dependency associated to a category or entity.
    public static function updateDirectory($oldDir, $newDir): bool
    {
        return rename($oldDir, $newDir);
    }


    // This method verify if a specific Views needs categories data or entities
    public static function verifySelects( $action ): bool
    {
        return match ($action) {
            'update', 'insert', 'home' => true,
            default => false,
        };
    }

    //  This method verify the URL endpoint in some Adminsitrator Views
    // to determinates the php file that needs to be loaded from Admin's Views folder.
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

    // This method is used to get object data if we need to update data or show all the data of certain entity.
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

    // This method verifies if the URL requested is valid, if it is not the case, it does a redirection
    public static function verifyUriRequest()
    {
        if( str_ends_with($_SERVER['REQUEST_URI'], '/') ) {
            header("Location:".BASE_URL.rtrim($_SERVER['REQUEST_URI'], '/'));
        }
    }

    // This method returns arrays within array with different entities's data that is necessary for Admin actions( insert, update, etc )
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