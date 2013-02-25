<?php

use scphp\Model;
use scphp\io\Parser;

include_once('../../AutoLoader.php');

/**
 *
 *
 *
 *
 * Also should make two SCXML things:
 *
 * 1) SCXMLint - scan SCXML for good technique, beyond validation
 * 2) SCXML Javascript based editor (HTML5 and CSS3 too!)
 *
 *
 *
 *
 *
 */


$parser = new Parser();

$xml = file_get_contents('simple.scxml');
$model = $parser->parse($xml);
$model->validateModel();

$descendants = $model->getTarget('second')->getInitialDescendants();
foreach ($descendants as $desc)
{
	echo (string) $desc . PHP_EOL;
}

echo "\n";

$descendants = $model->getTarget('third')->getInitialDescendants();
foreach ($descendants as $desc)
{
	echo (string) $desc . PHP_EOL;
}

echo "\n";

echo (string) $model;


//var_dump($model);


//function __autoload($class) {
////    if (strncmp($class, 'PHPUnit', 7)) {
////        $class = '/Applications/MAMP/bin/php/php5.4.4/lib/php/' . str_replace('_', '/', $class) . '.php';
////    }
////    else
////    {
//        // convert namespace to full file path
//        $class = '../../classes/' . str_replace('\\', '/', $class) . '.php';
////    }
//    require_once($class);
//}
