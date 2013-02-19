<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class FinalState extends TransitionContainer
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
        return (
			$parent instanceof State ||
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
