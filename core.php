<?
/**
 * sends request to server if config::$enableRequest is true, otherwise - prints it to screen
 * @param $req - request url
 */
function request($req) {
    if (Config::$enableRequest){
        file_get_contents($req);
    } else {
        print $req;
    }
}


function _print($data, $force = false)
{
    $trace = debug_backtrace();
    $file = explode("/", $trace[0]['file']);
    $file = $file[count($file) - 1];
    $line = "[" . $file . " line " . $trace[0]['line'] . "]";

    if ($data === 0) $data = '0 ';
    if ($data === false) $data = '[false]';
    if (empty($data)) $data = "[empty]";
    if (is_string($data)) {
        $a = $data;
        print $line . $a;
        return;
    } else {
        $a = print_r($data, 1);
    }
    $a = str_replace("\n", "<br>", $a);
    $a = str_replace(" ", "&nbsp;", $a);
    print $line . $a;
}

function rpcAddFile($url, $capt = false)
{
    if ($capt) {
        $file = "http://" . Config::$proxyUrl . "/mv/" . urlencode(str_replace("/", "ẹ", $url)) . "/" . $capt;
    } else {
        $file = $url;
    }

    $request["jsonrpc"] = "2.0";
    $request["id"] = "1";
    $request["method"] = "Playlist.Add";
    $request["params"]["playlistid"] = 1;
    $request["params"]["item"]["file"] = $file;
    request("http://" . SettingsManager::getRPCHost() . "/jsonrpc?request=" . json_encode($request));
}

function rpcShowPlayList()
{
    $request["jsonrpc"] = "2.0";
    $request["id"] = "1";
    $request["method"] = "GUI.ActivateWindow";
    $request["params"]["window"] = "videoplaylist";
    request("http://" . SettingsManager::getRPCHost() . "/jsonrpc?request=" . json_encode($request));
}

function getUrlredirrectLocation($url){
    file_get_contents($url, false, null, 0, 1);
    $capt=$url;
    foreach ($http_response_header as $node) {
        if (substr($node, 0, 9) == "Location:") {
            $url = str_replace("Location: ", "", $node);
            $parts = explode("/", $node);
            $capt = $parts[max(array_keys($parts))];
            break;
        }
    }
    return [$url,$capt];
}

function sendToPlayer($url, $capt = false)
{
    if ($capt === true) {
        list ($url, $capt) = getUrlredirrectLocation($url);
    } else if ($capt === false) {
        $url = getUrlredirrectLocation($url)[0];
    }
    rpcAddFile($url, $capt);
    //return file_get_contents('http://localhost:9900/?url='.$url.'&capt='.$capt);
}

function getSendMessage()
{
    $msg = "<table width=100% height=100%>";
    $msg .= "<tr vertical-align=center>";
    $msg .= "<td style='text-align:center;'>";
    $msg .= "Данные отправлены плееру.<br> Возврат...";
    $msg .= "</td>";
    $msg .= "</tr>";
    $msg .= "</table>";
    $msg .= "<script>setTimeout(function(){window.history.back();}, ".Config::$redirrectTimeout.")</script>";

    return $msg;
}
