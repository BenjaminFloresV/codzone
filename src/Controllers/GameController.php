<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\ErrorsRedirecter;
use CMS\Helpers\FormVerifier;
use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\Game;
use CMS\Models\Loadout;
use CMS\Models\WeaponCategory;
use Exception;

class GameController
{
    public function index( string $gameURL )
    {
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info( 'Trying to collect index data...' );


        try {

            Helpers::verifyUriRequest();
            $shortName = DataConverter::uriToString( $gameURL );

            $game = Game::getInstance();
            $gameData = $game::getByShortName( $shortName );


            if( !$gameData ) ErrorsRedirecter::redirect404();

            $wpCat = WeaponCategory::getInstance();
            $wpCatData = $wpCat::getAll();

            $loadout = Loadout::getInstance();
            $loadoutsData = $loadout::getAllByGame( $gameData['game_id'] );

            foreach ($loadoutsData as $key=>$loadout){
                $loadoutsData[$key]['title'] = DataConverter::stringToUri($loadout['title']);
            }

            $metaDescription = "Necesitas una clase de Call of Duty ".$gameData['short_name']."? Este es el lugar indicado para ti, entra y busca la clase que necesitas sin rodeos, directo al grano. Espero que te sean de utilidad :)";
            $breadcrumbs = DataConverter::getBreadcrumbs();

            $view = __DIR__ . '/../Views/Loadout/loadout-game.phtml';
            RenderView::renderUser($view, $loadoutsData, $_SERVER['REQUEST_URI'], $wpCatData, $gameData, $breadcrumbs, "Clases para ".$gameData['short_name'], $metaDescription);
            exit();

        } catch ( Exception $exception ) {
            $log->error( 'Something went wrong while load the index view', array( 'exception' => $exception ) );

        }
    }



    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method is executing...');
        if(!empty($_POST) && FormVerifier::verifyInputs( $_POST ) ){

            try{
                $_POST = DataConverter::dateFormatter( $_POST );
                $game = Game::getInstance();
                if ( !$game::verifyConnection() ) Helpers::manageRedirect();
                $game->storeFormValues($_POST);

                $saveImg = ImageManager::saveImage($game, 'game');

                if ( $saveImg ){
                    $log->info('Image saved.');

                    $save = $game->insert();

                    if ($save){
                        $log->info('The game has been created');
                        $_SESSION['success-message'] ='Juego creado con éxito';
                    }else {
                        $_SESSION['error-message'] ='No se pudo crear el Juego';
                    }
                }else {
                    $_SESSION['error-message'] = 'Nos has subido ninguna imagen';
                    Helpers::manageRedirect('juegos/crear');
                }

            }catch (Exception $exception){
                $log->error('Something went wrong, cannot create Loadout', array('exception' => $exception));
            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('juegos/crear');
        }

        Helpers::manageRedirect('juegos');

    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');
        if (!empty($_POST) && FormVerifier::verifyInputs( $_POST ) ){

            try{
                $_POST = DataConverter::dateFormatter( $_POST );
                $game = Game::getInstance();
                if ( !$game::verifyConnection() ) Helpers::manageRedirect();
                $game->storeFormValues($_POST);
                $id = $_POST['game_id'];
                //Guardar la imagen
                $lastData = $game::getById($id);

                if( $lastData['name'] != $_POST['name'] ){
                    $oldDir = "../public/uploads/images/weapon/".$lastData['name'];
                    $newDir = "../public/uploads/images/weapon/".$_POST['name'];
                    $updateWeaponDir = Helpers::updateDirectory($oldDir, $newDir);

                    if( !$updateWeaponDir ) $log->warning('The weapon folder could not be updated');
                }


                $updateImg = ImageManager::updateImage($lastData, $game, 'game', $_FILES['image'], $_POST['name']);

                if ( $updateImg ){

                    $update = $game->update();
                    if( $update ) {
                        $_SESSION['success-message'] ='Juego actualizado con éxito';
                    } else {
                        $_SESSION['error-message'] ='No se pudo actualizar el juego';
                    }
                }else {
                    $_SESSION['error-message'] = 'No se pudo actualizar la imagen';
                }


            }catch (Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));
            }


        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
        }

        Helpers::manageRedirect("juegos/editar/".$_POST['game_id']);


    }

    public function delete( int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Delete Method was executing...');

        try {
            $game = Game::getInstance();
            if ( !$game::verifyConnection() ) Helpers::manageRedirect();
            $game->setId($id);
            $gameData = $game::getById($id);

            $deleteImg = ImageManager::deleteImage($gameData['image'], 'game');

            if ( $deleteImg ) {

                $delete =  $game->delete();
                if ( $delete ) {
                    $_SESSION['success-message'] = 'Juego eliminado con éxito';
                } else {
                    $_SESSION['error-message'] = 'No se pudo eliminar el juego';
                }

            }else {
                $log->warning('The Loadout Image could not be deleted');
            }





        } catch (Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));
        }

        Helpers::manageRedirect('juegos');
    }

}