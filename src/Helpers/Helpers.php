<?php

namespace CMS\Helpers;

use CMS\Models\Settings;
use JetBrains\PhpStorm\NoReturn;
use mysql_xdevapi\Exception;


class Helpers
{

    public static function manageRedirect( string $view = '' ){
        header("Location:".BASE_URL."/admin/$view");
        exit();
    }

    public static  function deleteImage( $imgName, string $directory, bool $hasSubDirectory = false, string $subDirectory = ''): bool
    {
        $log = NewLogger::newLogger('DELETEIMG_HELPERS', 'FirePHPHandler');
        $log->Info('Trying to delete the image');
        try {
            if( $hasSubDirectory ){
                $path = "../public/uploads/images/$directory/$subDirectory/";
            }else {
                $path = "../public/uploads/images/$directory/";
            }

            if( is_array( $imgName ) ){

                $result = false;

                foreach ( $imgName as $key=>$image ){
                    if ( strlen($image) !=0 && is_numeric( $key )){
                       $result = unlink($path.$image);
                    }
                }

                return $result;

            }else {
                return unlink($path.$imgName);
            }


        } catch (\Exception $exception){
            $log->error('Could not deleted the image.', array('exception' => $exception));
            return false;
        }
    }

    public static function updateDirectory($oldDir, $newDir){
        return rename($oldDir, $newDir);
    }

    public static function updateImage( array $lastData, object $object, string $directory, array $file, string $name, bool $haSubDirectory = false, string $subDirectory = '')
    {

        try {

            if( $haSubDirectory ){
                $path = "../public/uploads/images/$directory/$subDirectory/";
            }else{
                $path = "../public/uploads/images/$directory/";
            }

            if ($file['size'] == 0 ){

                if ( $name != $lastData['name'] ){

                    $lastName = explode('.', $lastData['image']);
                    $newName = $name.".".$lastName[1];
                    $object->setImage( $newName );
                    rename($path.$lastData['image'], $path.$newName );

                    if( $lastName[1] !== 'webp' ){
                        $object->setImage($name.'.webp');
                        self::compressImage( $path.$newName, $path.$name.'.webp', 40 );
                        unlink($path.$newName);
                    }

                } else {
                    $lastName = explode('.', $lastData['image']);
                    $name = $lastName[0];
                    $object->setImage($lastData['image']);

                    if( $lastName[1] !== 'webp' ){
                        $object->setImage($name.'.webp');
                        self::compressImage( $path.$lastData['image'], $path.$name.'.webp', 40 );
                        unlink($path.$lastData['image']);

                    }

                }

                return true;



            }else {

                $filename = $file['name'];
                $filename = explode('.', $filename);

                if ( $name != $lastData['name'] ){

                    $newImage = $name.".".$filename[1];

                    $object->setImage($name.'.webp');
                    unlink($path.$lastData['image']);
                    move_uploaded_file($file['tmp_name'], $path.$newImage);

                } else {
                    unlink($path.$lastData['image']);
                    $name = explode('.', $lastData['image']);
                    $name = $name[0];
                    $newImage = $name.".".$filename[1];
                    $object->setImage($name.'.webp');
                    move_uploaded_file($file['tmp_name'], $path.$newImage);

                }

                if( $filename[1] !== 'webp' ){
                    self::compressImage( $path.$newImage, $path.$name.'.webp', 40 );
                    unlink($path.$newImage);
                }

                return true;
            }


        } catch (\Exception $exception){
            return $exception;
        }
    }


    public static function saveFile(object $object, $directory, bool $hasSubDirectory = false, string $subdirecoty = '' ): bool
    {
        $log = NewLogger::newLogger('SAVEFILE_HELPERS', 'FirePHPHandler');
        $log->info('Trying to save the image');
        try{

            $name = $object->getName();
            if (isset($_FILES['image'])) {
                $file = $_FILES['image'];
                $filename = $file['name'];
                $mimetype = $file['type'];
            }

            $log->info('Trying to save the image');

            if ($mimetype == 'image/jpg' || $mimetype == 'image/jpeg' || $mimetype == 'image/png' || $mimetype == 'image/gif') {
                if ( $hasSubDirectory ){
                    $path = "../public/uploads/images/$directory/$subdirecoty/";

                }else {

                    $path = "../public/uploads/images/$directory/";

                }
                if (!is_dir($path)) {

                    mkdir($path, 0777, true);
                }

                $extension = explode('.', $filename);
                $extension = $extension[1];

                $filename = $name.".webp";

                $object->setImage($filename);
                move_uploaded_file($file['tmp_name'], $path.$filename);
                try{
                    $log->info('Trying sto compress the image...');
                    return $compress = self::compressImage($path.$filename, $path.$filename, 40);

                }catch (\Exception $exception){
                    $log->error('Compression has failed.', array('exception' => $exception));
                }

            }

        } catch (\Exception $exception){
            $log->error('Something went wrong while saving the image', array('exception' => $exception));
        }

        return false;
    }

    public static function compressImage( string $source, string $destination, int $quality ): bool
    {
        $info = getimagesize($source);
        switch ($info['mime']){
            case 'image/png':
                try {
                    // Este código cambia la imagen de png de 32 bits a 8 bits para optimizarla
                    $srcimage = imagecreatefrompng($source);
                    imagepalettetotruecolor($srcimage);
                    imagealphablending($srcimage, true);
                    imagesavealpha($srcimage, true);
                    /*list($width, $height) = getimagesize($source);

                    $img = imagecreatetruecolor($width, $height);
                    $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
                    imagecolortransparent($img, $bga);
                    imagefill($img, 0, 0, $bga);
                    imagecopy($img, $srcimage, 0, 0, 0, 0, $width, $height);
                    imagetruecolortopalette($img, false, 255);
                    imagesavealpha($img, true);
                    */
                    $compress = imagewebp($srcimage, $destination, $quality);
                    imagedestroy($srcimage);
                    //imageAlphaBlending($image, true);
                    //imageSaveAlpha($image, true);
                    return $compress;
                    break;
                } catch (Exception $exception) {
                    echo $exception;
                }
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                $image = imagecreatefromjpeg($source);
                break;
        }

        return imagewebp( $image, $destination, $quality );
    }

