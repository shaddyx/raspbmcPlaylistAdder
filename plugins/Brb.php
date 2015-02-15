<?
require_once("includes/Plugin.php");

class Brb implements Plugin
{
    private static $hrefsChanged = false;

    public static function getUrl()
    {
        return "http://fs.ua";
    }

    private static function replaceHref($matches)
    {
        //_print($matches);

        return '<a href="/index.php?link=' . urlencode($matches[2]) . '"';
    }


    private static function replaceFileLinks($matches)
    {
        return '<a href="/index.php?play=' . urlencode("http://brb.to" . $matches[4]) . '"';
    }

    private static function replaceImg($matches)
    {
        //_print($matches);
        if (substr($matches[2], 0, 4) == 'http') {
            return '<img src="' . $matches[2] . '"';
        } else {
            return '<img src="http://ex.ua/' . $matches[2] . '"';
        }
    }


    private static function replaceSrc($matches)
    {
        $param = $matches[2];
        if (substr($param, 0, 4) != 'http') {
            if (substr($param, 0, 2) == '//') {
                return 'src="http:' . $matches[2] . '"';
            }

            if (substr($param, 0, 7) == '/jsitem') {
                return $matches[0];
            }


            return 'src="http://brb.to' . $matches[2] . '"';

        } else {
            return $matches[0];
        }
    }


    private static function replaceActions($matches)
    {
        return 'action="/index.php?toModule=' . $matches[2] . '"';
        //      _print($matches);
        //      die();
    }

    public static function replaceForms($matches)
    {
        return '<form method="post"';
    }

    public static function parsePlayList($url)
    {
        $data = file_get_contents($url);
        $data = preg_split('/\n|\r\n?/', $data);
        foreach ($data as $node) {
            if (empty($node)) continue;
            $parts = explode("/", $node);
            $capt = $parts[max(array_keys($parts))];
            sendToPlayer($node, $capt);
        }
        rpcShowPlayList();
        return getSendMessage();
    }

    public static function replaceFileList($data)
    {
        $data = $data[0];
        $l = strlen($data);

        $sPattern = '<li class="b-file-new m-file-new_type_video">';
        $ePattern = '/li';

        $parts = array();
        $cnt = 0;
        $start = false;
        for ($i = 0; $i < $l; $i++) {
            $sub = substr($data, $i, strlen($sPattern));
            if ($sub == $sPattern) {
                $start = true;
            }
            $sub = substr($data, $i, strlen($ePattern));
            if ($sub == $ePattern) {
                $start = false;
                $cnt++;
            }
            if ($start) {
                if (!isset($parts[$cnt])) $parts[$cnt] = "";
                $parts[$cnt] .= substr($data, $i, 1);
            }

        }

        $items = array();
        foreach ($parts as $node) {
            preg_match_all('~\<span class\=\"(.*?)\>(.*?)\<\/span\>~', $node, $spans);
            preg_match_all('~href\=\"\/get\/(.*?)\"~', $node, $links);

            $link = '"/index.php?play=' . urlencode("http://brb.to/get/" . $links[1][0]) . '"';

            $item = '<li class="b-file-new m-file-new_type_video"><a href=' . $link . '>';
            $item .= '<span style="font-family:Tahoma, Arial, sans-serif; font-size:14px; color:4e4e4e;">' . $spans[2][0] . '</span></a><a href=' . $link . ' class="b-file-new__link-material-download">' . $spans[0][1] . "</a></li>";

            $items[] = $item;
        }


        if (count($items)) {
            self::$hrefsChanged = true;
            $sPattern = '<li class="folder">';
            $ePattern = '<ul';
            $fold = '';
            $start = false;
            for ($i = 0; $i < $l; $i++) {
                $sub = substr($data, $i, strlen($sPattern));
                if ($sub == $sPattern) {
                    $start = true;
                }
                $sub = substr($data, $i, strlen($ePattern));
                if ($start && $sub == $ePattern) {
                    $start = false;
                    break;
                }
                if ($start) {
                    $fold .= substr($data, $i, 1);
                }

            }
            if ($fold) {
                $fold = preg_replace_callback('~\<a href=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceHref'), $fold);
                return '<ul class="filelist">' . $fold . '<ul class="filelist m-current"  >' . implode("\n", $items) . '</ul></li></ul>';
            } else {
                return '<ul class="filelist m-current"  >' . implode("\n", $items) . '</ul>';
            }
        }
        return $data;
    }


    public static function render($pageUrl = false)
    {
        if (!$pageUrl) {
            if (!isset($_GET['link'])) $_GET['link'] = "";
            $url = 'http://brb.to' . urldecode($_GET['link']);
        } else {
            $url = $pageUrl;
        }

        if (strpos($url, '/flist/') !== false) {
            return self::parsePlayList($url);
        }

        $page = file_get_contents($url);

        $page = preg_replace_callback('~\<ul class\=\"filelist([\s\S]*)\<\/ul\>~', array(self::getName(), 'replaceFileList'), $page);

        if (!self::$hrefsChanged) $page = preg_replace_callback('~\<a href=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceHref'), $page);
        if (!self::$hrefsChanged) $page = preg_replace_callback('~\<a id\=([\"\\\'])([\s\S]*?) href=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceFileLinks'), $page);

        $page = preg_replace_callback('~\<img src=([\"\\\'])(.*?)\1~', array(self::getName(), 'replaceImg'), $page);
        $page = preg_replace_callback('~src=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceSrc'), $page);
        $page = preg_replace_callback('~action=([\"\\\'])([\s\S]*?)\1~', array(self::getName(), 'replaceActions'), $page);
        $page = preg_replace_callback('~\<form method\="get"~', array(self::getName(), 'replaceForms'), $page);

        $page = str_replace('admixer', 'pwnd_by_Mohell', $page);


        return $page;
    }

    public static function getName()
    {
        return "Brb";
    }

    public static function passthrough()
    {
        $url = $_SERVER['REQUEST_URI'];
        if (strpos($url, 'jsitem') !== false) {
            return "window.FS_ITEM_FILE_LIST.data = {download: '1', view: '1', view_embed: '0', 'blocked': '0'};window.FS_ITEM_FILE_LIST.show();";
        }
        if (strpos($url, 'ajax') !== false) {
            return self::render('http://brb.to' . $url);
        }
        if (strpos($url, 'search.aspx') !== false) {
            return self::render('http://brb.to/search.aspx?search=' . $_POST['search']);
        }

        return self::render('http://brb.to' . $url);

        //$url=$_SERVER['REQUEST_URI'];
        //_print($url);
    }

}