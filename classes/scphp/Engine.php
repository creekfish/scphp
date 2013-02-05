<?php

namespace scphp;

/**
 *
 * @author bherring
 */
class Engine
{
    /**
     * Take next macro-step triggered by event.
     *
     * @param model\Event $event - external event
     * @throws ModelException
     */
    public function takeNextStep(model\Event $event)
    {
        while ($this->stateConfiguration.hasEvents())
        {
            // selectTransitions

            // filterPreempted

            // if transition(s) selected
                // microstep



        }
    }


    /**
     * Select transitions from the current configuration
     * (of active states) that are enabled by the given
     * event.
     *
     * @param model\Event $event
     * @return model\Transition $transition
     */
    public function selectTransitions(Model $model, model\Event $event)
    {
        $step->getConfiguration()->getStates();
    }


    /**
     *
     *
     * @param model\TransitionList $enabledTransitions
     */
    protected function takeNextMicrostep(model\TransitionList $enabledTransitions)
    {

    }

    protected function exitStates(model\TransitionList $enabledTransitions)
    {

    }

    protected function enterStates(model\TransitionList $enabledTransitions)
    {

    }


    public function findLeastCommonCompoundAncestor(model\StateList )
    {

    }
}
