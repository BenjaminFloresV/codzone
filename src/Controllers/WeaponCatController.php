<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\FormVerifier;
use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Models\WeaponCategory;
use Exception;

class WeaponCatController
{

    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method was executing...');
        if(!empty($_POST) && FormVerifier::verifyInputs($_POST)){

            try{
                $_POST = DataConverter::trimString($_POST);
                $wpcategory = WeaponCategory::getInstance();
                if ( !$wpcategory::verifyConnection() ) Helpers::manageRedirect();
                $wpcategory->storeFormValues($_POST);

                $saveImg = ImageManager::saveImage($wpcategory, 'weapon_category');
                $log->info('Image saved.');

                if ( $saveImg ){
                    $save = $wpcategory->insert();

                    if ($save){
                        $log->info('The Company has been created');
                        $_SESSION['success-message'] = 'Categoría de arma creada con éxito';
                    }else {
                        $_SESSION['error-message'] = 'No se pudo crear la categoría de arma';
                    }
                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                    Helpers::manageRedirect('categorias-armas/crear');
                }


            }catch (Exception $exception){
                $log->error('Something went wrong, cannot create Company', array('exception' => $exception));
            }
        }else {
            $_SESSION['error-message'] = 'Todos los campos son necesarios';
            Helpers::manageRedirect('categorias-armas/crear');
        }

        Helpers::manageRedirect('categorias-armas/');
    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');

        if (!empty($_POST) && FormVerifier::verifyInputs($_POST)){

            try{

                $wpcategory = WeaponCategory::getInstance();
                if ( !$wpcategory::verifyConnection() ) Helpers::manageRedirect();
                $wpcategory->storeFormValues($_POST);
                $id = $_POST['wpcategory_id'];
                //Guardar la imagen
                $lastData = $wpcategory::getById($id);

                $updateImg = ImageManager::updateImage($lastData, $wpcategory, 'weapon_category', $_FILES['image'], $_POST['name']);



                if ( $updateImg ){
                    $update = $wpcategory->update();
                    if( $update  ) {
                        $_SESSION['success-message'] = 'Categoría de arma actualizada';
                    }else {
                        $_SESSION['error-message'] = 'No se pudo actualizar la categoría de arma';
                    }

                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                }


            }catch (Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));

            }
        }else {
            $_SESSION['error-message'] = 'Todos los campos son necesarios';
        }

        Helpers::manageRedirect('categorias-armas/editar/'.$_POST['wpcategory_id']);

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
            $deleteImg = ImageManager::deleteImage($wpcategoryData['image'], 'weapon_category');



            if ( $deleteImg ) {

                $log->warning('The Weapon Cat Image could not be deleted.');
                $delete =  $wpcategory->delete();
                if( $delete ) {
                    $_SESSION['success-message'] = 'Categoría de arma eliminada con éxito';
                }else {
                    $_SESSION['error-message'] = 'No se pudo eliminar la categoría de arma';
                }

            }


        } catch (Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));
        }

        Helpers::manageRedirect('categorias-armas/');

    }
}