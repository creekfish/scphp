<?php

namespace scphp\io;

use scphp\Model;
use scphp\model\CompoundNode;
use scphp\model\ModelException;

/**
 * SCXML Parser
 * Note: currently based on SimpleXML
 * todo Allow different document parser to be plugged in and different digester to be specified.
 *
 * @author bherring
 */
class Parser
{
    /**
     * The scphp model that is the result of parsing.
     * @var Model
     */
    private $model;


    /**
     * Parse xml document string into scphp model.
     *
     * @param string $xml SCXML text to be parsed into model
     * @return Model the newly constructed scphp model
     * @throws ModelException
     */
    public function parse($xml)
    {
        $this->model = new Model();
        $scxml = new \SimpleXMLIterator($xml);

        // create and add the root SCXML node
        $scphp_node = $this->createNodeFromDocNode('scxml', $scxml);
        $this->model->addNode($scphp_node);

        // recursively create and add all other nodes in document order
        $this->iterateDocumentChildren($scxml, $scphp_node);
        $this->finalizeNodeFromDocNode($scphp_node, $scxml);

        return $this->model;
    }

    /**
     * Recursive method to traverse document tree in document order (inorder)
     * creating corresponding scphp nodes for an entire branch of the tree.
     *
     * @param \SimpleXMLElement $doc_node
     * @param \scphp\model\CompoundNode $scphp_node
     * @return void
     */
    private function iterateDocumentChildren(\SimpleXMLElement $doc_node, CompoundNode $scphp_node)
    {
       foreach ($doc_node->children() as $node_name => $doc_child)
       {
            // create and add the new child node
            $scphp_child = $this->createNodeFromDocNode($node_name, $doc_child);
            $this->model->addNode($scphp_child, $scphp_node);
            if (count($doc_child->children()))
            {
                // iterate children of the new node, if any
                $this->iterateDocumentChildren($doc_child, $scphp_child);
            }
           // finalize all attributes/properties of the new node
           $this->finalizeNodeFromDocNode($scphp_child, $doc_child);
       }
    }

    /**
     * Create new model node from SCXML document node.
     *
     * @param string $name the document node/tag name
     * @param \SimpleXMLElement $doc_node the XML document node
     * @return \scphp\model\CompoundNode the new scphp model node
     */
    private function createNodeFromDocNode($name, \SimpleXMLElement $doc_node)
    {
        $class = $this->getModelClassForNodeName($name);
        $new_node = new $class();
        $attr = $doc_node->attributes();
////echo "Creating node with id=" . (string) $attr['id'] . PHP_EOL;
        $new_node->setId((string) $attr['id']);
        return $new_node;
    }

    /**
     * Finalize all attributes/properties of a node based
     * on the corresponding document node. Note that this
     * must be done after the node's children (if any)
     * and parent are added, since some attributes may
     * map to child nodes (such as 'initial').  Node text
     * content is also processed if needed.
     *
     * @param \scphp\model\CompoundNode $node
     * @param \SimpleXMLElement $doc_node
     * @return void
     * @throws \scphp\model\ModelException
     */
    private function finalizeNodeFromDocNode($node, $doc_node)
    {
        // set all the attribute values by invoking setters using naming convention
        $attributes = $doc_node->attributes();
        foreach ($attributes as $name => $value)
        {
            $setter_name = $this->getSetterMethodName($name);
            if (method_exists($node, $setter_name))
            {
                $node->$setter_name((string) $value);
            }
            else
            {
                throw new ModelException(get_class($node) . ' has no setter for attribute ' . $name);
            }
        }
    }

    /**
     * Return setter method name for attribute. This
     * method defines the setter naming convention,
     * override to change it.
     *
     * @param string $attribute_name
     * @return string name of the setter method
     */
    protected function getSetterMethodName($attribute_name)
    {
        return 'set' . ucfirst($attribute_name);
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
