<?php

namespace CMS\Middleware;

use JetBrains\PhpStorm\NoReturn;

class RenderView
{
    #[NoReturn] public static function render($view = '', bool $isAdmin = true , $action = '', $data = '', $viewExtras = '', $selectsData = '')
    {
        if ( $isAdmin ){

            if ( $data !='' ){
                $allData = $data;
            }
            $viewAction = $action;

            if ( $viewExtras != '' ){
                $viewTitle = $viewExtras['viewTitle'];
                $urlPrefix = $viewExtras['urlPrefix'];
                $baseUrl = $viewExtras['baseUrl'];
                if( isset($viewExtras['newsOptionURI']) !== null ){
                    $newsView = $viewExtras['newsOptionURI'];
                }

                if( isset($viewExtras['tutorialOptionURI']) !== null ){
                    $tutorialView = $viewExtras['tutorialOptionURI'];
                }

                if( isset($viewExtras['homeOptionURI']) !== null ) {
                    $homeView = $viewExtras['homeOptionURI'];
                }


            }

            if ($selectsData != '' ){
                $selects = $selectsData;
            }

            require_once __DIR__. '/../../src/Views/layouts/header.phtml';
            require_once $view;
            require_once __DIR__. '/../../src/Views/layouts/footer.phtml';
            exit(); // Esto lo hice porque el código se ejecutaba dos veces ( no sé por qué xd )
        }


    }

    public static function renderHome($view = null, $lastNews = null, $someNews = null, $lastTutorial = null, $someTutorials = null, $loadouts = null, $randomLoadouts = null)
    {
        $metaDescription = 'Bienvenido a CodZone, aquí encontrarás información relacionada a Call of Duty. Tenemos variedad de tutoriales, noticias y las mejores clases para que puedas tener una experienca de juego mucho más divertida en Call of Duty.';

        require_once __DIR__ . '/../../src/Views/User/layouts/header.phtml';
        require_once $view;
        require_once __DIR__. '/../../src/Views/layouts/footer.phtml';
        exit();

    }

    public static function renderUser($view, $data = null, $uri = null, $categoriesData = null, $mainObjectData = null, $breadcrumbs = null, $pageTitle = null, $metaDescription = null)
    {
        if ( $data != null ){
            $allData = $data;
        }

        if ( $uri !=null ){
            $finalUri = $uri;
        }

        if ( $categoriesData != null ){
            $catsData = $categoriesData;
        }

        if ( $mainObjectData != null ) {
            $objectData = $mainObjectData;
        }

        if( $breadcrumbs != null ){
            $crumbs = $breadcrumbs;
        }

        if ( $pageTitle != null ){
            $titlePage = $pageTitle;
        }

        require_once __DIR__ . '/../../src/Views/User/layouts/header.phtml';
        require_once $view;
        require_once __DIR__. '/../../src/Views/layouts/footer.phtml';
        exit();
    }
}