<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class State extends TransitionTarget
{
	/**
	 * The initial child "substate" of this node.
	 * @var CompoundNode
	 */
	private $initial;


    public function __construct($id = NULL, $doc_order = NULL)
    {
		$this->initial = NULL;
        parent::__construct($id, $doc_order);
    }

	/**
	 * Add a child node to this node.
	 *
	 * @param CompoundNode $child
	 */
	public function addChild(CompoundNode $child)
	{
		if ($child instanceof Initial)
		{
			// initial sub-node specified by initial child element
			if (isset($this->initial) && !($this->initial instanceof Initial))
			{
				throw new ModelException("Cannot use initial child element with initial attribute.");
			}
			$this->initial = $child;
		}
		else
		{
			parent::addChild($child);
		}
	}

    /**
     * Return TRUE if this is a final state (no triggerable transitions)
     *
     * @return boolean
     */
    public function isFinal()
    {

    }

    /**
     *
     * @return boolean
     */
    public function isRegion()
    {

    }

    /**
     *
     * @return boolean
     */
    public function isOrthogonal()
    {

    }

    /**
     *
     * @return Invoke
     */
    public function getInvoke()
    {

    }

    /**
     *
     * @param boolean $value
     */
    public function setFinal($value)
    {

    }

    /**
     *
     * @param Invoke $invoke
     */
    public function setInvoke(Invoke $invoke)
    {

    }

	/**
	 * Get the initial element of this node
	 * (either the initial element or NULL).
	 * To get the initial child node, use getInitialChild()
	 *
	 * @return CompoundNode
	 */
	public function getInitial()
	{
		if ($this->initial instanceof Initial)
		{
			return $this->initial;
		}
		return NULL;
	}

	/**
	 * Set the initial child state of this state from
	 * the id specified in the initial attribute value.
	 *
	 * @param string $initial_attr_value
	 */
	public function setInitial($initial_attr_value)
	{
		$initial = $this->getChild($initial_attr_value);
		if (isset($initial))
		{
			if (!($initial instanceof TransitionTarget))
			{
				throw new ModelException("Initial attribute '{$initial_attr_value}' must specify state or parallel child element.");
			}
			if (isset($this->initial) && $this->initial instanceof Initial)
			{
				throw new ModelException("Cannot use initial attribute '{$initial_attr_value}' with initial child element.");
			}
			$this->initial = $initial;
		}
		else
		{
			throw new ModelException("Invalid id '{$initial_attr_value}' specified in initial attribute.");
		}
	}

	/**
	 * Get the initial child node of this node.
	 *
	 * @return State
	 */
	public function getInitialChild()
	{
		$initial = $this->initial;

		if (!isset($initial))
		{
			// default initial node is the first target child node
			return $this->getFirstTargetChild();
		}

		if ($initial instanceof Initial)
		{
			// follow the initial element's transition to the initial child
			$transition = $initial->getFirstTransition();
			if (isset($transition))
			{
				return $transition->getFirstTarget();
			}
			// initial node has a bad transition
			return NULL;
		}

		// initial child specified by initial attribute of this compound node
		return $initial;
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
	public function getInitialDescendants($depth = 0)
	{
		$ret = array();
		$state = $this;
		if ($depth > 0)
		{
			$ret[$state->getDocumentOrder()] = $state;
		}
		if ($state->isComposite())
		{
			$init = $state->getInitialChild();
			if ($init !== NULL)
			{
				$ret = $ret + $init->getInitialDescendants(++$depth);  // array union
			}
		}
		return $ret;
	}

	/**
	 * Return TRUE if this is an atomic state (a leaf)
	 *
	 * @return boolean
	 */
	public function isAtomic()
	{
		return parent::isSimple();
	}

	/**
	 * Return TRUE if this is a compound state (non-leaf, has child states)
	 *
	 * @return boolean
	 */
	public function isCompound()
	{
		return parent::isComposite();
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
        return $child instanceof TransitionContainer ||
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
		return TRUE;
	}

	public function __toString()
	{
		$init = $this->getInitialChild();
		return parent::__toString() . '; intitial:' . ((isset($init)) ? $init->getId() : 'N/A');
	}
}
