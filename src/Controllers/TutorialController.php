<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\FormVerifier;
use CMS\Helpers\Helpers;
use CMS\Helpers\ImageManager;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\Tutorial;
use Exception;

class TutorialController
{
    public function index( string $category, int $id )
    {

        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $tutorial = Tutorial::getInstance();


        $tutorialData = $tutorial::getById($id, true);

        if( !$tutorialData ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        //Procesar Descripcion
        $tutorialData['description'] = DataConverter::convertLoadoutInfoFormat($tutorialData['description']);

        $recommendedTutorials = $tutorial::getAll(true, 3, true, $category, false, $id);

        foreach ( $recommendedTutorials as $key=>$tutorial ){
            $tutorial['lowerCatName'] = DataConverter::stringToUri($tutorial['categoryName']);
            $tutorial['uriTitle'] = DataConverter::stringToUri( $tutorial['title'] );

            $recommendedTutorials[$key] = $tutorial;
        }

        $uri = DataConverter::stringToUri($tutorialData['title']);

        if( !strpos($_SERVER['REQUEST_URI'], $uri) ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        $view = __DIR__.'/../Views/Tutorial/tutorial.phtml';
        RenderView::renderUser($view, $recommendedTutorials, $_SERVER['REQUEST_URI'], null, $tutorialData, $breadcrumbs, $tutorialData['title'], $tutorialData['description'][0]['partZero']);

    }

    public function allTutorials( string $category = null )
    {

        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $tutorial = Tutorial::getInstance();

        if( !is_null($category) ){
            $allNews = DataConverter::subjectToLower($tutorial::getAll(true, 1, true, $category, true), 'categoryName', 'lowerCatName');
        }else{
            $allNews = DataConverter::subjectToLower($tutorial::getAll(true, 1, false, null, true), 'categoryName', 'lowerCatName');
        }


        $allCategories = DataConverter::subjectToLower($tutorial::getAllCategories(), 'name', 'lowerName');

        foreach ( $allNews as $key=>$tutorial ){
            $tutorial['uriTitle'] = DataConverter::stringToUri( $tutorial['title'] );
            $allNews[$key] = $tutorial;
        }


        $view = __DIR__.'/../Views/Tutorial/all-tutorials.phtml';
        RenderView::renderUser($view, $allNews, $_SERVER['REQUEST_URI'], $allCategories, null, $breadcrumbs, trim("Cod Zone: Tutoriales $category"), ALL_TUTORIALS_META_DESCRIPTION );

    }

    public function insert()
    {
        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert Tutorial method is executing');

        if( !empty($_POST) && FormVerifier::verifyInputs($_POST) ){
            try {
                $_POST = DataConverter::dateFormatter($_POST);
                $tutorial = Tutorial::getInstance();
                if ( !$tutorial::verifyConnection() ) Helpers::manageRedirect();
                $tutorial->storeFormValues($_POST);
                $category = $tutorial::getCategoryById($_POST['category_id']);

                $imgMethods = array('setImgTitle', 'setImgDesc','setImgFooter','setImgExtra');

                $saveImg = ImageManager::saveImgUnix('tutorial', $category['name'],$tutorial, $imgMethods);

                if( $saveImg ){

                    $saveNews = $tutorial->insert();

                    if( $saveNews ){
                        $_SESSION['success-message'] = 'Tutorial creado con éxito';
                    }else {
                        $_SESSION['error-message'] = 'No se pudo crear el tutorial';
                    }
                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                    Helpers::manageRedirect('tutoriales/crear');
                }

            } catch ( Exception $exception ){
                $log->error('Something went wrong while inserting News data.', array('exception'=>$exception));

            }
        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('tutoriales/crear');
        }

        Helpers::manageRedirect('tutoriales');
    }

    public function update()
    {

        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Update Tutorial method is executing');

        if( !empty($_POST) && FormVerifier::verifyInputs($_POST)){

            try {
                $_POST = DataConverter::dateFormatter($_POST);
                $tutorial = Tutorial::getInstance();
                if ( !$tutorial::verifyConnection() ) Helpers::manageRedirect();
                $lastTutorialInfo = $tutorial::getById($_POST['tutorial_id'], true);

                $tutorial->storeFormValues($lastTutorialInfo);
                $tutorial->storeFormValues($_POST);

                $imgMethods = array(
                    array('setImgTitle','getImgTitle',boolval($_POST['deleteTitleImg'])),
                    array('setImgDesc', 'getImgDesc',boolval($_POST['deleteDescImg'])),
                    array('setImgFooter', 'getImgFooter',boolval($_POST['deleteFooterImg'])),
                    array('setImgExtra', 'getImgExtra',boolval($_POST['deleteExtraImg']))
                );

                $category = $tutorial::getCategoryById($_POST['category_id']);

                $saveImg = ImageManager::saveImgUnix('tutorial', $category['name'],$tutorial, $imgMethods, true);

                if( $saveImg ){
                    $update = $tutorial->update();

                    if( $update ){
                        $_SESSION['success-message'] = 'Tutorial actualizado con éxito';
                    }else {
                        $_SESSION['error-message'] = 'El tutorial no se pudo actualizar';
                    }

                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                }

            } catch (Exception $exception){
                $log->error('Something went wrong while updating.', array('exception'=>$exception));

            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
        }

        Helpers::manageRedirect('tutoriales/editar/'.$_POST['tutorial_id']);

    }

    public function delete(int $id, int $images_id)
    {
        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');
        try {
            $tutorial = Tutorial::getInstance();
            if ( !$tutorial::verifyConnection() ) Helpers::manageRedirect();
            $tutorial->setTutorial_id($id);
            $tutorial->setImages_id($images_id);

            $tutorialData = $tutorial::getById($id, true);

            $imagesData = $tutorial::getAllImages($images_id);

            $deleteImg = ImageManager::deleteImage($imagesData, 'tutorial',true, $tutorialData['categoryName']);

            if( $deleteImg ) {

                $deleteNews = $tutorial->delete();

                if( $deleteNews ){
                    $_SESSION['success-message'] = 'Tutorial eliminado con éxito';
                    $log->info('News data was deleted successfully');
                }else {
                    $_SESSION['error-message'] = 'No se pudo eliminar el tutorial';
                }

            }



        } catch ( Exception $exception ){
            $log->error('Something went wrong while trying to delete tutorial data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('tutoriales');

    }



}