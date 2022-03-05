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

    public static function renderUser($view, $data = null, $uri = null, $weaponCatsData = null, $mainObjectData = null, $breadcrumbs = null)
    {
        if ( $data != null ){
            $allData = $data;
        }

        if ( $uri !=null ){
            $finalUri = $uri;
        }

        if ( $weaponCatsData != null ){
            $wpCats = $weaponCatsData;
        }

        if ( $mainObjectData != null ) {
            $objectData = $mainObjectData;
        }

        if( $breadcrumbs != null ){
            $crumbs = $breadcrumbs;
        }

        require_once __DIR__. '/../../src/Views/User/layouts/header.php';
        require_once $view;
        require_once __DIR__. '/../../src/Views/layouts/footer.phtml';
        exit();
    }
}