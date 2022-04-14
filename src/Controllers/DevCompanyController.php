<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Models\DeveloperCompany;

class DevCompanyController
{

    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('DEVCOMP_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method was executing...');
        if(!empty($_POST)){

            try{
                $company = DeveloperCompany::getInstance();
                if ( !$company::verifyConnection() ) Helpers::manageRedirect();
                $company->storeFormValues($_POST);

                $saveFile = Helpers::saveFile($company, 'company');

                $log->info('Image saved.');

                if ( $saveFile ){

                    $save = $company->insert();

                    if ($save){
                        $log->info('The Company has been created');
                        Helpers::manageRedirect('desarrolladoras');
                    }
                }


            }catch (\Exception $exception){
                $log->error('Something went wrong, cannot create Company', array('exception' => $exception));
            }
            Helpers::manageRedirect('desarrolladoras');

        }
        Helpers::manageRedirect('desarolladoras');

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
            $deleteImg = Helpers::deleteImage($companyData['image'], 'company');

            if(!$deleteImg) $log->warning('The company image could not be delete.');

            if ( !$delete){
                $log->info("Company with id: $id do not exists");
            } else {
                $log->info("Company was deleted successfully.");
            }


        } catch (\Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));
            Helpers::manageRedirect('desarrolladoras');

        }
        Helpers::manageRedirect('desarrolladoras');



    }
    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');
        if (!empty($_POST)){

            try{

                $company = DeveloperCompany::getInstance();
                if ( !$company::verifyConnection() ) Helpers::manageRedirect();
                $company->storeFormValues($_POST);

                //Guardar la imagen
                $lastData = $company::getById($_POST['company_id']);


                $updateImg = Helpers::updateImage($lastData, $company, 'company', $_FILES['image'], $_POST['name']);

                $update = $company->update();

                if ( $update && $updateImg ){
                    Helpers::manageRedirect('desarrolladoras');
                }


            }catch (\Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));
                header("Location: ".BASE_URL."/admin/desarrolladoras/editar/".$_POST['company_id']);
                exit();
            }


        }

        Helpers::manageRedirect('desarrolladoras');


    }

}