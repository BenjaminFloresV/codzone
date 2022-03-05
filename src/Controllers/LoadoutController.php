<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\Game;
use CMS\Models\Loadout;


class LoadoutController
{

    public function index(string $game, int $id)
    {
        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();

        $loadout = new Loadout();

        $loadoutData = $loadout::getById($id, true);

        if( !$loadoutData ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        //Procesar Accesorios y Ventajas
        $loadoutData['attachments'] = DataConverter::convertLoadoutInfoFormat( $loadoutData['attachments'] );
        $loadoutData['perks'] = DataConverter::convertLoadoutInfoFormat( $loadoutData['perks'] );
        $loadoutData['description'] = DataConverter::convertLoadoutInfoFormat( $loadoutData['description'] );

        $endpoint = DataConverter::stringToUri($loadoutData['title']);

        if( !strpos($_SERVER['REQUEST_URI'], $endpoint) ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        $view = __DIR__.'/../Views/Loadout/loadout.phtml';
        RenderView::renderUser($view, null, $_SERVER['REQUEST_URI'], null, $loadoutData, $breadcrumbs);

    }

    public function allLoadouts()
    {
        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();

        $game = new Game();

        $allGames = $game::getAll();

        foreach ($allGames as $key=>$game){
            $game['gameUri'] = DataConverter::stringToUri($game['short_name']);
            $allGames[$key] = $game;
        }

        $view = __DIR__.'/../Views/Loadout/all-loadouts.phtml';
        RenderView::renderUser($view, $allGames, $_SERVER['REQUEST_URI'], null, null, $breadcrumbs );


    }


    public function insert()
    {
        $log = NewLogger::newLogger('LOADOUT_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert method is executing');


        try {
            $loadout = new Loadout();
            if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
            $loadout->storeFormValues($_POST);

            $saveImg = Helpers::saveFile($loadout, 'loadout', true, $_POST['gameSubDirectory']);

            if( $saveImg ){

                $log->info('Loadout Image saved');
                $saveLoadout = $loadout->insert();

                if( $saveLoadout){
                    $log->info('The weapon was created successfully');
                    Helpers::manageRedirect('clases');
                }

            }

            Helpers::manageRedirect('clases');

        } catch (\Exception $exception){
            $log->error('Something went wrong while saving the Loadout', array('exception' => $exception));
        }

        Helpers::manageRedirect('clases');
    }

    public function update()
    {
        $log = NewLogger::newLogger('LOADOUT_CONTROLLER', 'FirePHPHandler');
        $log->info('Update method is executing');


        try {

            if (!empty($_POST)) {

                $loadout = new Loadout();
                if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
                $loadout->storeFormValues($_POST);

                $lastData = $loadout::getById($_POST['loadout_id']);

                $log->info('Trying to update the loadout image...');
                $updateImg = Helpers::updateImage($lastData, $loadout, 'loadout', $_FILES['image'], $_POST['title'], true, $_POST['gameSubDirectory']);
                $update = $loadout->update();

                if ($update) {
                    $log->info('Loadout object has been updated successfully');

                    if ($updateImg) {
                        $log->info('Loadout image has been updated successfully...');
                    } else {
                        $log->warning('Loadout image could not be updated');
                    }

                    Helpers::manageRedirect('clases');
                } else {
                    $log->warning('Loadout object could not be updated');
                }


            }

        } catch (\Exception $exception) {
            $log->error('Something went wrong...', array('exception' => $exception));
            Helpers::manageRedirect('clases');
        }

        Helpers::manageRedirect('clases');
    }

    public function delete(int $id)
    {
        $log = NewLogger::newLogger('WPCAT_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');

        try {


            $loadout = new Loadout();
            if ( !$loadout::verifyConnection() ) Helpers::manageRedirect();
            $loadout->setId($id);

            $loadoutData = $loadout::getById($id, true);
            $delete = $loadout->delete();
            $deleteImg = Helpers::deleteImage( $loadoutData['image'], 'loadout', true, $loadoutData['gameName'] );

            if(!$deleteImg) $log->warning('The weapon image could not be delete.');

            if ( !$delete){
                $log->info("Weapon with id: $id do not exists");
            } else {
                $log->info("Weapon was deleted successfully.");
            }


            // Terminar delete weapon
            // Implementar en Helpers un método para eliminar imágenes.

        } catch (\Exception $exception){
            $log->error('Something went wrong while deleting the loadout', array( 'exception' => $exception ));
            Helpers::manageRedirect('clases');
        }

        Helpers::manageRedirect('clases');


    }


}