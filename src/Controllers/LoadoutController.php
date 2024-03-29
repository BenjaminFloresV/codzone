<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\ErrorsRedirecter;
use CMS\Helpers\FormVerifier;
use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Helpers\UriVerifier;
use CMS\Middleware\RenderView;
use CMS\Models\Game;
use CMS\Models\Loadout;
use Exception;


class LoadoutController
{

    public function index( $game, $id)
    {
        Helpers::verifyUriRequest();
        try {

            $breadcrumbs = DataConverter::getBreadcrumbs();
            $loadout = Loadout::getInstance();

            if( !is_numeric( $id ) ) ErrorsRedirecter::redirect404();
            $loadoutData = $loadout::getById($id, true);

            if( !$loadoutData ) ErrorsRedirecter::redirect404();
            if( $loadoutData['shortName'] != DataConverter::uriToString($game) ) ErrorsRedirecter::redirect404();
            $uri = DataConverter::stringToUri($loadoutData['title']);
            UriVerifier::verifyLastUriString($uri);

            $loadout->storeFormValues($loadoutData);
            $recommendedLoadouts = $loadout::getAll(true, 3, false, $loadout->getGameId(), $id);

            foreach ( $recommendedLoadouts as $key=>$item ){
                $item['uriShortName'] = DataConverter::stringToUri($item['shortName']);
                $item['uriTitle'] = DataConverter::stringToUri( $item['title'] );

                $recommendedLoadouts[$key] = $item;
            }

            $loadoutData['attachments'] = DataConverter::explodeContent($loadoutData['attachments']);
            $loadoutData['perks'] = DataConverter::explodeContent($loadoutData['perks']);
            $loadoutData['description'] = DataConverter::explodeContent($loadoutData['description']);

            Helpers::deleteSession('maintenance');
            $view = __DIR__.'/../Views/Loadout/loadout.phtml';
            RenderView::renderUser($view, $recommendedLoadouts, $_SERVER['REQUEST_URI'], null, $loadoutData, $breadcrumbs, $loadoutData['title'], $loadoutData['description'][0]['partZero']);

        } catch (Exception $exception) {
            // This session variable is set because in case of an exception, we don't want to show
            // navigaiton options to the user
            $_SESSION['maintenance'] = true;
            RenderView::renderMaintenance();
        }
    }

    public function allLoadouts()
    {
        Helpers::verifyUriRequest();
        try {
            $breadcrumbs = DataConverter::getBreadcrumbs();

            $game = Game::getInstance();

            $allGames = $game::getAll();

            foreach ($allGames as $key=>$game){
                $game['gameUri'] = DataConverter::stringToUri($game['short_name']);
                $allGames[$key] = $game;
            }

            Helpers::deleteSession('maintenance');
            $view = __DIR__.'/../Views/Loadout/all-loadouts.phtml';
            RenderView::renderUser($view, $allGames, $_SERVER['REQUEST_URI'], null, null, $breadcrumbs, 'Cod Zone: Clases Call of Duty', ALL_LOADOUTS_META_DESCRIPTION );

        } catch (Exception $exception) {
            $_SESSION['maintenance'] = true;
            Helpers::deleteSession('maintenance');
        }
    }


    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('LOADOUT_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert method is executing');

        if( !empty($_POST) && FormVerifier::verifyInputs($_POST) ){
            try {
                $_POST = DataConverter::sanitizeData($_POST);
                $loadout = Loadout::getInstance();
                if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
                $saveValues = $loadout->storeFormValues($_POST);
                $saveImg = ImageManager::saveImage($loadout, 'loadout', true, $_POST['gameSubDirectory']);

                if( $saveImg ){

                    $log->info('Loadout Image saved');
                    $saveLoadout = $loadout->insert();

                    if( $saveLoadout){
                        $log->info('The Loadout was created successfully');
                        $_SESSION['success-message'] = 'Clase creada con éxito';
                    }else {
                        $_SESSION['error-message'] = 'No se pudo crear la calse';
                    }
                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                    Helpers::manageRedirect('clases/crear');
                }

            } catch (Exception $exception){
                $log->error('Something went wrong while saving the Loadout', array('exception' => $exception));
            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('clases/crear');
        }
        Helpers::manageRedirect('clases');
    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('LOADOUT_CONTROLLER', 'FirePHPHandler');
        $log->info('Update method is executing');

        if( !empty($_POST) && FormVerifier::verifyInputs($_POST) ){

            try {
                $_POST = DataConverter::dateFormatter($_POST);
                $loadout = Loadout::getInstance();
                if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
                $loadout->storeFormValues($_POST);

                $lastData = $loadout::getById($_POST['loadout_id']);

                $log->info('Trying to update the loadout image...');
                $updateImg = ImageManager::updateImage($lastData, $loadout, 'loadout', $_FILES['image'], $_POST['title'], true, $_POST['gameSubDirectory']);


                if ($updateImg) {

                    $update = $loadout->update();
                    if ($update) {
                        $log->info('Loadout image has been updated successfully...');
                        $_SESSION['success-message'] = 'Clase actualizada con éxito';
                    } else {
                        $log->warning('Loadout image could not be updated');
                        $_SESSION['error-message'] = 'No se pudo actualizar la clase';
                    }
                }else {
                    $_SESSION['error-message']= 'No se pudo guardar la imagen';
                }

            } catch (Exception $exception) {
                $log->error('Something went wrong...', array('exception' => $exception));
            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
        }

        Helpers::manageRedirect('clases/editar/'.$_POST['loadout_id']);
    }

    public function delete(int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');

        try {
            $loadout = Loadout::getInstance();
            if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
            $loadout->setId($id);

            $loadoutData = $loadout::getById($id, true);
            $deleteImg = ImageManager::deleteImage( $loadoutData['image'], 'loadout', true, $loadoutData['gameName'] );

            if($deleteImg) {
                $log->warning('The weapon image could not be delete.');
                $delete = $loadout->delete();

                if( $delete ) {
                    $log->info("Weapon was deleted successfully.");
                    $_SESSION['success-message'] = 'Clase eliminada con éxito';
                } else {
                    $_SESSION['error-message'] = 'No se pudo eliminar la clase';
                    $log->info("Weapon with id: $id do not exists");
                }


            }


        } catch (Exception $exception){
            $log->error('Something went wrong while deleting the loadout', array( 'exception' => $exception ));
        }

        Helpers::manageRedirect('clases');


    }



}