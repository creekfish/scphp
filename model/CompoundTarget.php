<?php

namespace org\funmonkeys\scphp\model;

/**
 * Base class for all compound transition targets, which may
 * have zero or more "child" target nodes.
 *
 * @author bherring
 */
abstract class CompoundTarget extends TransitionTarget
{

    /**
     * Child "substates" of this target in document order
     * @var CompoundTarget[]
     */
    private $children;

    /**
     * The parent node of this compound target.
     * @var CompoundTarget
     */
    private $parent;

    /**
     * The initial child "substate" of this target.
     * @var CompoundTarget
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
     * Add a child target to this target.
     *
     * @param CompoundTarget $child
     */
    public function addChild(CompoundTarget $child)
    {
        $this->children[$child->getDocumentOrder()] = $child;
        $child->setParent($this);
    }

    /**
     * Get a list of child targets in document order.
     *
     * @return TransitionTarget[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get the parent target of this target.
     *
     * @return CompoundTarget
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the parent target of this target.
     *
     * @param CompoundTarget $parent
     */
    public function setParent(CompoundTarget $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the initial child "sub-state" of this target.
     *
     * @return CompoundTarget
     */
    public function getInitial()
    {
        return $this->initial;
    }

    /**
     * Set the initial child "sub-state" of this target.
     *
     * @param CompoundTarget $initial
     */
    public function setInitial(CompoundTarget $initial)
    {
        $this->initial = $initial;
        $initial->setParent($this);
    }

}
