<?php

namespace CMS\Auth;

class JWT
{
    /*
     * Headers for JWT
     *
     * @var array
     *
     * */
    private $headers;

    /*
     * Headers for JWT
     *
     * @var string
     *
     * */
    private $secret;

    public function __construct()
    {
        $this->headers = [
            'alg' => 'HS256',
            'type' => 'JWT'
        ];

        $this->secret = 'thisIsASecret';
    }


    /**
     *
     * Generate JWT using a payload
     *
     * @param array $payload
     * @return string
     */
    public function generate( array $payload ): string
    {
        $headers = $this->encode(json_encode($this->headers));
        $payload['exp'] = time() + 60;
        $payload = $this->encode(json_encode($payload));
        $signature = hash_hmac('SHA256', "$headers.$payload", $this->secret, true);
        $signature = $this->encode($signature);
        return "$headers.$payload.$signature";
    }

    /**
     * Encode JWT using base 64
     *
     * @param string $str
     * @return string
     */

    private function encode( string $str ) : string
    {
        return rtrim(strtr(base64_encode($str), '+/','-_'),'=');
    }

    public function is_valid( string $jwt ) : bool
    {
        $token = explode('.', $jwt);
        if(!isset($token[1]) && !isset($token[2]) ) {
            return false;
        }

        $headers = base64_decode($token[0]);
        $payload = base64_decode($token[1]);
        $clientSignature = $token[2];

        if ( !json_decode($payload) ) {
            return false;
        }

        if( (json_decode($payload)->exp - time() ) < 0 ){
            return false;
        }

        $base64_header = $this->encode($headers);
        $base64_payload = $this->encode($payload);

        $signature = hash_hmac('SHA256', "$base64_header.$base64_payload", $this->secret, true);
        $base64_signature = $this->encode($signature);

        return ( $base64_signature === $clientSignature );
    }

}