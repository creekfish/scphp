<?php

namespace scphp\model;

/**
 * Base class for nodes that contain zero or more executable content
 * nodes. Examples are Onentry, Onexit, and Transition.
 *
 * @see ExecutableNode
 *
 * @author bherring
 */
abstract class ExecutableContainer extends CompoundNode
{

	/**
	 * Return TRUE if the provided node is a valid parent node type for this node.
	 *
	 * @param CompoundNode $parent
	 * @return boolean
	 */
	public function isValidParent(CompoundNode $parent)
	{
		return ($parent instanceof TransitionTarget ||
			$parent instanceof FinalState);
	}

	/**
	 * Return TRUE if the provided node is a valid child node type for this node.
	 *
	 * @param CompoundNode $child
	 * @return boolean
	 */
	public function isValidChild(CompoundNode $child)
	{
		return $child instanceof ExecutableNode;
	}
}
