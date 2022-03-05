<?php

use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;


SimpleRouter::get('/404', [\CMS\Controllers\ErrorViewsController::class, 'notFound']);



SimpleRouter::error(function(Request $request, \Exception $exception) {

    switch($exception->getCode()) {
        // Page not found
        case 404:
            response()->redirect('/404');
        // Forbidden
        case 403:
            response()->redirect('/404');

        case 302:
            response()->redirect('/404');
    }

});
