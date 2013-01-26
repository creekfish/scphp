<?php

namespace scphp\model;

/**
 * Base class for all compound nodes, which may
 * have zero or more "child" nodes.
 *
 * @author bherring
 */
abstract class CompoundNode extends DocumentNode
{

    /**
     * Child "substates" of this node in document order
     * @var array of CompoundNode
     */
    private $children;

    /**
     * The parent node of this compound node.
     * @var CompoundNode
     */
    private $parent;

    /**
     * The initial child "substate" of this node.
     * @var CompoundNode
     */
    private $initial;

    public function __construct()
    {
        $this->children = array();
        $this->parent = NULL;
        $this->initial = NULL;
        parent::__construct();
    }

    /**
     * Add a child node to this node.
     *
     * @param CompoundNode $child
     */
    public function addChild(CompoundNode $child)
    {
        if (!$this->isValidChild($child))
        {
            throw ModelException(get_class($child) . ' is not a valid child type for ' . get_class($this));
        }
        $this->children[$child->getDocumentOrder()] = $child;
        $child->setParent($this);
    }

    /**
     * Get a list of child nodes in document order.
     *
     * @return array of CompoundNode
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get the parent node of this node.
     *
     * @return CompoundNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent node of this node.
     *
     * @param CompoundNode $parent
     */
    public function setParent(CompoundNode $parent)
    {
        if (!$this->isValidParent($parent))
        {
            throw ModelException(get_class($parent) . ' is not a valid parent type for ' . get_class($this));
        }
        $this->parent = $parent;
    }

    /**
     * Get the initial child "sub-state" of this node.
     *
     * @return CompoundNode
     */
    public function getInitial()
    {
        return $this->initial;
    }

    /**
     * Set the initial child "sub-state" of this node.
     *
     * @param CompoundNode $initial
     */
    public function setInitial(CompoundNode $initial)
    {
        $this->initial = $initial;
        $initial->setParent($this);
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    abstract public function isValidChild(CompoundNode $child);

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    abstract public function isValidParent(CompoundNode $parent);
}
