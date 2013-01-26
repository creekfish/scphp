<?php

namespace org\funmonkeys\scphp\model;

/**
 *
 * @author bherring
 */
class Scxml extends CompoundTarget
{

    /**
     * SCXML node has no parent.
     *
     * @return NULL
     */
    public function getParent()
    {
        return NULL;
    }

    /**
     * No parent node can be set for SCXML node.
     * @throws ModelException
     */
    public function setParent(CompoundTarget $parent)
    {
        throw new ModelException('Cannot set parent of top-level SCXML node.');
    }

    /**
     * Add a transition to the list of transitions from this target.
     *
     * @param Transition $transition - Transition to be added.
     * @return void
     */
    public function addTransition(Transition $transition)
    {

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
     * @param Event $event - Triggering event or NULL if all transitions should be returned.
     * @return Transition[] - List of transitions that can be triggered by the the given event.
     */
    public function getTransitions(Event $event)
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
}
