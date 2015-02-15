<?php

/**
 * Created by PhpStorm.
 * User: shaddy
 * Date: 31.01.15
 * Time: 13:51
 */
class Re
{
    public static function replace($re, $replace, $string)
    {
        return preg_replace('~' . $re . '~', $replace, $string);
    }
}