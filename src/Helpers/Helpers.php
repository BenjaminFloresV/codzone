<?php

namespace CMS\Helpers;

use CMS\Models\DeveloperCompany;
use CMS\Models\Game;
use JetBrains\PhpStorm\NoReturn;
use ourcodeworld\PNGQuant\PNGQuant;

class Helpers
{

    public static function imageCompress(string $source, string $destination, int $quality)
    {
        $log = NewLogger::newLogger('COMPRESSFILE_HELPERS', 'FirePHPHandler');

        $log->info('Data to compress received.');
        $info = getimagesize($source);
        $log->info('Image size received.');

        $log->info($info['mime']);

        if( $info['mime'] == 'image/jpeg' ){
            $image = imagecreatefromjpeg($source);
        } else if ( $info['mime'] == 'image/gif' ){
            $image = imagecreatefromgif($source);
        } else if( $info['mime'] == 'image/png' ){
            $log->info('Is png');
            $image = imagecreatefrompng($source);
            $log->info('Image created');

        }

        $log->info('Mimetype received.');
        imagepng($image, $destination, $quality);
    }

    #[NoReturn] public static function manageRedirect( string $view = '' ){
        header("Location:".BASE_URL."/admin/$view");
        exit();
    }

    public static  function deleteImage( string $imgName, string $directory, bool $hasSubDirectory = false, string $subDirectory = ''): bool
    {
        $log = NewLogger::newLogger('DELETEIMG_HELPERS', 'FirePHPHandler');
        $log->Info('Trying to delete the image');
        try {
            if( $hasSubDirectory ){
                $path = "../public/uploads/images/$directory/$subDirectory/";
            }else {
                $path = "../public/uploads/images/$directory/";
            }
            return unlink($path.$imgName);

        } catch (\Exception $exception){
            $log->error('Could not deleted the image.', array('exception' => $exception));
            return false;
        }
    }

    public static function updateDirectory($oldDir, $newDir){
        return rename($oldDir, $newDir);
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

                $filename = $name.".".$extension;

                $object->setImage($filename);
                move_uploaded_file($file['tmp_name'], $path.$filename);
                try{
                    $log->info('Trying sto compress the image...');
                    return $compress = self::compressImage($path.$filename, $path.$filename, 9);

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

                // Este código cambia la imagen de png de 32 bits a 8 bits para optimizarla
                $srcimage = imagecreatefrompng($source);
                list($width, $height) = getimagesize($source);

                $img = imagecreatetruecolor($width, $height);
                $bga = imagecolorallocatealpha($img, 0, 0, 0, 127);
                imagecolortransparent($img, $bga);
                imagefill($img, 0, 0, $bga);
                imagecopy($img, $srcimage, 0, 0, 0, 0, $width, $height);
                imagetruecolortopalette($img, false, 255);
                imagesavealpha($img, true);

                $compress = imagepng($img, $destination, $quality);
                imagedestroy($img);
                //imageAlphaBlending($image, true);
                //imageSaveAlpha($image, true);
                return $compress;
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                $image = imagecreatefromjpeg($source);
                break;
        }

        return imagejpeg( $image, $destination, $quality );
    }

    public static function verifySelects( $action ){
        switch ($action){
            case 'update':
                $value = true;
                break;
            case 'insert':
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
            case 'delete';
                $value = 'delete';
                break;
            default:
                $value ='read';
                break;
        }

        return $value;
    }

    public static function retrieveObjectData(string $action, object $object, $id = '', $join = false ): string|array
    {

        if($action == 'read'){

            $allData = $object::getAll($join);
            if( empty($allData) ){
                $allData = $object::getAll();
            }

            return $allData;

        } elseif ($action == 'update'){

            $editId = $id;
            return $object::getById($editId, true);

        } else {
            return 'xd';
        }
    }

    public static function retrieveSelectsData( $objects ): bool|array
    {

        $objectsData = array();
        try{

            foreach ($objects as $object){
                $objectNamespace = explode('\\',get_class($object) );
                $name = $objectNamespace[2];


                $objectsData += [$name => $object::getAll()];

            }
            return $objectsData;

        } catch (\Exception $exception){
            return false;
        }

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
                    return true;
                } else {
                    $object->setImage($lastData['image']);
                    return true;
                }
            }else {

                $filename = $file['name'];
                $filename = explode('.', $filename);

                if ( $name != $lastData['name'] ){

                    $newImage = $name.".".$filename[1];

                    $object->setImage($newImage);
                    unlink($path.$lastData['image']);
                    move_uploaded_file($file['tmp_name'], $path.$newImage);
                    return true;

                } else {
                    unlink($path.$lastData['image']);
                    $lastName = explode('.', $lastData['image']);
                    $newName = $lastName[0].".".$filename[1];
                    $object->setImage($newName);
                    move_uploaded_file($file['tmp_name'], $path.$newName);
                    return true;
                }
            }

            return false;

        } catch (\Exception $exception){
            return $exception;
        }
    }

    public static function verifyUriRequest()
    {
        if( str_ends_with($_SERVER['REQUEST_URI'], '/') ) {
            header("Location:".BASE_URL.rtrim($_SERVER['REQUEST_URI'], '/'));
        }
    }


}