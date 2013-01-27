<?php

namespace scphp\io;

use scphp\Model;
use scphp\model\CompoundNode;
use scphp\model\ModelException;

/**
 *
 * @author bherring
 */
class Parser
{
    /**
     * @var int
     */
    private $doc_order;

    /**
     * @var Model
     */
    private $model;

    public function __construct()
    {
        $this->doc_order = 0;
    }

    /**
     * @param string $xml - SCXML text to be parsed into model
     * @return Model
     * @throws ModelException
     */
    public function parse($xml)
    {
        $model = new Model();

        $iter = new \SimpleXMLIterator($xml);
        foreach (new \RecursiveIteratorIterator($iter, \RecursiveIteratorIterator::SELF_FIRST)
                 as $name => $value) {
//            echo "$type: " . ((isset($value->attributes()->id)) ? $value->attributes()->id : $value->attributes()->event) . PHP_EOL;

            // create new scphp node
            $scphp_node = $this->createNodeFromSimpleXml($name, $value);

////            how do we get the parent node...
//                decorate the iterator with a parent tracking mechanism..
//                  http://stackoverflow.com/questions/517998/php-recursiveiteratoriterator-and-nested-sets
//                  http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre


            // add the node to the model
//           $this->model->addNode($scphp_node, );
        }

        return $model;
    }

    /**
     *
     * @param string $name the node/tag name
     * @param \SimpleXMLElement $simple_node
     * @return \scphp\model\CompoundNode
     */
    private function createNodeFromSimpleXml($name, $simple_node)
    {
        $attributes = $simple_node->attributes();
        $class = $this->getModelClassForNodeName($name);
        $new_node = new $class();
        return $new_node;
    }

    /**
     * Return the model class for the given node name.
     * @param $name name of the node/tag
     * @return string fully qualified class name
     */
    private function getModelClassForNodeName($name)
    {
        return 'scphp\\model\\' . ucfirst(strtolower($name));
    }
}
