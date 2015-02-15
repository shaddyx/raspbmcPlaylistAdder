<?

class Proxy
{
    private static $params;
    private static $plugin;

    public static function init($pluginName)
    {
        global $plugins;
        //if (!$pluginName){
         //    return;
       // }
        if(!isset($plugins[$pluginName]))
        {
           $pluginName=array_keys($plugins)[0];
        }
        self::$plugin = $plugins[$pluginName];
        if (!isset(Plugins::$list[$pluginName])) throw new Exception("undefined plugin: " . $pluginName);
        self::$params = Plugins::$list[$pluginName];
        require_once('plugins/' . self::$params['module']);
    }

    public static function getPage()
    {
        $plugin = self::$plugin;
        $data = $plugin['class']::render();
        return $data;
    }

    public static function getPlugins()
    {
        foreach (Plugins::$list as $key => $item) {
            $hrefs[] = "<a style='color:white; font-size:16px;' href='/index.php?setModule=" . $key . "'>" . $item['caption'] . '</a>';
        }
        print "<div style='display: inline-block; background:#6E2293; position:fixed; padding:5px; top:0px; left:0px;'>".implode(" | ", $hrefs)."</div>";
    }

    public static function passthrough()
    {
        $plugin = self::$plugin;
        return $plugin['class']::passthrough();
    }
}
    