<?
    $plugins = array();
    $path = "./plugins";
    $dir = opendir($path);
    while ($file = readdir($dir)){
        $fullFileName = $path.'/'.$file;
        $classNameChunks = explode('.', $file);
        $className = implode('.', array_slice($classNameChunks, 0, -1));

        if (is_file($fullFileName)){
            include ($fullFileName);
            $plugins[$file] = array(
                'url' =>$className::getUrl(),
                'module'=>$file,
                'caption'=>$className::getName(),
                'class'=>$className
            );
        }
    }
    class Plugins
    {
        public static $list;
        
        public static function init($data)
        {
            self::$list=$data;
        }
    }
  
    Plugins::init($plugins);