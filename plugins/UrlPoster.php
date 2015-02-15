<?php

/**
 * Created by PhpStorm.
 * User: shaddy
 * Date: 01.02.15
 * Time: 15:14
 */
require_once("includes/Plugin.php");
class UrlPoster implements Plugin{
    public static function getName(){
        return "UrlPoster.php";
    }

    public static function passthrough(){
        return "blablabla";
    }

    public static function getUrl(){
        return "http://";
    }

    public static function render($pageUrl = false){
        $url = "";
        if (isset($_POST["mainUrl"])){
            $url = $_POST["mainUrl"];
            $data = file_get_contents($url);
            print $data;
        }

        ob_start();
        include "UrlPoster/tpl/inputForm.php";
        return ob_get_clean();
    }

}