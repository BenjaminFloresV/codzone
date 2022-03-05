<?php

namespace CMS\Controllers;

use CMS\Helpers\DataConverter;
use CMS\Middleware\RenderView;
use CMS\Models\Loadout;

class UserController
{
    public function homePage()
    {
        $loadout1 = new Loadout();
        $loadout1_data = $loadout1::getLastByGame(5, true);

        $newData = DataConverter::addGameURL($loadout1_data);


        $view = __DIR__.'/../../src/Views/User/home.phtml';
        RenderView::renderUser($view, $newData);

    }



}