<?php

namespace scphp;

use scphp\model\Event;
use scphp\model\Parallel;
use scphp\model\State;
use scphp\model\Transition;
use scphp\model\TransitionTarget;


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
     * Array of states (both "regular" and parallel) in this
	 * configuration, indexed by doc order num to ensure states
	 * are not duplicated.
     *
     * @var array of TransitionTarget
     */
    private $states;

	/**
	 * Array of atomic states for this configuration, indexed by doc order num
	 * to ensure states are not duplicated.
	 *
	 * @var array of States
	 */
	private $atomic_states;

	public function __construct()
	{
		$this->states = array();
		$this->atomic_states = array();
	}

    public function addState(TransitionTarget $state)
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
		/** @var TransitionTarget $ancestor */
        foreach ($state->getAncestors() as $ancestor)
        {
			if ($ancestor instanceof TransitionTarget)
			{
	            $this->states[$ancestor->getDocumentOrder()] = $ancestor;
			}
        }

		if ($state->isComposite())
		{
			// add all initial descendant states to the configuration
			// (those entered upon entering the state, without following any transitions)
			$descendants = $state->getInitialDescendants();
			/** @var TransitionTarget $descendant */
			foreach ($descendants as $descendant)
			{
				if (!$descendant->isComposite())
				{
					$this->atomic_states[$descendant->getDocumentOrder()] = $descendant;
				}
			}
			$this->states = $this->states + $descendants;  // array union
		}
		else
		{
			$this->atomic_states[$state->getDocumentOrder()] = $state;
		}

		// sort the state lists
		ksort($this->states);
		ksort($this->atomic_states);
    }

	/**
	 * @param array $states TransitionTargets to add to this configuration.
	 */
	public function addStates(array $states)
	{
		/** @var TransitionTarget $state */
		foreach ($states as $state)
		{
			$this->addState($state);
		}
	}

//	private function addDescendantsRecursive(TransitionTarget $state)
//	{
//		if ($state instanceof State)
//		{
//			if ($state->isComposite())
//			{
//				/** @var State $last_descendant  */
//				$last_descendant = NULL;
//				// add all descendant states to this configuration
//				foreach ($state->getIntialDescendants() as $descendant)
//				{
//					$this->states[$state->getDocumentOrder()] = $descendant;
//					$last_descendant = $descendant;
//				}
//				if ($last_descendant !== NULL)
//				{
//					$this->atomic_states[$last_descendant->getDocumentOrder()] = $last_descendant;
//				}
//			}
//			else
//			{
//				$this->atomic_states[$state->getDocumentOrder()] = $state;
//			}
//		}
//		else if ($state instanceof Parallel)
//		{
//			// add all parallel child states to the configuration
//			foreach ($state->getTargetChildren() as $child)
//			{
//				// add child to the configuration
//				$this->states[$state->getDocumentOrder()] = $child;
//				$this->addDescendantsRecursive($child);
//			}
//		}
//	}

    /**
     * Select transitions from this configuration
     * (of active states) that are enabled by the given
     * event.  Preempt parallel transitions that conflict -
	 * that target incompatible states that cannot
	 * coexist in the same configuration.
     *
     * @param model\Event $event
     * @return array of Transition $transition
     */
    public function selectTransitions(Event $event)
    {
		$selected = array();

		/** @var State $state */
		foreach ($this->states as $state)  // all active states in this configuration
		{
			/** @var Transition $transition */
			foreach ($state->getTransitions() as $transition)
			{
				if ($transition->isEnabledByEvent($event))
				{
					$selected[$transition->getDocumentOrder()] = $transition;
					// select only first (in document order) enabled transition for this state
					break;
				}
			}
		}
		$selected = $this->preemptTransitions($selected);
		ksort($selected);

		return $selected;
    }

	/**
	 * @param array of Transition $transitions
	 */
	private function preemptTransitions($transitions)
	{
//		/** @var Transition $transition */
//		foreach ($transitions as $transition)
//		{
//		}
		return $transitions;
	}

    /**
     * Enter all states in this configuration.
	 *
	 * @return Configuration a new configuration after all states have been entered.
     */
    public function enterStates()
    {
		//TODO this is the last part of enterStates() proceedure in W3C SCXML example algorithm...

    }

    /**
     * Exit all states in this configuration.
     */
    public function exitStates()
    {
		//TODO this is the last part of exitStates() proceedure in W3C SCXML example algorithm...


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

	/**
	 * @return array
	 */
	public function getAtomicStates()
	{
		return $this->atomic_states;
	}

	/**
	 * @return array
	 */
	public function getStates()
	{
		return $this->states;
	}

	public function __toString()
	{
		$ret = '';
		foreach ($this->getStates() as $state)
		{
			$ret .= (string) $state . '; ';
		}
		return $ret;
	}
}
