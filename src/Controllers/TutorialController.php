<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\News;
use CMS\Models\Tutorial;

class TutorialController
{
    public function index( string $category, int $id )
    {

        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $tutorial = new Tutorial();


        $tutorialData = $tutorial::getById($id, true);

        if( !$tutorialData ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        //Procesar Descripcion
        $tutorialData['description'] = DataConverter::convertLoadoutInfoFormat( $tutorialData['description'] );

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
        RenderView::renderUser($view, $recommendedTutorials, $_SERVER['REQUEST_URI'], null, $tutorialData, $breadcrumbs, $tutorialData['title']);

    }

    public function allTutorials( string $category = null )
    {

        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $tutorial = new Tutorial();

        if( !is_null($category) ){
            $allNews = DataConverter::subjectToLower($tutorial::getAll(true, 5, true, $category), 'categoryName', 'lowerCatName');
        }else{
            $allNews = DataConverter::subjectToLower($tutorial::getAll(true, 5), 'categoryName', 'lowerCatName');
        }


        $allCategories = DataConverter::subjectToLower($tutorial::getAllCategories(), 'name', 'lowerName');

        foreach ( $allNews as $key=>$tutorial ){
            $tutorial['uriTitle'] = DataConverter::stringToUri( $tutorial['title'] );
            $allNews[$key] = $tutorial;
        }


        $view = __DIR__.'/../Views/Tutorial/all-tutorials.phtml';
        RenderView::renderUser($view, $allNews, $_SERVER['REQUEST_URI'], $allCategories, null, $breadcrumbs, trim("Cod Zone: Tutoriales $category") );

    }

    public function insert()
    {
        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert Tutorial method is executing');

        try {

            $tutorial = new Tutorial();
            if ( !$tutorial::verifyConnection() ) Helpers::manageRedirect();
            $tutorial->storeFormValues($_POST);
            $category = $tutorial::getCategoryById($_POST['category_id']);

            $imgMethods = array('setImgTitle', 'setImgDesc','setImgFooter','setImgExtra');

            $saveFiles = Helpers::saveImgUnix('tutorial', $category['name'],$tutorial, $imgMethods);

            if( $saveFiles ){

                $saveNews = $tutorial->insert();

                if( $saveNews ){
                    Helpers::manageRedirect('tutoriales');
                    exit();
                }

            }


        } catch ( \Exception $exception ){
            $log->error('Something went wrong while inserting News data.', array('exception'=>$exception));

        }

        Helpers::manageRedirect('noticias');
        exit();

    }

    public function update()
    {

        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Update Tutorial method is executing');

        try {

            $tutorial = new Tutorial();
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


            $saveFiles = Helpers::saveImgUnix('tutorial', $category['name'],$tutorial, $imgMethods, true);

            if( $saveFiles ){
                $update = $tutorial->update();

                if( $update ){

                    Helpers::manageRedirect('tutoriales');
                    exit();
                }

            }


        } catch (\Exception $exception){
            $log->error('Something went wrong while updating.', array('exception'=>$exception));

        }


        Helpers::manageRedirect('tutoriales');
        exit();

    }

    public function delete(int $id, int $images_id)
    {
        $log = NewLogger::newLogger('TUTORIAL_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');
        try {
            $tutorial = new Tutorial();
            if ( !$tutorial::verifyConnection() ) Helpers::manageRedirect();
            $tutorial->setTutorial_id($id);
            $tutorial->setImages_id($images_id);

            $tutorialData = $tutorial::getById($id, true);

            $imagesData = $tutorial::getAllImages($images_id);

            $deleteImg = Helpers::deleteImage($imagesData, 'tutorial',true, $tutorialData['categoryName']);

            if( $deleteImg ) {

                $deleteNews = $tutorial->delete();

                if( $deleteNews ){
                    $log->info('News data was deleted successfully');
                }

            }



        } catch ( \Exception $exception ){
            $log->error('Something went wrong while trying to delete tutorial data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('tutoriales');
        exit();

    }



}