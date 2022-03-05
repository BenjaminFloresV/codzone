<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Middleware\Auth;
use CMS\Middleware\RenderView;
use CMS\Models\DeveloperCompany;
use CMS\Helpers\NewLogger;
use CMS\Models\Game;
use CMS\Models\Loadout;
use CMS\Models\Weapon;
use CMS\Models\WeaponCategory;

class AdminController
{


    public function homePage()
    {
        if(!empty($_POST)){
            if (isset($_POST['name'])){
                echo "HOli";
            }
            var_dump( $_POST );
        }else {
            $view = __DIR__ . '/../../src/Views/Admin/admin-panel.php';
            RenderView::render($view);
        }



    }

    public function manageGames( $action = null, $id = null )
    {
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Admnistrar Juegos',
            'urlPrefix' => '/admin/juegos/',
            'baseUrl' => 'game'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';

        if ( Helpers::verifySelects( $action ) ){
            $selectObjects = array(new DeveloperCompany());
            $selectsOptions = Helpers::retrieveSelectsData($selectObjects);
        }else {
            $selectsOptions = '';
        }


        $data = Helpers::retrieveObjectData($action, new Game(), $id, true);
        RenderView::render($view,true, $action,$data, $viewExtras, $selectsOptions);



    }

    public function manageCompanies($action = null, $id = null) // Al establecer una valor para el método, se soluciono el tema del parametro GET recibido
    {

        $action = Helpers::verifyAction($action);

        $viewExtras = array(
          'viewTitle' => 'Admnistrar Desarrolladoras',
          'urlPrefix' => '/admin/desarrolladoras/',
              'baseUrl' => 'company'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';

        $data = Helpers::retrieveObjectData($action, new DeveloperCompany(), $id, false);
        RenderView::render($view, true, $action, $data , $viewExtras, '');

        //al establecer un parametro por defecto en el método, este tomarlos valores de la url que debería enviar datos
        // GET pero no lo hace, al paracer con GET no es igual que POST, el controlador detecta el POST pero no el GET  como ejemplifica
        // este método pero en caso de exisitr datos por POST

    }

    public function manageWeapons( $action = null, $id = null )
    {
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Admnistrar Armas',
            'urlPrefix' => '/admin/armas/',
            'baseUrl' => 'weapon'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $weapon = new Weapon();

        if( Helpers::verifySelects( $action ) ){
            $selectObjects = array(new WeaponCategory(), new Game());
            $selectsOptions = Helpers::retrieveSelectsData($selectObjects);

        }else {
            $selectsOptions = '';
        }

        $data = Helpers::retrieveObjectData($action, $weapon, $id, true);

        RenderView::render($view, true, $action,$data, $viewExtras, $selectsOptions);

    }

    public function manageWpCategories( $action = null, $id = null )
    {
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Categorias de Armas',
            'urlPrefix' => '/admin/categorias-armas/',
            'baseUrl' => 'weaponcategory'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';


        $data = Helpers::retrieveObjectData($action, new WeaponCategory(), $id, false);
        RenderView::render($view, true, $action,$data, $viewExtras, '');

    }

    public function manageLoadouts($action = null, $id = null)
    {
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Clases',
            'urlPrefix' => '/admin/clases/',
            'baseUrl' => 'loadout'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';


        $data = Helpers::retrieveObjectData($action, new Loadout(), $id, true); //Datos de la vista read

        if( Helpers::verifySelects($action) ){
            $selectObjects = array(new WeaponCategory(), new Game(), new Weapon()); // Datos para los elementos HTML select (create, update)
            $selectOptions = Helpers::retrieveSelectsData($selectObjects);
        }else {
            $selectOptions = '';
        }

        RenderView::render($view,true, $action,$data, $viewExtras, $selectOptions);

    }

}