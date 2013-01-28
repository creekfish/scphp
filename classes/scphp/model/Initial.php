<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Initial extends TransitionTarget
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a transition to the list of transitions from this initial state.
     *
     * @param Transition $transition - Transition to be added.
     * @return void
     */
    public function addTransition(Transition $transition)
    {
        if ($transition->getCondition() !== NULL)
        {
            throw new ModelException('Initial node transition cannot have condition.');
        }
        if ($transition->getEvent() !== NULL)
        {
            throw new ModelException('Initial node transition cannot have event.');
        }
        if (count($transition->getTargets()) === 0)
        {
            throw new ModelException('Initial node transition must specify a valid target.');
        }
        if (count($transition->getChildren()) !== 0)
        {
            throw new ModelException('Initial node transition cannot contain executable content.');
        }
        parent::addTransition($transition);
    }

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return ($parent instanceof TransitionTarget ||
                $parent instanceof Scxml
        );
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof Transition;
    }
}
