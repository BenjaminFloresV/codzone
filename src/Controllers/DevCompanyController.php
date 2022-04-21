<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\FormVerifier;
use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Models\DeveloperCompany;
use Exception;

class DevCompanyController
{

    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('DEVCOMP_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method was executing...');
        if(!empty($_POST) && FormVerifier::verifyInputs($_POST)){

            //str_replace('-', '/', $data);
            try{
                $_POST = DataConverter::dateFormatter( $_POST );
                $company = DeveloperCompany::getInstance();
                if ( !$company::verifyConnection() ) Helpers::manageRedirect();
                $company->storeFormValues($_POST);

                $saveImg = ImageManager::saveImage($company, 'company');

                $log->info('Image saved.');

                if ( $saveImg ){

                    $save = $company->insert();

                    if ($save){
                        $log->info('The Company has been created');
                        $_SESSION['success-message'] = 'Compañía creada con éxito';

                    }else {
                        $_SESSION['error-message'] = 'No se pudo crear la Compañía';
                    }
                } else {
                    $_SESSION['error-message'] = 'No has subido ninguna imagen';
                    Helpers::manageRedirect('desarrolladoras/crear');
                }


            }catch (Exception $exception){
                $log->error('Something went wrong, cannot create Company', array('exception' => $exception));
            }

        } else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('desarrolladoras/crear');
        }
        Helpers::manageRedirect('desarrolladoras');

    }

    public function delete( int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('DEVCOMP_CONTROLLER','FirePHPHandler');
        $log->info('Delete Method was executing...');

        try {
            $company = DeveloperCompany::getInstance();
            if ( !$company::verifyConnection() ) Helpers::manageRedirect();
            $company->setId($id);
            $companyData = $company::getById($id);

            $delete =  $company->delete();
            $deleteImg = ImageManager::deleteImage($companyData['image'], 'company');

            if(!$deleteImg){
                $log->warning('The company image could not be delete.');
                $_SESSION['error-message'] = 'No se pudo eliminar la compañía';
            }

            if ( !$delete){
                $log->info("Company with id: $id do not exists");
                $_SESSION['error-message'] = 'No se pudo eliminar la compañía';
            } else {
                $log->info("Company was deleted successfully.");
                $_SESSION['success-message'] = 'La compañía ha sido eliminada.';
            }


        } catch (Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));

        }
        Helpers::manageRedirect('desarrolladoras');



    }
    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');
        if (!empty($_POST) && FormVerifier::verifyInputs($_POST)){

            try{
                $_POST = DataConverter::dateFormatter( $_POST );
                $company = DeveloperCompany::getInstance();
                if ( !$company::verifyConnection() ) Helpers::manageRedirect();
                $company->storeFormValues($_POST);

                //Guardar la imagen
                $lastData = $company::getById($_POST['company_id']);

                $updateImg = ImageManager::updateImage($lastData, $company, 'company', $_FILES['image'], $_POST['name']);

                if ( $updateImg ){
                    $update = $company->update();

                    if( $update ) {
                        $_SESSION['success-message'] = 'Compañía actualizada con éxito';
                        Helpers::manageRedirect('desarrolladoras/editar/'.$_POST['company_id']);
                    }else {
                        $_SESSION['error-message'] = 'Compañía actualizada con éxito';
                    }

                }else {
                    $_SESSION['error-message'] = 'No se pudo actualizar la imagen';
                }


            }catch (Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));
            }


        } else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
        }

        Helpers::manageRedirect('desarrolladoras/editar/'.$_POST['company_id']);

    }

}