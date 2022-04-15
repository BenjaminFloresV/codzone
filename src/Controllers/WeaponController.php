<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Models\Weapon;
use Exception;

class WeaponController
{
    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WEAPON_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert method is executing...');

        try {

            if ( !empty($_POST) ){


                $weapon = Weapon::getInstance();
                if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
                $weapon->storeFormValues($_POST);

                $saveImg = ImageManager::saveImage($weapon, 'weapon', true, $_POST['gameSubdirectoy']);


                if ( $saveImg ){
                    $log->info('Image saved.');
                    $saveWeapon = $weapon->insert();

                    if ( $saveWeapon ){
                        $log->info('The weapon was created successfully');
                        $_SESSION['success-message'] = 'Arma creada con éxito';
                    } else {
                        $_SESSION['error-message'] = 'No se pudo crear el arma';
                    }

                }
            }


        } catch ( Exception $exception ){
            $log->error('Something went wrong while saving the Weapon', array( 'exception' => $exception ));

        }

        Helpers::manageRedirect('armas');
    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Update method is executing...');

        try {

            if ( !empty($_POST) ){

                $weapon = Weapon::getInstance();
                if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
                $weapon->storeFormValues($_POST);

                $lastData = $weapon::getById($_POST['weapon_id']);

                //update with subdirectory
                $log->info('Trying to update the image...');
                $updateImg = ImageManager::updateImage($lastData, $weapon, 'weapon', $_FILES['image'],$_POST['name'], true, $_POST['gameSubdirectoy'] );

                if( $updateImg ){
                    $log->info('Weapon object has been updated successfully');
                    $update = $weapon->update();
                    if( $update ){
                        $log->info('Weapon image has been updated successfully...');
                        $_SESSION['success-message'] = 'Arma actualizada con éxito';
                    }else {
                        $log->warning('Weapon image could not be updated');
                        $_SESSION['error-message'] = 'No se pudo actualizar el arma';
                    }

                }

            }

            Helpers::manageRedirect('armas/editar/'.$_POST['weapon_id']);

        } catch ( Exception $exception){
            $log->error("Something went wrong... ", array( 'exception' => $exception ));

        }
        Helpers::manageRedirect('armas');


    }

    public function delete(int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');

        try {
            $weapon = Weapon::getInstance();
            if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
            $weapon->setId($id);

            $weaponData = $weapon::getById($id, true);
            $deleteImg = ImageManager::deleteImage( $weaponData['image'], 'weapon', true, $weaponData['gameName'] );

            if($deleteImg) {

                $log->warning('The weapon image could not be delete.');
                $delete = $weapon->delete();

                if( $delete ) {
                    $log->info("Weapon with id: $id do not exists");
                    $_SESSION['success-message'] = 'Arma eliminada con éxito';
                } else {
                    $log->info("Weapon was deleted successfully.");
                    $_SESSION['success-message'] = 'No se pudo eliminar el arma';
                }

            }

            // Terminar delete weapon
            // Implementar en Helpers un método para eliminar imágenes.


        } catch (Exception $exception){
            $log->error('Something went wrong', array( 'exception' => $exception ));
        }

        Helpers::manageRedirect('armas');


    }
}