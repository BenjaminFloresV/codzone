<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Middleware\Auth;
use CMS\Middleware\RenderView;
use CMS\Models\Category;
use CMS\Models\DeveloperCompany;
use CMS\Helpers\NewLogger;
use CMS\Models\Game;
use CMS\Models\Loadout;
use CMS\Models\News;
use CMS\Models\Settings;
use CMS\Models\Tutorial;
use CMS\Models\User;
use CMS\Models\Weapon;
use CMS\Models\WeaponCategory;

class AdminController
{


    public function homePage()
    {
        Helpers::isAdmin();
        $view = __DIR__ . '/../../src/Views/Admin/admin-panel.php';
        RenderView::render($view);

    }

    public function manageGames( $action = null, $id = null )
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Admnistrar Juegos',
            'urlPrefix' => '/admin/juegos/',
            'baseUrl' => 'game'
        );


        if ( Helpers::verifySelects( $action ) ){
            $selectObjects = array(DeveloperCompany::getInstance());
            $selectsOptions = Helpers::retrieveSelectsData($selectObjects);
        }

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData($action, Game::getInstance(), $id, true);
        RenderView::render($view,true, $action,$data, $viewExtras, $selectsOptions);
    }

    public function manageCompanies($action = null, $id = null) // Al establecer una valor para el método, se soluciono el tema del parametro GET recibido
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
          'viewTitle' => 'Admnistrar Desarrolladoras',
          'urlPrefix' => '/admin/desarrolladoras/',
              'baseUrl' => 'company'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData($action, DeveloperCompany::getInstance(), $id, false);
        RenderView::render($view, true, $action, $data , $viewExtras, '');

        //al establecer un parametro por defecto en el método, este tomarlos valores de la url que debería enviar datos
        // GET pero no lo hace, al paracer con GET no es igual que POST, el controlador detecta el POST pero no el GET  como ejemplifica
        // este método pero en caso de exisitr datos por POST

    }

    public function manageWeapons( $action = null, $id = null )
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Admnistrar Armas',
            'urlPrefix' => '/admin/armas/',
            'baseUrl' => 'weapon'
        );


        if( Helpers::verifySelects( $action ) ){
            $selectObjects = array(WeaponCategory::getInstance(), Game::getInstance());
            $selectsOptions = Helpers::retrieveSelectsData($selectObjects);

        }

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData($action, Weapon::getInstance(), $id, true);
        RenderView::render($view, true, $action,$data, $viewExtras, $selectsOptions);

    }

    public function manageWpCategories( $action = null, $id = null )
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Categorias de Armas',
            'urlPrefix' => '/admin/categorias-armas/',
            'baseUrl' => 'weaponcategory'
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData($action, WeaponCategory::getInstance(), $id, false);
        RenderView::render($view, true, $action,$data, $viewExtras, '');

    }

    public function manageLoadouts($action = null, $id = null)
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Clases',
            'urlPrefix' => '/admin/clases/',
            'baseUrl' => 'loadout',
            'homeOptionURI'=> array('name'=> 'Home', 'uri' => '/admin/clases/home'),
        );

        if( Helpers::verifySelects($action) ){
            if( $action == 'home' ){ //En caso de que la vista sea home, necesitaremos los valores de configuracion para las clases de tres juegos predeterminados
                $selectOptions = Helpers::retrieveSelectsData(array( Game::getInstance()), false, ['lastHomeLoadouts']); // Datos para los elementos HTML select (create, update)
                $selectOptions['Settings']['lastHomeLoadouts']['value'] = explode(',', $selectOptions['Settings']['lastHomeLoadouts']['value']);
            }
            else {
                $selectObjects = array(WeaponCategory::getInstance(), Game::getInstance(), Weapon::getInstance()); // Datos para los elementos HTML select (create, update)
                $selectOptions = Helpers::retrieveSelectsData($selectObjects);
            }

        }else {
            if( $action == 'read'){
                $selectObjects = array(Game::getInstance(), WeaponCategory::getInstance());
                $selectOptions = Helpers::retrieveSelectsData($selectObjects);
            }
        }


        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData( $action, Loadout::getInstance(), $id, true);
        RenderView::render($view,true, $action,$data, $viewExtras, $selectOptions);

    }

    public function manageNews( $action = null, $id = null )
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Noticias',
            'urlPrefix' => '/admin/noticias/',
            'baseUrl' => 'news',
            'newsOptionURI'=> array('name'=> 'Ver Categorias', 'uri' => '/admin/categorias/')

        );

        if( Helpers::verifySelects($action) ){
            $selectObjects = array(News::getInstance()); // Datos para los elementos HTML select (create, update)
            $selectOptions = Helpers::retrieveSelectsData($selectObjects, true);
        }else {
            if( $action == 'read'){
                $category = Category::getInstance();
                $selectOptions = $category::getAllCategories();
            }
        }

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData( $action, News::getInstance(), $id, true); //Datos del objeto
        RenderView::render($view,true, $action,$data, $viewExtras, $selectOptions);

    }

    public function manageCategories($action = null, $id = null)
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Noticias',
            'urlPrefix' => '/admin/categorias/',
            'baseUrl' => 'news-categories',
            'newsOptionURI'=> array('name'=> 'Ir a Noticias', 'uri' => '/admin/noticias/'),
            'tutorialOptionURI'=> array('name'=> 'Ir a Tutoriales', 'uri' => '/admin/tutoriales/')

        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        $data = Helpers::retrieveObjectData( $action, News::getInstance(), $id, false, true); //Datos de la vista read
        RenderView::render($view,true, $action,$data, $viewExtras, '');

    }

    public function manageTutorials($action = null, $id = null)
    {
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Tutoriales',
            'urlPrefix' => '/admin/tutoriales/',
            'baseUrl' => 'tutorial',
            'newsOptionURI'=> array('name'=> 'Ver Categorias', 'uri' => '/admin/categorias/')

        );

        if( Helpers::verifySelects($action) ){
            $selectObjects = array(Tutorial::getInstance()); // Datos para los elementos HTML select (create, update)
            $selectOptions = Helpers::retrieveSelectsData($selectObjects, true);
        }else {
            if( $action == 'read' ){
                $category = Category::getInstance();
                $selectOptions = $category::getAllCategories();
            }
        }

        $data = Helpers::retrieveObjectData( $action, Tutorial::getInstance(), $id, true, false); //Datos de la vista read
        $view = __DIR__ . '/../../src/Views/Admin/administration.php';
        RenderView::render($view,true, $action,$data, $viewExtras, $selectOptions);

    }


    public function login()
    {
        if( isset($_SESSION['admin'])  ){
            // logic
            Helpers::manageRedirect();
        }else if( !empty($_POST) ){
            // logic
            $user = new User();
            $user->storeFormValues($_POST);

            $userData = $user->login();

            if( $userData !== false ){
                $isAdmin = boolval($userData['isAdmin']);
                if( $isAdmin ){
                    $_SESSION['admin'] = $userData;
                    Helpers::manageRedirect();
                }
            }else {
                $_SESSION['error-message'] = 'El usuario y/o la contraseña no son correctas';
                header("Location:".BASE_URL.'/admin-login');
                exit();
            }

        }else {
            $view = __DIR__.'/../../src/Views/Admin/login.phtml';
            RenderView::render($view);
        }
    }

    public function logout()
    {
        User::logout();
    }

}