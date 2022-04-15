<?php

namespace CMS\Controllers;

use CMS\Helpers\Helpers;
use CMS\Models\Settings;

class SettingsController
{
    public function updateHomeLoadoutsCSV()
    {
        Helpers::isAdmin();
        if( !empty($_POST) ){

            $settings = Settings::getInstance();
            $value = $_POST['firstGame'].','.$_POST['secondGame'].','.$_POST['thirdGame'];

            $update = $settings->updateSetting($_POST['settingId'], $value);

            if( $update ) {
                $_SESSION['success-message'] = "Datos Actualizados con éxito";
            }else {
                $_SESSION['error-message'] = "No se pudieron actualizar los datos";
            }

        }
        Helpers::manageRedirect('clases/home');
    }
}