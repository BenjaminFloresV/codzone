<?php

namespace CMS\Helpers;

class ErrorsRedirecter
{
    public static function redirect404()
    {
        http_response_code(404);
        header("Location:".BASE_URL."/404");
        exit();
    }
}