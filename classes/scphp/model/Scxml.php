<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Scxml extends CompoundNode
{
    /**
     * The SCXML namespace
     */
    const SCXML = 'http://www.w3.org/2005/07/scxml';

    /**
     * The SCXML version of this document.
     * @var string
     */
    private $version;

	/**
	 * The initial state for this SCXML state machine.
	 * @var TransitionTarget
	 */
	private $initial;

    /**
     * Set the document version.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Get the document version.
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
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
			$this->initial = $initial;
		}
		else
		{
			throw new ModelException("Invalid id '{$initial_attr_value}' specified in initial attribute.");
		}
	}

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return FALSE;
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof TransitionContainer;
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
