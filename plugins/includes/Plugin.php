<?

interface Plugin
{
    public static function getName();

    public static function passthrough();

    public static function getUrl();

    public static function render($pageUrl = false);
}