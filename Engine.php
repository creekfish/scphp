<?php

namespace org\funmonkeys\scphp;

/**
 *
 * @author bherring
 */
class Engine
{
    /**
     * Take next macro-step triggered by event.
     *
     * @param org\funmonkeys\scphp\model\Event $event - external event
     * @throws ModelException
     */
    public function takeNextStep(org\funmonkeys\scphp\model\Event $event)
    {
        while ($this->stateConfiguration.hasEvents())
        {
            // selectTransitions

            // filterPreempted

            // if transition(s) selected
                // microstep



        }
    }

}
