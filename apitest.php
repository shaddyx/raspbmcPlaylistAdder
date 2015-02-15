<?
    
$request["jsonrpc"]="2.0";
$request["id"]="1";
$request["method"]="Playlist.Add";
$request["params"]["playlistid"]=1;
$request["params"]["item"]["file"]="http://my.tv/redirect1.php";





print file_get_contents("http://my.tv:808/jsonrpc?request=".json_encode($request));

$request=array();
$request["jsonrpc"]="2.0";
$request["id"]="1";
$request["method"]="GUI.ActivateWindow";
$request["params"]["window"]="videoplaylist";


print file_get_contents("http://my.tv:808/jsonrpc?request=".json_encode($request));
    
