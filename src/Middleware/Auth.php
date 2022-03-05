<?php

namespace CMS\Middleware;


class Auth
{


    public static function isAdmin()
    {
        if (!isset($_SESSION['admin'])) {
            return header('Location: http:localhost/7882/mejoresclases');
        }



    }
}