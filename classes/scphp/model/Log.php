<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Log extends ExecutableNode
{

    /**
     * @var Expression
     */
    private $expression;

    /**
     * @var string
     */
    private $label;

    public function execute($logger)
    {
        $logger->info($this->label . ': ' . $this->expression->toString());
    }

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return ($parent instanceof Transition ||
            $parent instanceof Onentry ||
            $parent instanceof Onexit
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
