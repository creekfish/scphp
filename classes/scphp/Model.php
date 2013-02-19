<?php

namespace scphp;

use scphp\model\CompoundNode;
use scphp\model\Event;
use scphp\model\Scxml;
use scphp\model\Transition;
use scphp\model\TransitionContainer;
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
		$this->targets = array();
		$this->transitions = array();
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
        if ($node instanceof TransitionContainer)
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
     * @param \scphp\model\TransitionContainer $target
     * @return void
     */
    public function addTarget(TransitionContainer $target)
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
        $this->transitions[] = $transition;
    }

    /**
     * Return a list of all Transitions in this model
     * that can be triggered by the given event.
     *
	 * @param Event $event Triggering event or NULL if all transitions should be returned.
	 * @return array of Transition List of transitions that can be triggered by the the given event.
     */
    public function getTransitions(Event $event = NULL)
    {
		if ($event === NULL) {
			// return all transitions
			return array_values($this->transitions);
		}
		$event_trans = array();
		/** @var Transition $transition */
		foreach ($this->transitions as $transition) {
			if ($transition->isTriggeredByEvent($event)) {
				$event_trans[] = $transition;
			}
		}
		return $event_trans;
	}

    /**
     * Return a list of all event names that can trigger
     * transitions in this model.
     * NOTE: NULL and "" are valid event names
	 * NOTE: ordering of list is indeterminate
     *
     * @return array of Event Event list keyed by event descriptors
     */
    public function getTriggers()
    {
		$triggers = array();
		/** @var Transition $transition */
        foreach ($this->transitions as $transition)
		{
			/** @var Event $event */
			foreach ($transition->getEvents() as $event)
			{
				// NOTE: keying by event descriptor eliminates duplicated triggers
				$triggers[$event->getDescriptor()] = $event;
			}
		}
		return $triggers;
    }

    /**
     * Return the TransitionTarget that matches the given
     * transition target id.
     *
     * @param string $target_id
     * @return TransitionContainer
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
		/** @var Transition $transition */
		foreach ($this->transitions as $transition)
		{
			$transition_event_attr = $transition->getEventAttribute();
			try
			{
				/** @var TransitionTarget $target */
				foreach ($transition->getTargets() as $target)
				{
					if ($target !== NULL && !in_array($target->getId(), $valid_targets))
					{
						throw new ModelValidationException(
								"Invalid target '{$target}' for " .
								"transition with event '{$transition_event_attr}'"
						);
					}
				}
			}
			catch (ModelException $e)
			{
				throw new ModelValidationException(
					"Invalid target for " .
						"transition with event '{$transition_event_attr}'. " . $e->getMessage()
				);
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
