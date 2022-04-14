<?php

namespace CMS\Models\Singleton;

abstract class Singleton
{

    public static function getInstance()
    {

        $calledClass = get_called_class();

        if( $_SESSION['SINGLETON'] === null ) {
            $_SESSION['SINGLETON'] = array();
        }

        if(  !is_array( unserialize($_SESSION['SINGLETON'][$calledClass]) ) ) {
            $class = new $calledClass();
            $_SESSION['SINGLETON'][$calledClass] = serialize($class);
        }



        return unserialize($_SESSION['SINGLETON'][$calledClass]);
    }

}

