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
use CMS\Models\News;
use Exception;

class NewsController
{
    public function index( $category, $id )
    {
        Helpers::verifyUriRequest();
        try {

            $breadcrumbs = DataConverter::getBreadcrumbs();
            $news = News::getInstance();

            if( !is_numeric( $id ) ) ErrorsRedirecter::redirect404();
            $newsData = $news::getById($id, true);

            if( !$newsData ) ErrorsRedirecter::redirect404();
            if( $newsData['categoryName'] !== DataConverter::uriToString($category) ) ErrorsRedirecter::redirect404();
            $uri = DataConverter::stringToUri($newsData['title']);
            UriVerifier::verifyLastUriString($uri);

            $newsData['description'] = DataConverter::explodeContent($newsData['description']);
            $recommendedNews = $news::getAll(true, 3, true, $category, false, $id);

            foreach ( $recommendedNews as $key=>$news ){
                $news['lowerCatName'] = DataConverter::stringToUri($news['categoryName']);
                $news['uriTitle'] = DataConverter::stringToUri( $news['title'] );

                $recommendedNews[$key] = $news;
            }

            Helpers::deleteSession('maintenance');
            $view = __DIR__.'/../Views/News/news.phtml';
            RenderView::renderUser($view, $recommendedNews, $_SERVER['REQUEST_URI'], null, $newsData, $breadcrumbs, $newsData['title'], $newsData['description'][0]['partZero']);

        } catch ( Exception $exception){
            $_SESSION['maintenance'] = true;
            RenderView::renderMaintenance();
        }
    }


    public function allNews( string $category = null)
    {
        try {

            Helpers::verifyUriRequest();
            $breadcrumbs = DataConverter::getBreadcrumbs();
            $news = News::getInstance();

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

            Helpers::deleteSession('maintenance');
            $view = __DIR__.'/../Views/News/all-news.phtml';
            RenderView::renderUser($view, $allNews, $_SERVER['REQUEST_URI'], $allCategories, null, $breadcrumbs, trim("Cod Zone: Noticias $category"), ALL_NEWS_META_DESCRIPTION );


        } catch ( Exception $exception) {
            $_SESSION['maintenance'] = true;
            RenderView::renderMaintenance();
        }
    }


    public function insert()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Insert News method is executing');

        if( !empty($_POST) && FormVerifier::verifyInputs($_POST) ) {

            try {
                $_POST = DataConverter::dateFormatter($_POST);
                $news = News::getInstance();
                if ( !$news::verifyConnection() ) Helpers::manageRedirect();
                $news->storeFormValues($_POST);

                $category = $news::getCategoryById($_POST['category_id']);

                $imgMethods = array('setImgTitle', 'setImgDesc','setImgFooter','setImgExtra');


                $saveImg = ImageManager::saveImgUnix('news', $category['name'],$news, $imgMethods);

                if( $saveImg ){

                    $saveNews = $news->insert();

                    if( $saveNews ){
                        $_SESSION['success-message'] = 'Noticia creada con éxito';

                    }else {
                        $_SESSION['error-message'] = 'No se pudo crear la noticia';
                    }

                }else {
                    $_SESSION['error-message'] = 'No se pudo guardar la imagen';
                    Helpers::manageRedirect('noticias/crear');
                }


            } catch ( Exception $exception ){
                $log->error('Something went wrong while inserting News data.', array('exception'=>$exception));

            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('noticias/crear');
        }

        Helpers::manageRedirect('noticias');


    }

    public function update()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Update News method is executing');


        if( !empty($_POST) && FormVerifier::verifyInputs( $_POST ) ){
            try {
                $_POST = DataConverter::dateFormatter($_POST);
                $news = News::getInstance();
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


                $saveImg = ImageManager::saveImgUnix('news', $category['name'],$news, $imgMethods, true);

                if( $saveImg ){
                    $update = $news->update();

                    if( $update ){

                        $_SESSION['success-message'] = 'Noticia actualizada con éxito';
                    } else {
                        $_SESSION['error-message'] = 'No se pudo actualizar la noticia';
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

        Helpers::manageRedirect('noticias/editar/'.$_POST['news_id']);

    }


    public function insertCategory()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('InsertCategory method is executing');
        if( !empty($_POST) && FormVerifier::verifyInputs($_POST)){

            try {
                $news = News::getInstance();
                if ( !$news::verifyConnection() ) Helpers::manageRedirect();
                $news->storeFormValues($_POST);

                $saveCategory = $news->insertCategory();

                if( $saveCategory){
                    $log->info('The News Category was created successfully');
                    $_SESSION['success-message'] = 'Categoria creada con éxito';
                }else {
                    $_SESSION['error-message'] = 'No se pudo crear la categoría';
                }

            } catch (Exception $exception){
                $log->error('Something went wrong while saving the News Category', array('exception' => $exception));
            }


        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
            Helpers::manageRedirect('categorias/crear');
        }

        Helpers::manageRedirect('categorias');
    }

    public function delete( int $id, int $images_id ){
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete method is executing...');
        try {
            $news = News::getInstance();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->setNews_id($id);
            $news->setImages_id($images_id);

            $newsData = $news::getById($id, true);

            $imagesData = $news::getAllImages($images_id);

            $deleteImg = ImageManager::deleteImage($imagesData, 'news',true, $newsData['categoryName']);


            if( $deleteImg ) {

                $deleteNews = $news->delete();

                if( $deleteNews ){
                    $log->info('News data was deleted successfully');
                    $_SESSION['success-message'] = 'Noticia eliminada con éxito';
                }else {
                    $_SESSION['error-message'] = 'No se pudo eliminar la notiica';
                }

            }



        } catch ( Exception $exception ){
            $log->error('Somethin went wrong whilte trying to elimnate the news data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('noticias');

    }

    public function updateCategory()
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Update Category was executing');
        if( !empty($_POST) && FormVerifier::verifyInputs($_POST)) {

            try {
                $news = News::getInstance();
                if( !$news::verifyConnection() ) Helpers::manageRedirect();
                $news->storeFormValues($_POST);

                $updateCategory = $news->updateCategory();

                if( !$updateCategory ){
                    $log->warning("The News Category with id: {$news->getCategoryId()} do not exists");
                    $_SESSION['error-message'] = 'No se pudo actualizar la categoría';
                }else {
                    $_SESSION['success-message'] = 'Categoría actualizada con éxito';
                }


            } catch ( Exception $exception){
                $log->error('Something went wrong while updating the category', array('exception' => $exception));

            }

        }else {
            $_SESSION['error-message'] = 'Todos los campos son requeridos';
        }


        Helpers::manageRedirect("categorias/editar/".$_POST['category_id']);


    }

    public function deleteCategory( int $id)
    {
        Helpers::isAdmin();
        $log = NewLogger::newLogger('NEWS_CONTROLLER', 'FirePHPHandler');
        $log->info('Delete Category method is executing...');
        try {
            $news = News::getInstance();
            if ( !$news::verifyConnection() ) Helpers::manageRedirect();
            $news->setNewsCategory($id);

            $deleteCategory = $news->deleteCategory();

            if( $deleteCategory ){
                $log->info('News data was deleted successfully');
                $_SESSION['success-message'] = 'Categoría eliminada con éxito';
            }else {
                $_SESSION['error-message'] = 'No se pudo eliminar la categoría';
            }



        } catch ( Exception $exception ){
            $log->error('Something went wrong while trying to eliminate the news data', array('exception' => $exception ));

        }

        Helpers::manageRedirect('categorias-noticias');

    }



}