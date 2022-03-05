<?php

namespace CMS\Controllers;

use CMS\Middleware\RenderView;

class ErrorViewsController
{
    public function notFound(){

        $view = __DIR__ . '/../../src/Views/404.php';
        RenderView::renderUser($view);

        exit();


    }

}