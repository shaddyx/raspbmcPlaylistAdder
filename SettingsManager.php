<?
    class SettingsManager
    {
        public static function showSettingsPage()
        {
            if(config::$useSettingsManager)
            {
                include("tpl/settings.tpl");
                die();
            }
        }
        public static function getRPCHost()
        {
            if(config::$useSettingsManager)
            {
                if(!in_array($_SERVER['REMOTE_ADDR'],config::$settingsManagerExclusions))
                {
                    return $_COOKIE['settings_rpc_host'];
                }
            }
            return config::$rpcHost;
        }
        
    }