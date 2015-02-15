<?php
/**
 * Created by PhpStorm.
 * User: shaddy
 * Date: 03.02.15
 * Time: 18:16
 */

class ExUaPoster implements UrlPosterPlugin{
    public static function getName(){
        return "Ex.ua";
    }

    public static function canProcessUrl($url){
        return strstr($url,"http://ex.ua/");
    }

    public static function processUrl($url) {
        $data = file_get_contents("$url");
    }
}