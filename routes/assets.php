<?php


use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::get('/css/mystyles', function (){
    include __DIR__.'/../assets/css/css.php';
});


SimpleRouter::get('/js/bulmacalendar', function(){
    include __DIR__.'/../assets/js/bulmacalendar.php';
});

SimpleRouter::get('/js/modal', function (){
    include __DIR__ . '/../assets/js/modal.php';
});

SimpleRouter::get('/js/loadout-selects', function (){
    include __DIR__ . '/../assets/js/loadout-selects.php';
});

SimpleRouter::get('/js/loadout-selects-insert', function (){
    include __DIR__ . '/../assets/js/loadout-selects-insert.php';
});


SimpleRouter::get('/js/weapons', function (){
    include __DIR__ . '/../assets/js/weapons.php';
});

SimpleRouter::get('/js/navigation', function (){
    include __DIR__ . '/../assets/js/navigation.php';
});


SimpleRouter::get('/js/game-view-modals', function (){
    include __DIR__ . '/../assets/js/game-view-modals.php';
});


SimpleRouter::get('/js/game-loadout-items', function (){
    include __DIR__ . '/../assets/js/game-loadout-items.php';
});

SimpleRouter::get('/js/news-image-input', function (){
    include __DIR__ . '/../assets/js/news-image-input.php';
});

SimpleRouter::get('/js/news-img-checkbox', function (){
    include __DIR__ . '/../assets/js/news-img-checkbox.php';
});

SimpleRouter::get('/js/main-scroll', function (){
    include __DIR__ . '/../assets/js/main-scroll.php';
});


SimpleRouter::get('/js/key-script', function(){
    include __DIR__ . '/../assets/js/key-script.php';
});

SimpleRouter::get('/js/search', function(){
    include __DIR__ . '/../assets/js/search/search.php';
});




