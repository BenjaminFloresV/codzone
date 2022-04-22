<?php

namespace CMS\Helpers;

class UriVerifier
{
    public static function verifyLastUriString( string $string )
    {

        $uri = explode('/', $_SERVER['REQUEST_URI']);

        if( end($uri) !== $string  ) ErrorsRedirecter::redirect404();
    }

}