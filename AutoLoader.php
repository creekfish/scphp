<?php

class AutoLoader
{

    static private $classNames = array();

    /**
     * Store the filename (sans extension) & full path of all ".php" files found
     */
    public static function registerDirectory($dirName)
    {
        $di = new DirectoryIterator($dirName);
        foreach ($di as $file) {

            if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
                // recurse into directories other than a few special ones
                self::registerDirectory($file->getPathname());
            } elseif (substr($file->getFilename(), -4) === '.php') {
                // save the class name / path of a .php file found
                $className = substr($file->getFilename(), 0, -4);
                AutoLoader::registerClass($className, $file->getPathname());
            }
        }
    }

    public static function registerClass($className, $fileName) {
        AutoLoader::$classNames[$className] = $fileName;
    }

    public static function loadClass($class) {
        // look for the class in registered class names
        if (isset(AutoLoader::$classNames[$class])) {
            require_once(AutoLoader::$classNames[$class]);
            return;
        }

        // try to convert namespace to full file path
        $ns_class = dirname(__FILE__) . '/classes/' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($ns_class))
        {
            require_once($ns_class);
            return;
        }

        // try breaking down classname with underscores
        $us_class = dirname(__FILE__) . '/classes/' . str_replace('_', '/', $class) . '.php';
        if (file_exists($us_class))
        {
            require_once($us_class);
            return;
        }

        // give up - this class ain't loading
    }

}

spl_autoload_register('AutoLoader::loadClass');
