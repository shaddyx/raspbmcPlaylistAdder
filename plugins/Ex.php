<?
require_once("includes/Plugin.php");
require_once("includes/Re.php");

class Ex implements Plugin
{
    public static function getUrl()
    {
        return "http://ex.ua";
    }

    private static function replaceHref($matches)
    {
        $url = $matches[2];
        if (strpos($url, '/get/') !== false) {
            return '<a href="index.php?play=' . urlencode('http://ex.ua' . $url) . '"';
        }
        return '<a href="index.php?link=' . urlencode($url) . '"';
    }

    private static function replaceFileLinks($matches)
    {
        return '<a href="index.php?play=' . urlencode("http://ex.ua" . $matches[4]) . '"';
    }


    private static function replaceImg($matches)
    {
        if (substr($matches[2], 0, 4) == 'http') {
            return '<img src="' . $matches[2] . '"';
        } else {
            return '<img src="http://ex.ua/' . $matches[2] . '"';
        }
    }

    public static function getName()
    {
        return "Ex";
    }


    private static function parsePlayList($url)
    {
        $data = file_get_contents($url);

        if (strpos($data, '<trackList>') == false) {
            $data = preg_split('/\n|\r\n?/', $data);
            foreach ($data as $node) {
                if (empty($node)) continue;
                sendToPlayer($node, false);
            }
            rpcShowPlayList();
        } else {
            $data = preg_split('/\n|\r\n?/', $data);
            $trackOpen = false;
            $title = false;
            foreach ($data as $node) {
                if ($node == '<track>') {
                    $trackOpen = true;
                    continue;
                }
                if ($trackOpen) {
                    $caption = str_replace("<title>", "", $node);
                    $caption = trim(str_replace("</title>", "", $caption));
                    $trackOpen = false;
                    $title = true;
                    continue;
                }
                if ($title) {
                    $link = str_replace("<location>", "", $node);
                    $link = trim(str_replace("</location>", "", $link));
                    sendToPlayer($link, false);
                    $trackOpen = false;
                    $title = false;
                }

            }
            rpcShowPlayList();
        }
        return getSendMessage();
    }


    public static function passthrough()
    {
        if ($_GET['toModule'] == 'search') {
            $link = "http://ex.ua/search?s=" . $_POST['s'];
            return self::render($link);
        }
    }

    public static function render($pageUrl = false)
    {
        if (!$pageUrl) {
            if (!isset($_GET['link'])) $_GET['link'] = '';
            $url = 'http://ex.ua' . urldecode($_GET['link']);
        } else {
            $url = $pageUrl;
        }

        if (strpos($url, '/playlist/') !== false) {
            return self::parsePlayList($url);
        }

        $page = file_get_contents($url);

        $page = str_replace("window.top.location = window.self.location", "console.log('removed');", $page);
        $page = preg_replace('~\/js\/(.*?)~', 'http://ex.ua/js/$1', $page);
        $page = preg_replace('~\/(.*?\.css)~', 'http://ex.ua/$1', $page);
        $page = preg_replace_callback('~\<a href=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceHref'), $page);
        $page = preg_replace_callback('~\<a id\=([\"\\\'])([\s\S]*?) href=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceFileLinks'), $page);
        $page = preg_replace_callback('~\<img src=([\"\\\'])(.*?)\1~', array(self::getName(), 'replaceImg'), $page);
        $page = preg_replace('~\<iframe ([\s\S]*?)iframe\>~', '', $page);
        $page = str_replace('<link href="/index.css?0.04" type="text/css" rel="stylesheet">', '<link href="http://ex.ua/index.css?0.04" type="text/css" rel="stylesheet">', $page);
        $page = str_replace("<form name=search action='/search' id=search_form>", "<form name=search id=search_form method='post' action='/index.php?toModule=search'>", $page);
        $page = str_replace('</head>', '
                <script type="text/javascript" src="/js/injects/jquery.js"></script>
                <script type="text/javascript" src="/js/injects/func.js"></script>
                <script type="text/javascript">console.log("Injection success"); </script>
                <script type="text/javascript" src="/js/injects/ex.js"></script></head>', $page);

        $removeSplashHack='<script>setTimeout(function(){var evObj = document.createEvent("Events"); evObj.initEvent("click", true, false); document.getElementsByClassName("ex_file")[0].dispatchEvent(evObj);},100)</script>';        
        
        $page.=$removeSplashHack;       
        
        return $page;
    }
}
    
