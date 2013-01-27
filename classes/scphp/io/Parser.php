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
     * @var Model
     */
    private $model;

    /**
     * @var array of \SimpleXMLElement
     */
    private $simple_to_scphp_map;


    /**
     * @param string $xml - SCXML text to be parsed into model
     * @return Model
     * @throws ModelException
     */
    public function parse($xml)
    {
        $this->model = new Model();

        $scxml = new \SimpleXMLIterator($xml);

//        echo "\n\nTOP LEVEL\n";
//        var_dump($iter->attributes());

        // create and add the root SCXML node
        $scphp_node = $this->createNodeFromSimpleXml('scxml', $scxml);
        $this->model->addNode($scphp_node);
        $this->addNodeToXmlMap($scxml, $scphp_node);

        foreach (new \RecursiveIteratorIterator($scxml, \RecursiveIteratorIterator::SELF_FIRST)
                 as $name => $xml_node) {
//            echo "$type: " . ((isset($value->attributes()->id)) ? $value->attributes()->id : $value->attributes()->event) . PHP_EOL;

            // create new scphp node
            $scphp_node = $this->createNodeFromSimpleXml($name, $xml_node);

////            how do we get the parent node...
//                decorate the iterator with a parent tracking mechanism..
//                  http://stackoverflow.com/questions/517998/php-recursiveiteratoriterator-and-nested-sets
//                  http://stackoverflow.com/questions/2915748/how-can-i-convert-a-series-of-parent-child-relationships-into-a-hierarchical-tre
//todo stack for parent node... or just go with recursion...

            $parent = $xml_node->xpath('..');
            echo "\n\nNODE\n";
            var_dump($xml_node->attributes());

//            $var_parent
            echo "\nPARENT\n";
            var_dump($parent[0]->attributes());
            echo "\n\n";

            // add the node to the model
           $this->model->addNode($scphp_node, $this->getNodeFromXmlMap($parent[0]));
           $this->addNodeToXmlMap($xml_node, $scphp_node);
        }

        return $this->model;
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

    /**
     * @param \SimpleXMLElement $xml_node
     * @param \scphp\model\CompoundNode $node
     */
    private function addNodeToXmlMap(\SimpleXMLElement $xml_node, \scphp\model\CompoundNode $node)
    {
        $this->simple_to_scphp_map[$this->get_object_hash($xml_node)] = $node;
    }

    /**
     * @param \SimpleXMLElement $xml_node
     * @return \scphp\model\CompoundNode
     */
    private function getNodeFromXmlMap(\SimpleXMLElement $xml_node)
    {
        return $this->simple_to_scphp_map[$this->get_object_hash($xml_node)];
    }

    private function get_object_hash($obj)
    {
        return spl_object_hash($obj);
    }
}
