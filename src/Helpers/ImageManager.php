<?php

namespace CMS\Helpers;

use Exception;

class ImageManager
{

    public static  function deleteImage( $imgName, string $directory, bool $hasSubDirectory = false, string $subDirectory = ''): bool
    {
        $log = NewLogger::newLogger('DELETEIMG_HELPERS', 'FirePHPHandler');
        $log->Info('Trying to delete the image');
        $result = false;
        try {
            if( $hasSubDirectory ){
                $path = "../public/uploads/images/$directory/$subDirectory/";
            }else {
                $path = "../public/uploads/images/$directory/";
            }

            if( is_array( $imgName ) ){
                // Verify if all he elements are null or '', in this case simply return true for continue the execution
                if( ( count(array_keys($imgName, null))/2 )  == ( count($imgName)/2 ) ) return true;
                foreach ( $imgName as $key=>$image ){
                    if ( strlen($image) !=0 && is_numeric( $key )){
                        $result = unlink($path.$image);
                    }
                }

            }else {
                $result = unlink($path.$imgName);
            }

        } catch (Exception $exception){
            $log->error('Could not deleted the image.', array('exception' => $exception));
        }

        return $result;
    }


    public static function updateImage( array $lastData, object $object, string $directory, array $file, string $name, bool $haSubDirectory = false, string $subDirectory = ''): bool
    {
        $result = true;
        try {

            if( $haSubDirectory ){
                $path = "../public/uploads/images/$directory/$subDirectory/";
            }else{
                $path = "../public/uploads/images/$directory/";
            }

            $lastImgName = explode('.', $lastData['image']);

            if( isset($lastData['title']) ) {
                $lastData['name'] = $lastData['title'];
            }

            if ($file['size'] == 0 ){

                if ( $name !== $lastData['name'] ){

                    $newName = $name;
                    $result = rename($path.$lastData['image'], $path.$newName.".".$lastImgName[1] );

                    if( $lastImgName[1] !== 'webp' ){
                        self::compressImage( $path.$newName, $path.$newName.'.webp', IMG_QUALITY);
                        $result = unlink($path.$newName);
                    }

                } else {

                    $newName = $lastImgName[0];
                    if( $lastImgName[1] !== 'webp' ){
                        self::compressImage( $path.$lastData['image'], $path.$newName.'.webp', IMG_QUALITY );
                        $result = unlink($path.$lastData['image']);
                    }
                }

            }else {

                $filename = $file['name'];
                $filename = explode('.', $filename);

                if ( $name != $lastData['name'] ){
                    $newName = $name;
                } else {
                    $newName = $lastImgName[0];
                }

                $result = move_uploaded_file($file['tmp_name'], $path.$newName.".".$filename[1]);
                $result = unlink($path.$lastData['image']);

                if( $filename[1] !== 'webp' ){
                    self::compressImage( $path.$newName.'.'.$filename[1], $path.$newName.'.webp', IMG_QUALITY );
                    $result = unlink($path.$newName.'.'.$filename[1]);
                }

            }

        } catch (Exception){
            // Some logic
        }

        if ( $result ){
            $object->setImage($newName.'.webp');
        }

        return $result;
    }


    public static function saveImage(object $object, $directory, bool $hasSubDirectory = false, string $subdirecoty = '' ): bool
    {
        $log = NewLogger::newLogger('SAVEFILE_HELPERS', 'FirePHPHandler');
        $log->info('Trying to save the image');
        $result = false;
        try{

            $name = $object->getName();
            if (isset($_FILES['image'])) {
                $file = $_FILES['image'];
                $mimetype = $file['type'];

                $extension = explode('/', $mimetype);
                $extension = $extension[1];

                $filename = $name;

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

                    $result = move_uploaded_file($file['tmp_name'], $path.$filename.'.'.$extension);
                    try{
                        $log->info('Trying sto compress the image...');
                        $result = self::compressImage($path.$filename.'.'.$extension, $path.$filename.'.webp', IMG_QUALITY);
                        unlink($path.$filename.'.'.$extension);

                    }catch (Exception $exception){
                        $log->error('Compression has failed.', array('exception' => $exception));
                    }
                }

                if( $result ) {
                    $object->setImage($filename.'.webp');
                }else {
                    $object->setImage($filename.'.'.$extension);
                }

            }

        } catch (Exception $exception){
            $log->error('Something went wrong while saving the image', array('exception' => $exception));
        }


        return $result;
    }


    public static function compressImage( string $source, string $destination, int $quality ): bool
    {

        $info = getimagesize($source);

        try {

            switch ($info['mime']){
                case 'image/png':
                    // Este código cambia la imagen de png de 32 bits a 8 bits para optimizarla
                    $image = imagecreatefrompng($source);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($source);
                    break;
                default:
                    $image = imagecreatefromjpeg($source);
                    break;
            }
            $result = imagewebp($image, $destination, $quality);
            imagedestroy($image);

        } catch (Exception) {
            //
        }

        return $result;

    }


    public static function saveImgUnix(string $directory, string $subDirectory = null, object $object, array $imgMethods, bool $update = false): bool
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
                                $compress = self::compressImage($path.$filename, $path.$filename, IMG_QUALITY);

                            }catch (Exception $exception){
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
                                unlink($path.$lastName);

                            }else {
                                $log->info('Trying update existing image');
                                if( $lastName != null || $lastName != ""  ){
                                    $lastNameParts = explode('.', $lastName);
                                    $extension = $lastNameParts[1];
                                    if( $extension !== 'webp' ) {

                                        $log->info('Trying to change extension');
                                        $object->{$imgMethods[$counter][0]}($lastNameParts[0].'.webp');
                                        $log->info('Trying to compress image');

                                        try {
                                            self::compressImage($path.$lastName, $path.$lastNameParts[0].'.webp', IMG_QUALITY);
                                            unlink($path.$lastName);

                                        } catch (Exception $exception){
                                            echo $exception;
                                        }


                                    } else {
                                        $object->{$imgMethods[$counter][0]}($lastNameParts[0].'.webp');
                                    }
                                }
                            }
                        }
                    }
                }
                usleep(150);
                $counter++;
            }


        } catch (Exception $exception){
            $log->error('Something went wrong.', array('exception'=> $exception) );
        }

        $log->info('Images have been processed successfully.');
        return $moveImg && $compress;
    }

}