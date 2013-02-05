<?php

use scphp\Model;
use scphp\io\Parser;

include_once('../../AutoLoader.php');

//$t = new scphp\model\TransitionList();
//
//$t[2] = new scphp\model\Transition();
//$t[2]->setEvent(new \scphp\model\Event('event1'));
//$t[4] = new scphp\model\Transition();
//$t[4]->setEvent(new \scphp\model\Event('event2'));
//$t[5] = new scphp\model\Transition();
//$t[5]->setEvent(new \scphp\model\Event('event3'));
//$t[5] = new scphp\model\Log();
////$t[5]->setEvent(new \scphp\model\Event('event3'));
//
//foreach ($t as $tran)
//{
//    echo $tran->getEvent()->getName() . PHP_EOL;
//}
//exit;

$parser = new Parser();

$xml = file_get_contents('simple.scxml');
$model = $parser->parse($xml);
$model->validateModel();

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
