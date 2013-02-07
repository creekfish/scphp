<?php

namespace scphp;

use scphp\model\CompoundNode;
use scphp\model\Event;
use scphp\model\Scxml;
use scphp\model\Transition;
use scphp\model\TransitionTarget;
use scphp\model\ModelException;
use scphp\model\ModelValidationException;

/**
 *
 * @author bherring
 */
class Model
{
    /**
     * @var int
     */
    private $doc_order;

    /**
     * @var Scxml
     */
    private $scxml;

    /**
     * @var array of TransitionTarget
     */
    private $targets;

    /**
     * @var array of Transition
     */
    private $transitions;


    public function __construct()
    {
        $this->doc_order = 0;
        $this->scxml = new Scxml();
    }

    /**
     * Add a node to this model.
     *
     * @param model\CompoundNode $node
     * @param model\CompoundNode $parent
     * @return void
	 * @throws ModelException
     */
    public function addNode(CompoundNode $node, CompoundNode $parent = NULL)
    {
        $node->setDocumentOrder($this->doc_order++);
        $node->setModel($this);
        if ($node instanceof TransitionTarget)
        {
            // keep up with all transition targets
            $this->addTarget($node);
        }
        if ($node instanceof Transition)
        {
            $this->addTransition($node);
        }
        if ($node instanceof Scxml)
        {
			if ($parent !== NULL) {
				throw new ModelException('SCXML node cannot have parent node.');
			}
            // this is the "parent" for the SCXML doc node
            $this->scxml = $node;
        }
        else
        {
            $parent->addChild($node);
        }
    }

    /**
     * Add a TransitionTarget for this model.
     *
     * @param \scphp\model\TransitionTarget $target
     * @return void
     */
    public function addTarget(TransitionTarget $target)
    {
        $idx = $target->getId();
        if (empty($idx))
        {
            // ensure valid index for target without id (cannot be referenced in transitions)
            $idx = '__TID__' . count($this->targets);
        }
        $this->targets[$idx] = $target;
    }

	/**
	 * Return the root Scxml node for this model.
	 *
	 * @return Scxml
	 */
	public function getScxml()
	{
		return $this->scxml;
	}

    /**
     * Return a list of all TransitionTargets for this model.
     *
     * @return array of TransitionTarget
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Add a Transition to this model.
     *
     * @param \scphp\model\Transition $transition
     * @return void
     */
    public function addTransition(Transition $transition)
    {
        $event = $transition->getEvent();
        $idx = (!empty($event)) ? $event->getName() : NULL;
        if (empty($idx))
        {
            $idx = NULL;
        }
        $this->transitions[$idx][] = $transition;
    }

    /**
     * Return a list of all Transitions in this model
     * that can be triggered by the given event.
     *
     * @param \scphp\model\Event $event
     * @return array of Transition
     */
    public function getTransitions(Event $event)
    {
        $event_key = (!empty($event)) ? $event->getName() : NULL;
        return $this->transitions[$event_key];
    }

	/**
	 * Return a list of all Transitions in this model.
	 *
	 * @return array of Transition
	 */
	public function getAllTransitions()
	{
		$ret = array();
		foreach ($this->getTriggers() as $trigger)
		{
			foreach ($this->transitions[$trigger] as $transition)
			{
				$ret[] = $transition;
			}
		}
		return $ret;
	}

    /**
     * Return a list of all event names that can trigger
     * transitions in this model.
     * NOTE: NULL and "" are valid event names
	 * NOTE: ordering of list is indeterminate
     *
     * @return array of string
     */
    public function getTriggers()
    {
        return array_keys($this->transitions);
    }

    /**
     * Return the TransitionTarget that matches the given
     * transition target id.
     *
     * @param string $target_id
     * @return TransitionTarget
     */
    public function getTarget($target_id)
    {
		if (array_key_exists($target_id, $this->targets))
		{
			return $this->targets[$target_id];
		}
		return NULL;
    }

    /**
     * Return TRUE if the target id is a valid transiton
     * target for this model.
     *
     * @param string $target_id
     * @return boolean
     */
    public function isTarget($target_id)
    {
        if (!isset($this->targets[$target_id]))
        {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Throw exception if the finalized model fails validation.
     *
     * @throws model\ModelValidationException
     */
    public function validateModel()
    {
        $this->validateTargets();
    }

    /**
     * Throw exception if any transitions specify targets
     * that do not exist in the model.
     *
	 * @return void
     * @throws model\ModelValidationException
    */
    public function validateTargets()
    {
        $valid_targets = array_keys($this->getTargets());

        // validate that all transitions target valid nodes in the model
        foreach ($this->transitions as $transition_list)
        {
            foreach ($transition_list as $transition)
            {
                $transition_event = $transition->getEvent();
				try
				{
					foreach ($transition->getTargets() as $target)
					{
						if ($target !== NULL && !in_array($target->getId(), $valid_targets))
						{
							throw new ModelValidationException(
									"Invalid target '{$target}' for " .
									"transition with event '{$transition_event}'"
							);
						}
					}
				}
				catch (ModelException $e)
				{
					throw new ModelValidationException(
						"Invalid target for " .
							"transition with event '{$transition_event}'. " . $e->getMessage()
					);
				}
            }
        }
    }

    public function __toString()
    {
        return $this->recursiveToString($this->scxml);
    }

    /**
     * @param model\CompoundNode $node
     * @param int $depth
     * @return string
     */
    private function recursiveToString(CompoundNode $node, $depth = 0)
    {
        $indent = str_repeat('    ', $depth);
        $ret = '';
        $ret .= $indent . (string) $node . PHP_EOL;
        foreach ($node->getChildren() as $child)
        {
            $ret .= $this->recursiveToString($child, $depth+1);
        }
        return $ret;
    }

}
