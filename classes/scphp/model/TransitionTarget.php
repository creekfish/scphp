<?php

namespace scphp\model;

/**
 * Base class for all transition targets, nodes targeted
 * by transitions (and also contain zero or more transitions
 * to other targets). Nodes of this type are the only ones
 * that can become "active", part of the current Configuration.
 * These nodes also contain zero or more additional transitions;
 * they are all TransitionContainers. Examples are State and
 * Parallel elements.
 *
 * @author bherring
 */
abstract class TransitionTarget extends TransitionContainer
{

	/**
	 * Return array of all children of this target
	 * that are TransitionTargets, in document order.
	 *
	 * @return array of TransitionTarget
	 */
	public function getTargetChildren()
	{
		$targets = array();
		foreach (parent::getChildren() as $key => $child)
		{
			if ($child instanceof TransitionTarget)
			{
				$targets[$key] = $child;
			}
		}
		return $targets;
	}

	/**
	 * Return the first child of this target
	 * that is a TransitionTarget.
	 *
	 * @return TransitionTarget
	 */
	public function getFirstTargetChild()
	{
		foreach (parent::getChildren() as $key => $child)
		{
			if ($child instanceof TransitionTarget)
			{
				return $child;
			}
		}
		return NULL;
	}

	/**
	 * Return a list of initial descendant states of this state
	 * in document order - all states that are automatically
	 * entered by entering this state, not following any
	 * transitions.  Includes both parallel and "normal"
	 * states.
	 *
	 * @return array of TransitionTarget
	 */
	abstract public function getInitialDescendants();
}
