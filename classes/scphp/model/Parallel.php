<?php

namespace scphp\model;

/**
 * A parallel state whose children execute in parallel.
 * of the state machine configuration.
 * This node is essentially a state that can be active.
 *
 * @author bherring
 */
class Parallel extends TransitionTarget
{
	/**
	 * Return a list of initial descendant states of this state
	 * in document order - all states that are automatically
	 * entered by entering this state, not following any
	 * transitions.  Includes both parallel and "normal"
	 * states.
	 *
	 * @return array of TransitionTarget
	 */
	public function getInitialDescendants($depth = 0)
	{
		$ret = array();
		$parallel = $this;

		if ($depth > 0)
		{
			$ret[$parallel->getDocumentOrder()] = $parallel;
		}
		// add all parallel child states to the configuration
		/** @var TransitionTarget $child  */
		foreach ($parallel->getTargetChildren() as $child)
		{
			// add child to the configuration
			$ret[$child->getDocumentOrder()] = $child;
			$ret = $ret + $child->getInitialDescendants(++$depth);  // array union
		}
		return $ret;
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
		return $child instanceof TransitionTarget ||
			$child instanceof Transition;
	}

	/**
	 * Validate this document node (e.g. against the SCXML standard).
	 * Only has meaning once the model if fully parsed and
	 * all nodes are created.
	 *
	 * @return boolean TRUE if validation passes, otherwise FALSE
	 * @throws \scphp\model\ModelValidationException
	 */
	public function validate()
	{
		if ($this->isSimple())
		{
			throw new \scphp\model\ModelValidationException('Parallel node must have child nodes.');
		}
		return TRUE;
	}
}
