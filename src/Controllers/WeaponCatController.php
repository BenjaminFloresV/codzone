<?php

namespace CMS\Controllers;
use CMS\Helpers\Connection;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Models\DeveloperCompany;
use CMS\Models\Game;
use CMS\Models\WeaponCategory;

class WeaponCatController
{

    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method was executing...');
        if(!empty($_POST)){

            try{
                $wpcategory = WeaponCategory::getInstance();
                if ( !$wpcategory::verifyConnection() ) Helpers::manageRedirect();
                $wpcategory->storeFormValues($_POST);

                $saveFile = Helpers::saveFile($wpcategory, 'weapon_category');
                $log->info('Image saved.');

                if ( $saveFile ){

                    $save = $wpcategory->insert();

                    if ($save){
                        $log->info('The Company has been created');
                        Helpers::manageRedirect('categorias-armas/');
                    }
                }

                Helpers::manageRedirect('categorias-armas/');



            }catch (\Exception $exception){
                $log->error('Something went wrong, cannot create Company', array('exception' => $exception));
            }


        }

        Helpers::manageRedirect('categorias-armas/');
    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');

        if (!empty($_POST)){

            try{

                $wpcategory = WeaponCategory::getInstance();
                if ( !$wpcategory::verifyConnection() ) Helpers::manageRedirect();
                $wpcategory->storeFormValues($_POST);
                $id = $_POST['wpcategory_id'];
                //Guardar la imagen
                $lastData = $wpcategory::getById($id);

                $updateImg = Helpers::updateImage($lastData, $wpcategory, 'weapon_category', $_FILES['image'], $_POST['name']);

                $update = $wpcategory->update();

                if ( $update && $updateImg ){
                    Helpers::manageRedirect('categorias-armas/');
                }


            }catch (\Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));
                Helpers::manageRedirect('categorias-armas/');
            }


        }

        Helpers::manageRedirect('categorias-armas/');
    }

    public function delete( int $id )
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER','FirePHPHandler');
        $log->info('Delete Method was executing...');

        try {

            $wpcategory = WeaponCategory::getInstance();
            if ( !$wpcategory::verifyConnection() ) Helpers::manageRedirect();
            $wpcategory->setId($id);

            $wpcategoryData = $wpcategory::getById($id);
            $deleteImg = Helpers::deleteImage($wpcategoryData['image'], 'weapon_category');
            $delete =  $wpcategory->delete();


            if ( !$deleteImg ) $log->warning('The Weapon Cat Image could not be deleted.');

            if ( !$delete ){
                $log->info("Company with id: $id do not exists");

            } else {

                $log->info("Company was deleted successfully.");
            }
            Helpers::manageRedirect('categorias-armas/');


        } catch (\Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));
            Helpers::manageRedirect('categorias-armas/');
        }

    }
}