    public static function saveImgUnix(string $directory, string $subDirectory = null, object $object, array $imgMethods, bool $update = false)
    {
        $log = NewLogger::newLogger('HELPERS_SAVEIMGUNIX', 'FirePHPHandler');
        $log->info('Trying to save image with unix time name');

        $compress = true;
        $moveImg = true;

        if ( $subDirectory != null ){
            $path = "../public/uploads/images/$directory/$subDirectory/";

        }else {

            $path = "../public/uploads/images/$directory/";

        }

        $counter = 0;
        try {
            foreach ($_FILES as $file) {
                if ( $file['size'] !== 0 ){

                    if( $counter < count($imgMethods) ){
                        if ($file['type'] == 'image/jpg' || $file['type'] == 'image/jpeg' || $file['type'] == 'image/png' || $file['type'] == 'image/gif') {

                            if (!is_dir($path)) {

                                mkdir($path, 0777, true);
                            }

                            $type = explode('/', $file['type'] );
                            $type = $type[1];

                            $unixTime = round(microtime(true)*1000);
                            $filename = $unixTime.'.webp';

                            if ( $update ){


                                $lastName = $object->{$imgMethods[$counter][1]}();

                                if( $lastName != null ){
                                    unlink($path.$lastName);
                                }


                                $object->{$imgMethods[$counter][0]}($filename);

                            }else {
                                $object->{$imgMethods[$counter]}($filename);
                            }

                            $moveImg = move_uploaded_file($file['tmp_name'], $path.$filename);
                            try{
                                $log->info('Trying sto compress the image...');
                                $compress = self::compressImage($path.$filename, $path.$filename, 40);

                            }catch (\Exception $exception){
                                $log->error('Compression has failed.', array('exception' => $exception));
                            }
                        }
                    }
                }else {
                    if( $counter < count($imgMethods) ){
                        if( $update ){

                            $lastName = $object->{$imgMethods[$counter][1]}();


                            if( $imgMethods[$counter][2] ){
                                $log->info('Trying to delete one image.');
                                $object->{$imgMethods[$counter][0]}("");
                                $log->info('Trying to delete one image2.');


                                $delete = unlink($path.$lastName);
                                $log->info('Trying to delete one image3.');

                            }else {
                                if( $lastName != null || $lastName != ""  ){
                                    $lastNameParts = explode('.', $lastName);
                                    $extension = $lastNameParts[1];
                                    if( $extension !== 'webp' ) {
                                        $object->{$imgMethods[$counter][0]}($lastNameParts[0].'.webp');
                                        self::compressImage($path.$lastName, $path.$lastNameParts[0].'.webp', 40);
                                        unlink($path.$lastName);
                                    } else {

                                        $object->{$imgMethods[$counter][0]}($lastName);
                                    }

                                }
                            }


                        }
                    }

                }
                usleep(150);
                $counter++;
            }


        } catch (\Exception $exception){
            $log->error('Something went wrong.', array('exception'=> $exception) );
        }

        $log->info('Images saved successfully.');
        return $moveImg && $compress;


    }

    public static function verifySelects( $action ){
        switch ($action){
            case 'update':
                $value = true;
                break;
            case 'insert':
                $value = true;
                break;
            case 'home':
                $value = true;
                break;
            default:
                $value = false;
                break;
        }
        return $value;
    }


    public static function verifyAction( $action )
    {
        switch ($action) {
            case 'ver':
                $value = 'read';
                break;
            case 'editar':
                $value = 'update';
                break;
            case 'crear':
                $value = 'insert';
                break;
            case 'delete':
                $value = 'delete';
                break;
            case 'home':
                $value = 'home';
                break;
            default:
                $value ='read';
                break;
        }

        return $value;
    }

    public static function retrieveObjectData(string $action, object $object, $id = '', $join = false, $getObjectCategories = false )
    {
        if( !empty($_GET)){
            return $object->getAllFiltered($_GET);
        }

        if ($action == 'read') {
            if( $getObjectCategories ){

                return $object::getAllCategories();
            }


            $allData = $object::getAll($join);
            if (empty($allData)) {
                $allData = $object::getAll();
            }

            return $allData;

        } elseif ($action == 'update') {

            if( $getObjectCategories ){
                return $object::getCategoryById($id);
            }else {
                return $object::getById($id, true);
            }



        } else {
            return 'xd';
        }

    }

    public static function retrieveSelectsData( $objects, $getCategories = false, array $getSettings = null ): bool|array
    {

        $objectsData = array();
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
                $settingObject = new Settings();
                $settings = array();
                foreach ($getSettings as $setting){
                    $settings += [$setting => $settingObject->getOneSetting($setting)];
                }
                $objectsData['Settings'] = $settings;
            }

            return $objectsData;

        } catch (\Exception $exception){
            return false;
        }

    }




    public static function verifyUriRequest()
    {
        if( str_ends_with($_SERVER['REQUEST_URI'], '/') ) {
            header("Location:".BASE_URL.rtrim($_SERVER['REQUEST_URI'], '/'));
        }
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