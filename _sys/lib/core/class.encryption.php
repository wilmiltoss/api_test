<?php

class Encryption {

    private $default_key;
    private $method = 'AES-256-CBC';

    function __construct() {
        //$this->default_key = config()['authToken'];
        $this->default_key = "R6sdfh83GUSg34i8";
    }

    public static function Encrypt($value, $key = null) {

        $obj = new self();

        if (is_null($key)) {
            $key = $obj->default_key;
        }

        return @openssl_encrypt($value, $obj->method, $key);
    }

    public static function Decrypt($value, $key = null) {

        $obj = new self();

        if (is_null($key)) {
            $key = $obj->default_key;
        }

        $iv = openssl_random_pseudo_bytes(16);
        return openssl_decrypt($value, $obj->method, $key);
    }

}
?>