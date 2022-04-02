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
        if(!empty($_POST)){
            if (isset($_POST['name'])){
                echo "HOli";
            }
            var_dump( $_POST );
        }else {
            Helpers::isAdmin();
            $view = __DIR__ . '/../../src/Views/Admin/admin-panel.php';
            RenderView::render($view);
        }



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
        Helpers::isAdmin();
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
        Helpers::isAdmin();
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
        Helpers::isAdmin();
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
        Helpers::isAdmin();
        $action = Helpers::verifyAction($action);

        $viewExtras = array(
            'viewTitle' => 'Administrar Clases',
            'urlPrefix' => '/admin/clases/',
            'baseUrl' => 'loadout',
            'homeOptionURI'=> array('name'=> 'Home', 'uri' => '/admin/clases/home'),
        );

        $view = __DIR__ . '/../../src/Views/Admin/administration.php';

        $loadout = new Loadout();
        //Datos de la vista read
        if( !empty($_GET)){
            $data = $loadout->getAllFiltered($_GET);
        } else{
            $data = $loadout::getAll(true, 20,true);
        }

        if( Helpers::verifySelects($action) ){
            if( $action == 'home' ){ //En caso de que la vista sea home, necesitaremos los valores de configuracion para las clases de tres juegos predeterminados
                $selectOptions = Helpers::retrieveSelectsData(array( new Game()), false, ['lastHomeLoadouts']); // Datos para los elementos HTML select (create, update)
                $selectOptions['Settings']['lastHomeLoadouts']['value'] = explode(',', $selectOptions['Settings']['lastHomeLoadouts']['value']);
            }
            else {
                $selectObjects = array(new WeaponCategory(), new Game(), new Weapon()); // Datos para los elementos HTML select (create, update)
                $selectOptions = Helpers::retrieveSelectsData($selectObjects);
            }

        }else {
            if( $action == 'read'){
                $selectObjects = array(new Game(), new WeaponCategory());
                $selectOptions = Helpers::retrieveSelectsData($selectObjects);
            }
        }

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


        $view = __DIR__ . '/../../src/Views/Admin/administration.php';

        if( !empty($_GET) ){
            $news = new News();
            $data = $news->getAllFiltered($_GET);
        }else {
            $data = Helpers::retrieveObjectData( $action, new News(), $id, true); //Datos de la vista read
        }


        if( Helpers::verifySelects($action) ){
            $selectObjects = array(new News()); // Datos para los elementos HTML select (create, update)
            $selectOptions = Helpers::retrieveSelectsData($selectObjects, true);
        }else {
            if( $action == 'read'){
                $category = new Category();
                $selectOptions = $category::getAllCategories();
            }
        }

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

        $data = Helpers::retrieveObjectData( $action, new News(), $id, false, true); //Datos de la vista read

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

        if( !empty($_GET) ){
            $tutorial = new Tutorial();
            $data = $tutorial->getAllFiltered($_GET);
        }else {
            $data = Helpers::retrieveObjectData( $action, new Tutorial(), $id, true, false); //Datos de la vista read
        }


        if( Helpers::verifySelects($action) ){
            $selectObjects = array(new Tutorial()); // Datos para los elementos HTML select (create, update)
            $selectOptions = Helpers::retrieveSelectsData($selectObjects, true);
        }else {
            if( $action == 'read' ){
                $category = new Category();
                $selectOptions = $category::getAllCategories();
            }
        }


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