<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Transition extends ExecutableNodeContainer
{
	const EVENT_DESCRIPTOR_SEPARATOR = ' ';

    /**
     * Triggering event(s) for this transition (if any).
     * @var array of Event
     */
    private $events;

    /**
     * Guard condition for this transition (if any).
     * @var Condition
     */
    private $condition;

    /**
     * List of target ids (if any) for this transition in document order.
     * If more than one target is specified, they must belong to the
     * same parallel region.
     * @var array of string
     */
    private $target_ids;

	/**
	 * The event attribute string for the transition element.
	 * @var string
	 */
	private $event_attribute;

    /**
     * Constructor
     *
     * @param string $target_id Single target id (default is NULL for no target)
     * @param string $event_attribute Event attribute for this transition node or NULL if no events
     * @param Condition $condition Guard condition for this transition or NULL if no conition
     */
    public function __construct($target_id = NULL, $event_attribute = NULL, Condition $condition = NULL)
    {
		$this->setEvent($event_attribute);
        $this->condition = $condition;
        $this->target_ids = array();
        if (isset($target_id) && is_string($target_id))
        {
            $this->addTarget($target_id);
        }
        parent::__construct();
    }

    /**
     * Get the type of this transition.
     *
     * @see TransitionType
     *
     * @return TransitionType Type of transition.
     */
    public function getType()
    {

    }

    /**
     * Get the triggering events for this transition (if any).  Note that
     * for the transition to be taken, the guard condition
     * must also be satisfied.
     *
     * @return array of Event The events or empty array if no triggering events
     */
    public function getEvents()
    {
        return $this->events;
    }

	/**
	 * Get the event attribute string for this event.
	 * @return string
	 */
	public function getEventAttribute()
	{
		return $this->event_attribute;
	}

    /**
     * Get the guard condition (if any) for this transition.
     *
     * @return Condition The condition or NULL if no guard condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set the trigger events for this transition, based on the
	 * event attribute in the transition node.
     *
     * @param string $event_attribute Trigger event attribute for this transition, or NULL if no event attribute
     */
    public function setEvent($event_attribute)
    {
		if (empty($event_attribute))
		{
			$this->event_attribute = NULL;
			$this->events = array();
			return;
		}
		$this->event_attribute = $event_attribute;
		foreach (explode(self::EVENT_DESCRIPTOR_SEPARATOR, $event_attribute) as $descriptor)
		{
			$this->events[] = new Event($descriptor);
		}
    }

    /**
     * Set the guard condition for this transition. Set to NULL
     * if there is no condition.
     *
     * @param string $condition_expression_text Text of the expression for the guard condition
     */
    public function setCondition($condition_expression_text)
    {
        $this->condition = new Condition(new Expression($condition_expression_text));
    }

	/**
	 * Return true if this transition is enabled by the given event.
	 * See http://www.w3.org/TR/scxml/#SelectingTransitions (section 3.13)
	 * for more info.
	 *
	 * @param Event $match_event The event this transitions event(s) must match to be enabled
	 * @return boolean
	 */
	public function isEnabledByEvent(Event $match_event)
	{
		if ($this->isTriggeredByEvent($match_event))  // if event can trigger (is a match)
		{
			// also must either not have condition or condition evaluates to true
			if (empty($this->condition) || $this->condition->isTrue())
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Return true if this transition can be triggered by the given event.
	 * Does not consider the guard condition, which, if it evaluates to
	 * FALSE may still cause the transtion not the be enabled in the
	 * current configuration.
	 * See http://www.w3.org/TR/scxml/#SelectingTransitions (section 3.13)
	 * for more info.
	 *
	 * @see isEnabledByEvent
	 *
	 * @param Event $match_event The event this transitions event(s) must match to be enabled, or NULL if match
	 * 			transition with no event.
	 * @return boolean
	 */
	public function isTriggeredByEvent(Event $match_event)
	{
		$matches = FALSE;
		if (!empty($this->events))
		{
			// must match the event name
			/** @var Event $my_event */
			foreach ($this->events as $my_event)
			{
				if ($my_event->matches($match_event))
				{
					$matches = TRUE;
					break;
				}
			}
		}
		else
		{
			if ($match_event === NULL)
			{
				$matches = TRUE;  // no event attribute specified, depends only on condition
			}
		}
		return $matches;
	}

    /**
     * Set list of targets for this transition.
     *
     * @param array of string $target_id_list
     */
    public function setTargets(array $target_id_list)
    {
        $this->target_ids = $target_id_list;
    }

    /**
     * Add a target id for this transition.
     *
     * @param string $target_id
     * @return void
     * @throws ModelException
     */
    public function addTarget($target_id)
    {
        if (empty($target_id))
        {
            throw new ModelException('Transition target id must not be empty.');
        }
        $this->target_ids[] = $target_id;
    }

    /**
     * Add a target or multiple targets (if in parallel region) for this transition
     * based on the "targets" attribute.
     *
     * @param string $target_id Id (or list of ids) of the target for this transition
     * @return void
     * @throws ModelException
     */
    public function setTarget($target_id)
    {
        if (empty($target_id))
        {
            throw new ModelException('Transition target attribute must not be empty.');
        }
        if (!is_string($target_id))
        {
            throw new ModelException('Transition target attribute must be string.');
        }
        foreach (explode(' ', $target_id) as $id)
        {
            $this->addTarget($id);
        }
    }

    /**
     * Get list of target ids for this transition (possibly none),
     * in document order.
     *
     * @return array of string List of target ids
     */
    public function getTargetIds()
    {
        return $this->target_ids;
    }

	/**
	 * Get list of targets for this transition (possibly none),
	 * in document order.
	 *
	 * @return array of TransitionTarget List of targets
	 */
	public function getTargets()
	{
		$targets = array();
		foreach ($this->getTargetIds() as $id)
		{
			$targets[] = $this->getTarget($id);
		}
		return $targets;
	}

    /**
     * Get target for this transition that matches the given id.
     *
	 * @param string $target_id
     * @return TransitionContainer Target matching the given id || NULL if no target matches
     * @throws ModelException
     */
    public function getTarget($target_id)
    {
        if (in_array($target_id, $this->target_ids))
        {
            $model = $this->getModel();
            if (!isset($model))
            {
                throw new ModelException("Model not specified for transition; cannot find target.");
            }
            $target = $model->getTarget($target_id);
			if (!isset($target))
			{
				throw new ModelException("Transition target '{$target_id}' does not exist in model.");
			}
            return $target;
        }
        throw new ModelException("Transition target '{$target_id}' not valid for transition.");
    }

    /**
     * Return the first target node for this transition (first in the list of target ids).
     *
     * @return TransitionContainer The first target node | NULL if no targets
     * @throws ModelException
     */
    public function getFirstTarget()
    {
        if (isset($this->target_ids) && count($this->target_ids) > 0)
        {
            $first_array = array_slice($this->target_ids,0,1);
            return $this->getTarget(array_shift($first_array));
        }
        return NULL;
    }

	/**
	 * Return TRUE if the provided node is a valid parent node type for this node.
	 *
	 * @param CompoundNode $parent
	 * @return boolean
	 */
	public function isValidParent(CompoundNode $parent)
	{
		return ($parent instanceof TransitionContainer);
	}

    public function __toString()
    {
        $cond = ($this->getCondition() !== NULL) ? $this->getCondition()->getExpression() : 'NULL';
        $event = ($this->getEventAttribute() !== NULL) ? $this->getEventAttribute() : 'NULL';
        $target_str = implode(',' , $this->getTargetIds());
        return parent::__toString() . '; event:' . $event . '; cond:' . $cond . '; targets:' . $target_str;
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
