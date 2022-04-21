<?php

namespace CMS\Helpers;

class FormVerifier
{

    public static function verifyString( $data ): bool
    {
        $result = false;
        if ( !intval( $data ) && !empty( $data ) ) $result = true;
        return $result;
    }

    public static function verifyInt( $data ): bool
    {
        $result = false;
        if (  intval( $data ) && !empty( $data ) ) $result = true;
        return $result;
    }

    public static function verifyDate( $data ): bool
    {
        $result = true;
        if( empty( $data ) || !preg_match('/\d+\/\d+\/\d+/', $data ) || !preg_match('/\d+-\d+-\d+/', $data ) ) $result = false;
        return $result;

    }

    public static function verifyBoolean( $data ):bool {
        $result = false;
        if( $data === '0' || $data === '1' ) $result = true;
        return $result;
    }

    // This method verifies if all inputs are valid
    public static function verifyInputs( array $data ): bool
    {
        $result = false;

        foreach ( $data as $input) {
            // We use all the methods above to verify the type of input, if any condition doesn't match that means that the input is invalid
            if( self::verifyString($input) || self::verifyInt( $input ) || self::verifyDate( $input ) || self::verifyBoolean($input) ) {
                $result = true;
            }else {
                $result = false;
                break;
            }
        }
        return $result;
    }

}