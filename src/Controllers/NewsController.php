<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Helpers\Helpers;
use CMS\Helpers\NewLogger;
use CMS\Middleware\RenderView;
use CMS\Models\Loadout;
use CMS\Models\News;

class NewsController
{
    public function index( string $category, int $id )
    {


        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $news = new News();

        $newsData = $news::getById($id, true);

        if( !$newsData ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        //Procesar Descripcion
        $newsData['description'] = DataConverter::convertLoadoutInfoFormat( $newsData['description'] );

        $recommendedNews = $news::getAll(true, 3, true, $category, false, $id);

        foreach ( $recommendedNews as $key=>$news ){
            $news['lowerCatName'] = DataConverter::stringToUri($news['categoryName']);
            $news['uriTitle'] = DataConverter::stringToUri( $news['title'] );

            $recommendedNews[$key] = $news;
        }


        $uri = DataConverter::stringToUri($newsData['title']);

        if( !strpos($_SERVER['REQUEST_URI'], $uri) ){
            header("Location:".BASE_URL."/404");
            exit();
        }

        $view = __DIR__.'/../Views/News/news.phtml';
        RenderView::renderUser($view, $recommendedNews, $_SERVER['REQUEST_URI'], null, $newsData, $breadcrumbs, $newsData['title']);

    }


    public function allNews( string $category = null)
    {
        Helpers::verifyUriRequest();
        $breadcrumbs = DataConverter::getBreadcrumbs();
        $news = new News();

        if( !is_null($category) ){
            $allNews = DataConverter::subjectToLower($news::getAll(true, 1, true, $category, true), 'categoryName', 'lowerCatName');
        }else{
            $allNews = DataConverter::subjectToLower($news::getAll(true, 1, false, null, true), 'categoryName', 'lowerCatName');
        }


        $allCategories = DataConverter::subjectToLower($news::getAllCategories(), 'name', 'lowerName');

        foreach ( $allNews as $key=>$news ){
            $news['uriTitle'] = DataConverter::stringToUri( $news['title'] );
            $allNews[$key] = $news;
        }


        $view = __DIR__.'/../Views/News/all-news.phtml';
        RenderView::renderUser($view, $allNews, $_SERVER['REQUEST_URI'], $allCategories, null, $breadcrumbs, trim("Cod Zone: Noticias $category") );

    }


    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert News method is executing');

        try {

            $news = new News();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->storeFormValues($_POST);
            $category = $news::getCategoryById($_POST['category_id']);

            $imgMethods = array('setImgTitle', 'setImgDesc','setImgFooter','setImgExtra');

            $saveFiles = Helpers::saveImgUnix('news', $category['name'],$news, $imgMethods);

            if( $saveFiles ){

                $saveNews = $news->insert();

                if( $saveNews ){
                    Helpers::manageRedirect('noticias');
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
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Update News method is executing');

        try {

            $news = new News();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $lastNewsInfo = $news::getById($_POST['news_id'], true);

            $news->storeFormValues($lastNewsInfo);
            $news->storeFormValues($_POST);

            $imgMethods = array(
                array('setImgTitle','getImgTitle',boolval($_POST['deleteTitleImg'])),
                array('setImgDesc', 'getImgDesc',boolval($_POST['deleteDescImg'])),
                array('setImgFooter', 'getImgFooter',boolval($_POST['deleteFooterImg'])),
                array('setImgExtra', 'getImgExtra',boolval($_POST['deleteExtraImg']))
            );

            $category = $news::getCategoryById($_POST['category_id']);


            $saveFiles = Helpers::saveImgUnix('news', $category['name'],$news, $imgMethods, true);

            if( $saveFiles ){
                $update = $news->update();

                if( $update ){

                    Helpers::manageRedirect('noticias');
                    exit();
                }

            }


        } catch (\Exception $exception){
            $log->error('Something went wrong while updating.', array('exception'=>$exception));

        }


        Helpers::manageRedirect('noticias');
        exit();

    }


    public function insertCategory()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('InsertCategory method is executing');

        try {
            $news = new News();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->storeFormValues($_POST);

            $saveCategory = $news->insertCategory();

            if( $saveCategory){
                $log->info('The News Category was created successfully');
                Helpers::manageRedirect('categorias');
            }

            Helpers::manageRedirect('categorias');

        } catch (\Exception $exception){
            $log->error('Something went wrong while saving the News Category', array('exception' => $exception));
        }

        Helpers::manageRedirect('categorias');
    }

    public function delete( int $id, int $images_id ){
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');
        try {
            $news = new News();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->setNews_id($id);
            $news->setImages_id($images_id);

            $newsData = $news::getById($id, true);

            $imagesData = $news::getAllImages($images_id);

            $deleteImg = Helpers::deleteImage($imagesData, 'news',true, $newsData['categoryName']);

            if( $deleteImg ) {

                $deleteNews = $news->delete();

                if( $deleteNews ){
                    $log->info('News data was deleted successfully');
                }

            }



        } catch ( \Exception $exception ){
            $log->error('Somethin went wrong whilte trying to elimnate the news data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('noticias');

    }

    public function updateCategory()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Update Category was executing');
        try {
            $news = new News();
            if( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->storeFormValues($_POST);

            $updateCategory = $news->updateCategory();

            if( !$updateCategory ){
                $log->warning("The News Category with id: {$news->getCategoryId()} do not exists");
                Helpers::manageRedirect('categorias');
            }


        } catch ( \Exception $exception){
            $log->error('Something went wrong while updating the category', array('exception' => $exception));

        }

        Helpers::manageRedirect("categorias/editar/{$news->getCategoryId()}");


    }

    public function deleteCategory( int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete Category method is executing...');
        try {
            $news = new News();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->setNewsCategory($id);

            $deleteNews = $news->deleteCategory();

            if( $deleteNews ){
                $log->info('News data was deleted successfully');
            }



        } catch ( \Exception $exception ){
            $log->error('Something went wrong while trying to eliminate the news data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('categorias-noticias');

    }



}