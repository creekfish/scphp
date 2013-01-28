<?php

use scphp\Model;
use scphp\io\Parser;

$parser = new Parser();

$xml = file_get_contents('simple.scxml');
$model = $parser->parse($xml);
$model->validateModel();

echo (string) $model;


//var_dump($model);


function __autoload($class) {
    // convert namespace to full file path
    $class = '../../classes/' . str_replace('\\', '/', $class) . '.php';
    require_once($class);
}
