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
            throw new ModelException(get_class($child) . ' is not a valid child type for ' . get_class($this));
        }
        if ($child instanceof Initial)
        {
            $this->initial = $child;
        }
        else
        {
            $idx = $child->getId();
            if (empty($idx))
            {
                // ensure valid index for child node without id
                $idx = '__NID__' . count($this->children);
            }
            $this->children[$idx] = $child;
        }
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
     * Fetch a child by id.
     *
     * @param string $target_id
     * @return CompoundNode | NULL
     */
    public function getChild($target_id)
    {
        $this->children[$target_id];
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
    public function setParent(CompoundNode $parent = NULL)
    {
        if ($parent !== NULL && !$this->isValidParent($parent))
        {
            throw new ModelException(get_class($parent) . ' is not a valid parent type for ' . get_class($this));
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
     * Set the initial child "sub-state" of this node from
     * the id specified in the initial attribute value.
     *
     * @param string $initial_attr_value
     */
    public function setInitial($initial_attr_value)
    {
        $initial = $this->getModel()->getTarget($initial_attr_value);
        if (isset($initial) && in_array($initial, $this->children))
        {
            $this->initial = $initial;
        }
        else
        {
            throw new ModelException("Invalid id '{$initial_attr_value}' specified in initial attribute.");
        }
    }

    public function __toString()
    {
        $init = $this->getInitial();
        return parent::__toString() . '; intitial:' . ((isset($init)) ? $init->getId() : 'N/A');
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
