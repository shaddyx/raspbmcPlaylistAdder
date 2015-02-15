<?
$addr=$_GET["address"];
$chunks=explode("/",$addr);
$location=str_replace("ẹ","/",$chunks[1]);
file_put_contents("lastLocation.txt", $location);
header("HTTP/1.1 301 Moved Permanently");
header("Location: ".$location);	

