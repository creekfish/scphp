<?php

namespace scphp\engine;

use scphp\Configuration;
use scphp\model\Event;
use scphp\model\Transition;

/**
 * A microstep in the state machine, which when executed processes the next event
 * in the event queue, resulting in a possibly new configuration and additional
 * events in the queue.
 *
 * @author bherring
 */
class EventQueue
{
	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var EventQueue
	 */
	private $event_queue;

	/**
	 * @var bool
	 */
	private $is_done;

	public function __construct(Configuration $configuration, EventQueue $event_queue)
	{
		$this->configuration = $configuration;
		$this->event_queue = $event_queue;
		$this->is_done = FALSE;
	}

	/**
	 * Execute this microstep, producing a new configuration and possibly additional event queue entries
	 */
	public function executeStep()
	{
		// fetch the next event from the event queue
		$event = $this->event_queue->nextEvent();

		// fetch the list of transitions enabled by the event
		$enabled_transitions = $this->configuration->selectTransitions($event);

		// exit states in the current configuration
		$this->configuration->exitStates();

		// execute all executable content in enabled transitions
		$this->executeTransitionContent($enabled_transitions);

		// follow all enabled transitions to get to the next configuration
		$this->followTransitions($enabled_transitions);

		// enter all states in the next configuration
		$this->configuration->enterStates();

		//FIXME The current config should NOT be all exited - instead this class should calc which states should be
		//FIXME exited and entered based on the config and transitions, right?
		//FIXME So it goes: calc states to be exited, exit them, execute trans content, calc states to be entered, enter them

		// this step is completely executed
		$this->is_done = TRUE;
	}

	private function executeTransitionContent(array $transitions)
	{
		/** @var Transition $transition */
		foreach ($transitions as $transition)
		{
			$transition->executeContent();
		}
		//TODO can executable content trigger new events that would need to be processed before microstep is done?  I think not, but might add to overall event queue?
	}

	private function followTransitions(array $transitions)
	{
		//TODO this is the first part of enterStates() proceedure in W3C SCXML example algorithm...

	}

	/**
	 * @return bool
	 */
	public function isDone()
	{
		return $this->is_done;
	}

	/**
	 * @return \scphp\Configuration
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @return \scphp\engine\EventQueue
	 */
	public function getEventQueue()
	{
		return $this->event_queue;
	}
}
