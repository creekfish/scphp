<?php

namespace org\funmonkeys\scphp\model;

/**
 *
 * @author bherring
 */
class Transition extends DocumentNode
{

    /**
     * Triggering event for this transition (if any).
     * @var Event
     */
    private $event;

    /**
     * Guard condition for this transition (if any).
     * @var Condition
     */
    private $condition;

    /**
     * List of targets (if any) for this transition in document order.
     * If more than one target is specified, they must belong to the
     * same parallel region.
     * @var TransitionTarget[]
     */
    private $targets;

    /**
     * Parent target of this transition.
     * @var TransitionTarget
     */
    private $parent;

    public function __construct()
    {
        $this->event = NULL;
        $this->condition = NULL;
        parent::__construct();
    }

    /**
     * Get the type of this transition.
     *
     * @see TransitionType
     *
     * @return TransitionType - Type of transition.
     */
    public function getType()
    {

    }

    /**
     * Get the trigger event for this transition (if any).  Note that
     * for the transition to be taken, the guard condition
     * must also be satisfied.
     *
     * @return Event - The event or NULL if no trigger event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get the guard condition (if any) for this transition.
     *
     * @return Condition - The condition or NULL if no guard condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Get list of targets for this transition (possibly none),
     * in document order.
     *
     * @return TransitionTarget[] - List of targets
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Get the parent target of this transition.
     *
     * @return TransitionTarget
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the trigger event for this transition. Set to NULL
     * if there is no event.
     *
     * @param Event $event - Trigger event for this transition, or NULL
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Set the guard condition for this transition. Set to NULL
     * if there is no condition.
     *
     * @param Condition $condition
     */
    public function setCondition(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Set list of targets for this transition.
     *
     * @param TransitionTarget[] $targets
     */
    public function setTargets(array $targets)
    {
        $this->targets = $targets;
    }

    /**
     * Add a target for this transition.
     *
     * @param TransitionTarget $target
     */
    public function addTarget(TransitionTarget $target)
    {
        $this->targets[$target->getDocumentOrder()] = $target;
    }

    /**
     * Set the parent target of this transition.
     *
     * @param TransitionTarget $parent
     */
    public function setParent(TransitionTarget $parent)
    {
        $this->parent = $parent;
    }

}
