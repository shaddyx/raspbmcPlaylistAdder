<? 
error_reporting(E_ALL); 
ini_set('display_errors', 1);


require_once('Config.php');
require_once('core.php');
require_once('Proxy.php');
require_once('Plugins.php');
require_once('SettingsManager.php');

if (!isset($_COOKIE['plugin'])) {
    $_COOKIE['plugin'] = Config::$defaultPlugin;
}

if (!isset($_COOKIE['settings_rpc_host'])) {
    SettingsManager::showSettingsPage();
}


if (isset($_GET['setModule'])) {
        if (isset(Plugins::$list[$_GET['setModule']])) {
        setcookie('plugin', $_GET['setModule']);
        $currentPlugin = $_GET['setModule'];
    }
} else {
    if (isset($_COOKIE['plugin'])){
        $currentPlugin = $_COOKIE['plugin'];
    } else {
        $currentPlugin = false;
    }
}
header('Content-Type: text/html; charset=utf-8');
if (isset($_GET['play'])) {
    sendToPlayer($_GET['play']);
    rpcShowPlayList();
    print getSendMessage();
    die();
}
try {
    Proxy::init($currentPlugin);
} catch (Exception $e) {
    print "Error initializing plugin $currentPlugin :" . $e;
}


if (isset($_GET['toModule'])) {
    print Proxy::passthrough();
    die();
}

print Proxy::getPlugins();
if ($currentPlugin) {
    print Proxy::getPage();
}




