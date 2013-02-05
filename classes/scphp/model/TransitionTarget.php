<?php

namespace scphp\model;

/**
 * Base class for all transition targets, nodes targeted
 * by transitions that also contain zero or more transitions
 * to other targets. Examples are state and parallel elements.
 *
 * @author bherring
 */
abstract class TransitionTarget extends CompoundNode
{
    /**
     * Transitions from this target to other targets in document order.
     * @var Transition[]
     */
    private $transitions;

    public function __construct()
    {
        $this->transitions = array();
        parent::__construct();
    }

    /**
     * Add a transition to the list of transitions from this target.
     *
     * @param Transition $transition - Transition to be added.
     * @return void
     */
    public function addTransition(Transition $transition)
    {
        $this->transitions[$transition->getDocumentOrder()] = $transition;
        $transition->setParent($this);
    }

    /**
     * Remove a transition from the list of transitions from this target.
     *
     * @param Transition $transition - Transition to remove.
     * @return void
     */
    public function removeTransition(Transition $transition)
    {
        unset($this->transitions[$transition->getDocumentOrder()]);
        $transition->setParent(NULL);
    }

    /**
     * Get a list of transitions that can be triggered by the given event,
     * in document order.  Note that the event may be triggered but still
     * not taken if the guard condition is not satisfied.  List is
     * keyed by transition target id for fast lookup by target.
     *
     * @see Transition
     * @see Condition
     *
     * @param Event $event Triggering event or NULL if all transitions should be returned.
     * @return Transition[] List of transitions that can be triggered by the the given event.
     */
    public function getTransitions($event)
    {
        if ($event === NULL) {
            // return all transitions
            return array_values($this->transitions);
        }
        $name = $event->getName();
        $events = array();
        foreach ($this->transitions as $transition) {
            if ($transition->getEvent()->getName() === $name) {
                $events[] = $transition;
            }
        }
        return $events;
    }


    /**
     * Return the first transition for this target.
     *
     * @return Transition The first transition | NULL if no transitions
     */
    public function getFirstTransition()
    {
        if (isset($this->transitions) && count($this->transitions) > 0)
        {
            $first_array = array_slice($this->transitions,0,1);
            return array_shift($first_array);
        }
        return NULL;
    }

}
