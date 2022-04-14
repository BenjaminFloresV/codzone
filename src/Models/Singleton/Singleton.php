<?php

namespace CMS\Models\Singleton;

abstract class Singleton
{
    private static array $instances = array();

    public static function getInstance()
    {
        $calledClass = get_called_class();
        if( !isset( self::$instances[$calledClass] ) ) {
            self::$instances[$calledClass] = new $calledClass();
        }

        return self::$instances[$calledClass];
    }
}

