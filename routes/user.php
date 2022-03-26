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


SimpleRouter::get('/noticias', [\CMS\Controllers\NewsController::class, 'allNews'] );
SimpleRouter::get('/noticias/{category}', [\CMS\Controllers\NewsController::class, 'allNews'] );
SimpleRouter::get('/noticias/{category}/{id}/{title}', [\CMS\Controllers\NewsController::class, 'index'] );



SimpleRouter::get('/tutoriales', [\CMS\Controllers\TutorialController::class, 'allTutorials'] );
SimpleRouter::get('/tutoriales/{category}', [\CMS\Controllers\TutorialController::class, 'allTutorials'] );
SimpleRouter::get('/tutoriales/{category}/{id}/{title}', [\CMS\Controllers\TutorialController::class, 'index'] );


SimpleRouter::get('/jwt', function(){
    require __DIR__.'/../src/Views/User/jwt.phtml';
});





