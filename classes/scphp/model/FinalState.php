<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class FinalState extends TransitionTarget
{

    public function addTransition(Transition $transition)
    {
        throw new ModelException('Cannot add transition to Final');
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
        return FALSE;
    }
}
