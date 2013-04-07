<?php

namespace scphp\model;

/**
 * Base class for nodes that contain zero or more transitions
 * leading to nodes (targets). Examples are state, initial, final, and
 * parallel elements.
 *
 * @author bherring
 */
abstract class TransitionContainer extends CompoundNode
{
    /**
     * Transitions from this node to other nodes in document order.
     * @var Transition[]
     */
    private $transitions;

    public function __construct($id = NULL, $doc_order = NULL)
    {
        $this->transitions = array();
        parent::__construct($id, $doc_order);
    }

    /**
     * Add a transition to the list of transitions from this node.
     *
     * @param Transition $transition - Transition to be added.
     * @return void
     */
    public function addTransition(Transition $transition)
    {
        $this->transitions[] = $transition;
        $transition->setParent($this);
    }

    /**
     * Get a list of transitions that can be triggered by the given event,
     * in document order.  Note that the event may be triggered but still
     * not taken if the guard condition is not satisfied.  List is
     * keyed by node id for fast lookup by node.
     *
     * @see Transition
     * @see Condition
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
     * Return the first transition for this node.
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
