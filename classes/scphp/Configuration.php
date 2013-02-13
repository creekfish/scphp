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

	/**
	 * Array of atomic states for this configuration, indexed by doc order num
	 * to ensure states are not duplicated.
	 *
	 * @var array of model\State
	 */
	private $atomic_states;


	private $states_doc_order;

    public function addState(model\State $state)
    {
		if (array_key_exists($state->getDocumentOrder(), $this->states))
		{
			return;  // already have this node in the configuration
		}

/*
 *
 *  Refactor to handle parallels (all refs to "State" should be Targets).
 *
 *  Parallels will have ALL children added to config when they are
 *  added directly OR encountered as ancestor or descendant nodes.
 *  Do this in a separate addParallel() method if at ALL possible.
 *  Need to reduce cyclomatic complexity of this method!
 *
 * 	Also validate as we add nodes.  See notes below.  That makes sense - do not
 *  allow invalid config.  If an invalid node is encountered, be able to "roll back"
 *  any config changes from adding the requested node and then throw InvalidConfiguration exception!!!!
 *  Wow, I like this a lot! :)
 *
 */


		// add the state to this configuration
		$this->states[$state->getDocumentOrder()] = $state;

        // add all ancestor states to this configuration
        foreach ($state->getAncestors() as $ancestor)
        {
            $this->states[$state->getDocumentOrder()] = $ancestor;
        }

        if ($state->isComposite())
        {
			/** @var model\State $last_descendant  */
			$last_descendant = NULL;
            // add all descendant states to this configuration
            foreach ($state->getIntialDescendants() as $descendant)
            {
                $this->states[$state->getDocumentOrder()] = $descendant;
				$last_descendant = $descendant;
            }
			if ($last_descendant !== NULL)
			{
				$this->atomic_states[$last_descendant->getDocumentOrder()] = $last_descendant;
			}
        }
		else
		{
			$this->atomic_states[$state->getDocumentOrder()] = $state;
		}

		// rebuild the cached sorted doc order keys list
		$this->states_doc_order = sort(array_keys($this->states));
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
	 *
	 * @return Configuration a new configuration after all states have been entered.
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

	public function isValid()
	{
		/*
		 *
		 *
		 *
		 *
		 * OR we could do validation whenever a node is added...  and throw exception if validaiton fails (or just return FAIL_CODE, otherwise TRUE)
		 *
		 * I like that idea - validate as we go, and catch anything that breaks the validation.  But does that make sense?
		 * With the add function we have, CAN we even break validation???  Is it possible...  well, YES it is
		 * - example is we add two children for a STATE.  OR we don't add all children of a parallel state. OR multiple substates of
		 * STATE children of SCXML root.
		 *
		 *
		 *
		 *
		 */



		// MUST contain at least one atomic state
		if (count($this->atomic_states) < 1)
		{
			return FALSE;
		}

		foreach ($this->atomic_states as $atomic)
		{
			// follow ancestors of atomic state to scxml
			// each ancestor MUST be in configuration
			// each ancestor MUST have either only ONE child in config if state or ALL children in config if parallel
				// GOOD WAY TO COUNT THIS: cache all visited ancestors
					// 1) for states, they should ONLY be visited once and should be in config
					// 2) for parallels, they should be visited EXACTLY count(children) times and should be in config

			// should only have one scxml child...

			//
		}

		// contains exactly one scxml child

		// contains all ancestors (states or parallel) for every atomic state

		// contains one and only one child of each non-atomic state

		// contains all children of each parallel

	}
}
