<?php

namespace scphp;

/**
 * A set of active states (generally the current set).
 * This class enforces requirements of a legal configuration:
 * - The configuration contains exactly one child of the <scxml> element.
 * - The configuration contains one or more atomic states.
 * - When the configuration contains an atomic state,
 *   it contains all of its <state> and <parallel> ancestors.
 * - When the configuration contains a non-atomic <state>, it contains one
 *   and only one of the state's children.
 * - If the configuration contains a <parallel> state, it contains all of
 *   its children.
 *
 * @author bherring
 */
class Configuration
{
    /**
     * Array of states for this configuration, indexed by doc order num
     * to ensure states are not duplicated.
     *
     * @var array of model\State
     */
    private $states;

    public function addState(model\State $state)
    {
        // add all ancestor states to this configuration
        foreach ($state->getAncestors() as $ancestor)
        {
            $this->states[$state->getDocumentOrder()] = $ancestor;
        }

        // add the state to this configuration
        $this->states[$state->getDocumentOrder()] = $state;

        if ($state->isComposite())
        {
            // add all descendant states to this configuration
            foreach ($state->getIntialDescendants() as $descendant)
            {
                $this->states[$state->getDocumentOrder()] = $descendant;
            }
        }
    }

    /**
     * Select transitions from this configuration
     * (of active states) that are enabled by the given
     * event.
     *
     * @param model\Event $event
     * @return array of model\Transition $transition
     */
    public function selectTransitions(model\Event $event)
    {

    }


    /**
     * Enter all states in this configuration.
     */
    public function enterStates()
    {

    }

    /**
     * Exit all states in this configuration.
     */
    public function exitStates()
    {

    }
}
