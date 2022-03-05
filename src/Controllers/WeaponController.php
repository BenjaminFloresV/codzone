<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Models\Weapon;

class WeaponController
{
    public function insert(): void
    {
        $log = NewLogger::newLogger('WEAPON_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert method is executing...');

        try {

            if ( !empty($_POST) ){


                $weapon = new Weapon();
                if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
                $weapon->storeFormValues($_POST);

                $saveImg = Helpers::saveFile($weapon, 'weapon', true, $_POST['gameSubdirectoy']);


                if ( $saveImg ){
                    $log->info('Image saved.');
                    $saveWeapon = $weapon->insert();

                    if ( $saveWeapon ){
                        $log->info('The weapon was created successfully');
                        Helpers::manageRedirect('armas');
                    }

                }

                Helpers::manageRedirect('armas');
            }


        } catch ( \Exception $exception ){
            $log->error('Something went wrong while saving the Weapon', array( 'exception' => $exception ));

        }

        Helpers::manageRedirect('armas');
    }

    public function update()
    {
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Update method is executing...');

        try {

            if ( !empty($_POST) ){

                $weapon = new Weapon();
                if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
                $weapon->storeFormValues($_POST);

                $lastData = $weapon::getById($_POST['weapon_id']);

                //update with subdirectory
                $log->info('Trying to update the image...');
                $updateImg = Helpers::updateImage($lastData, $weapon, 'weapon', $_FILES['image'],$_POST['name'], true, $_POST['gameSubdirectoy'] );
                $update = $weapon->update();



                if( $update ){
                    $log->info('Weapon object has been updated successfully');

                    if( $updateImg ){
                        $log->info('Weapon image has been updated successfully...');
                    }else {
                        $log->warning('Weapon image could not be updated');
                    }

                    Helpers::manageRedirect('armas');
                }else {
                    $log->warning('Weapon object could not be updated');
                }


            }

            Helpers::manageRedirect('armas');

        } catch ( \Exception $exception){
            $log->error("Somthing went wrong... ", array( 'exception' => $exception ));

        }
        Helpers::manageRedirect('armas');


    }

    public function delete(int $id)
    {
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');

        try {


            $weapon = new Weapon();
            if ( !$weapon::verifyConnection() ) Helpers::manageRedirect();
            $weapon->setId($id);

            $weaponData = $weapon::getById($id, true);
            $delete = $weapon->delete();
            $deleteImg = Helpers::deleteImage( $weaponData['image'], 'weapon', true, $weaponData['gameName'] );

            if(!$deleteImg) $log->warning('The weapon image could not be delete.');

            if ( !$delete){
                $log->info("Weapon with id: $id do not exists");
            } else {
                $log->info("Weapon was deleted successfully.");
            }

            // Terminar delete weapon
            // Implementar en Helpers un método para eliminar imágenes.


        } catch (\Exception $exception){
            $log->error('Something went wrong', array( 'exception' => $exception ));
            Helpers::manageRedirect('armas');
        }

        Helpers::manageRedirect('armas');


    }
}