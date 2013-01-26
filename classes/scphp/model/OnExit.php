<?php

namespace scphp\model;

/**
 * Container for executable content to be executed when the transition
 * target is exited.
 *
 * @author bherring
 */
class OnExit extends CompoundTarget
{
    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return $parent instanceof TransitionTarget;
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof ExecutableNode;
    }
}
