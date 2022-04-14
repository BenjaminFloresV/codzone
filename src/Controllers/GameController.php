<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\Game;
use CMS\Models\Loadout;
use CMS\Models\WeaponCategory;

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


            if( !$gameData ){
                header("Location:".BASE_URL."/404");
                exit();
            }

            $wpCat = WeaponCategory::getInstance();
            $wpCatData = $wpCat::getAll();

            $loadout = Loadout::getInstance();
            $loadoutsData = $loadout::getAllByGame( $gameData['game_id'] );

            foreach ($loadoutsData as $key=>$loadout){
                $loadoutsData[$key]['title'] = DataConverter::stringToUri($loadout['title']);
            }


            $breadcrumbs = DataConverter::getBreadcrumbs();

            $view = __DIR__ . '/../Views/Loadout/loadout-game.phtml';
            RenderView::renderUser($view, $loadoutsData, $_SERVER['REQUEST_URI'], $wpCatData, $gameData, $breadcrumbs, "Clases para ".$gameData['short_name']);
            exit();

        } catch ( \Exception $exception ) {
            $log->error( 'Something went wrong while load the index view', array( 'exception' => $exception ) );

        }
    }



    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Insert Method is executing...');
        if(!empty($_POST)){

            try{
                $game = Game::getInstance();
                if ( !$game::verifyConnection() ) Helpers::manageRedirect();
                $game->storeFormValues($_POST);

                $saveFile = Helpers::saveFile($game, 'game');

                if ( $saveFile ){
                    $log->info('Image saved.');

                    $save = $game->insert();

                    if ($save){
                        $log->info('The game has been created');


                        Helpers::manageRedirect('juegos');
                    }
                }

                Helpers::manageRedirect('juegos');


            }catch (\Exception $exception){
                $log->error('Something went wrong, cannot create Loadout', array('exception' => $exception));
                Helpers::manageRedirect('juegos');
            }

        }else {
            Helpers::manageRedirect('juegos');
        }

    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('GAME_CONTROLLER','FirePHPHandler');
        $log->info('Update Method was executing...');
        if (!empty($_POST)){

            try{

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


                $updateImg = Helpers::updateImage($lastData, $game, 'game', $_FILES['image'], $_POST['name']);

                $update = $game->update();

                if ( $update && $updateImg ){
                    Helpers::manageRedirect('juegos');
                }


            }catch (\Exception $exception){
                $log->error('Something went wrong', array( 'exception' => $exception ));
                header("Location: ".BASE_URL."/admin/juegos/editar/".$id);
                exit();
            }


        }

        Helpers::manageRedirect('juegos');


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

            $deleteImg = Helpers::deleteImage($gameData['image'], 'game');
            $delete =  $game->delete();

            if ( !$deleteImg ) $log->warning('The Loadout Image could not be deleted');

            if ( !$delete ){
                $log->info("Company with id: $id do not exists");


            } else {

                $log->info("Company was deleted successfully.");
            }
            Helpers::manageRedirect('juegos');


        } catch (\Exception $exception){
            $log->error('Something went wrong', array('exception' => $exception));
            Helpers::manageRedirect('juegos');
        }

    }

}