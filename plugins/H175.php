<?
require_once("includes/Plugin.php");
require_once("includes/Re.php");

class H175 implements Plugin
{
    public static function getUrl()
    {
        return "http://175h.net";
    }

    public static function getName()
    {
        return "H175";
    }


    public static function passthrough()
    {
        $url = $_SERVER['REQUEST_URI'];
    
    
        if($_GET['toModule']=='movie')
        {
            $url="http://175h.net/movie.html?".$_GET['m'];
            print self::render($url);
            return;
        }
        if(strpos($url,"api.js")!==false)
        {
            
            $url=explode("?toModule=",$url)[1];
            $data=file_get_contents($url);
            $data=str_replace("function template","function removed_template",$data);
            
            $data.="function template(name, data){"."\n";   
            $data.="var tpl=document.getElementById(name).text;"."\n";
            $data.="tpl=tpl.replace('img src=\'/','img src=\'http://175h.net/')"."\n";
            $data.="tpl=tpl.replace('a href=\'/movie.html?','a href=\'?toModule=movie&m=')"."\n";
         
            $data.="tpl=tpl.replace(/\<video([\s\S]*?)<\/video>/,'');"."\n";
            
            $data.="tpl=\"<a href='/index.php?play=http://175h.net{{=it.src}}'><img src='tv.png'/></a>\"+tpl;"."\n";
            
            $data.="var f = doT.template(tpl);"."\n";
            $data.="document.getElementById('content').innerHTML = f(data);"."\n";
            $data.="}";
       
            print $data;
            return;
        }
        
        $url=str_replace("/?toModule=","",$url);
        print file_get_contents('http://175h.net/'.$url);
    }

    public static function render($pageUrl = false)
    {
        if (!$pageUrl) {
            if (!isset($_GET['link'])) $_GET['link'] = '';
            $url = 'http://175h.net' . urldecode($_GET['link']);
        } else {
            $url = $pageUrl;
        }
        
        
        
        
        
        $page = file_get_contents($url);
        $page = str_replace("href='/index.css?0.02", "href='http://175h.net/index.css?0.02",$page);
        $page = str_replace("src='/doT.min.js'", "src='http://175h.net/doT.min.js'",$page);
        $page = str_replace("src='/api.js?0.03'", "src='?toModule=http://175h.net/api.js?0.03'",$page);
        $page = str_replace("/hs/highslide.js", "http://175h.net/hs/highslide.js",$page);
        
        
        $page = str_replace("loadJs('/'", "loadJs('http://175h.net/'",$page);
        $page = str_replace("var name = search.length > 1 ? search.substr(1) : 'id';", "var name = 'id';",$page);
        if($pageUrl&&strpos($pageUrl,"movie.html?"))
        {
            $movie=explode("movie.html?",$pageUrl)[1];
            $page = str_replace("loadJs('http://175h.net/' + search.substr(1) + '/a.js', cb)", "loadJs('http://175h.net/".$movie."/a.js', cb)",$page);
        }
        
        
        
        /*
        
        <script src='/doT.min.js'></script>
<script src='/api.js?0.03'></script>
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
        
        */
        return $page;
    }
}
    
