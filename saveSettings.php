<?
    if(isset($_POST['addr']))
    {
        setcookie("settings_rpc_host",$_POST['addr']);
        print "<script>window.location.href='/';</script>";
    }
   