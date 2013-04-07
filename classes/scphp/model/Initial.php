<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Initial extends TransitionContainer
{
    /**
     * Add a transition to this initial element node, replacing
     * any existing transition (only one transition is permitted).
     *
     * @param Transition $transition Transition to be added.
     * @return void
     * @throws ModelException
     */
    public function addTransition(Transition $transition)
    {
        // validate the transition for an initial node
        if ($transition->getCondition() !== NULL)
        {
            throw new ModelException('Initial node transition cannot have condition.');
        }
		$events = $transition->getEvents();
        if (!empty($events))
        {
            throw new ModelException('Initial node transition cannot have event.');
        }
        if (count($transition->getTargetIds()) === 0)
        {
            throw new ModelException('Initial node transition must specify a valid target.');
        }

        // remove any existing transition
        $trans = $this->getFirstTransition();
        if (isset($trans))
        {
            $this->removeTransition($trans);
        }

        // add the specified transition
        parent::addTransition($transition);
    }

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     *
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return ($parent instanceof TransitionContainer ||
                $parent instanceof Scxml
        );
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     *
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof Transition;
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
		return TRUE;
	}
}
