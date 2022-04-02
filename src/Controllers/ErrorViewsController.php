<?php

namespace CMS\Controllers;

use CMS\Middleware\RenderView;

class ErrorViewsController
{
    public function notFound(){
        header("HTTP/1.0 404 Not Found");
        $view = __DIR__ . '/../../src/Views/404.php';
        RenderView::renderUser($view);

        exit();


    }

}