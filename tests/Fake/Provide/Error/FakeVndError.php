<?php

/**
 * test header function is taken from Aura.Web
 * @see https://github.com/auraphp/Aura.Web/blob/a1a4e45d14b21d40d716d341b78a050e1905cc05/tests/unit/src/FakeResponseSender.php
 */
namespace BEAR\Sunday\Provide\Error;

use BEAR\Resource\ResourceObject;

require_once __DIR__ . '/header.php';

class FakeVndError extends VndError
{
    public static $code = [];
    public static $headers = [];
    public static $content;

    public static function reset()
    {
        static::$code = [];
        static::$headers = [];
        static::$content = null;
    }

    public function transfer()
    {
        ob_start();
        parent::transfer();
        $body =  ob_get_clean();
        self::$content = $body;
    }
}
