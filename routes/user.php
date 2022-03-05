<?php


use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::get('/', [\CMS\Controllers\UserController::class, 'homePage']);

SimpleRouter::get('/juegos/{id}/{nombre}/', function ($id, $name){
    echo $id;
    echo $name;
});

SimpleRouter::get('/clases', [\CMS\Controllers\LoadoutController::class, 'allLoadouts']);

SimpleRouter::get('/clases/{juego}/', [\CMS\Controllers\GameController::class, 'index' ] );

SimpleRouter::get('/clases/{juego}/{id}/{name}', [\CMS\Controllers\LoadoutController::class, 'index']);


SimpleRouter::get('/noticias/', [\CMS\Controllers\LoadoutController::class, 'index'] );
SimpleRouter::get('/noticias/{id}/{title}', [\CMS\Controllers\LoadoutController::class, 'index'] );






