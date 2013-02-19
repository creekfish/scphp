<?php

namespace scphp\model;

/**
 * Base class for all "executable content" nodes.
 *
 * @see ExecutableNodeContainer
 *
 * @author bherring
 */
abstract class ExecutableNode extends CompoundNode
{

    /**
     * Execute this action instance.
     *
     * @param evtDispatcher The EventDispatcher for this execution instance
     * @param errRep        The ErrorReporter to broadcast any errors
     *                      during execution.
     * @param scInstance    The state machine execution instance information.
     * @param appLog        The application Log.
     * @param derivedEvents The collection to which any internal events
     *                      arising from the execution of this action
     *                      must be added.
     *
     * @throws ModelException If the execution causes the model to enter
     *                        a non-deterministic state.
     * @throws SCXMLExpressionException If the execution involves trying
     *                        to evaluate an expression which is malformed.
     */
//    abstract public function execute(EventDispatcher $evtDispatcher,
//            ErrorReporter $errRep,
//            SCInstance $scInstance,
//            Log $appLog,
//            Collection $derivedEvents);

    abstract public function execute($logger);


	/**
	 * Return TRUE if the provided node is a valid parent node type for this node.
	 *
	 * @param CompoundNode $parent
	 * @return boolean
	 */
	public function isValidParent(CompoundNode $parent)
	{
		return ($parent instanceof ExecutableNodeContainer);
	}

	/**
	 * Return TRUE if the provided node is a valid child node type for this node.
	 * NOTE: Executable node has no children by default, subclass must override to allow
	 * child nodes.
	 *
	 * @param CompoundNode $child
	 * @return boolean
	 */
	public function isValidChild(CompoundNode $child)
	{
		return FALSE;
	}

}
