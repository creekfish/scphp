<?php

namespace scphp;

use scphp\model\Event;

/**
 *
 * @author bherring
 */
class Engine
{
	/**
	 * @var array of Event $event_queue
	 */
	public $event_queue;

	/**
	 * @var Configuration $configuration
	 */
	public $configuration;

	/*
	 * @var boolean $running
	 */
	public $running;

	public function start()
	{
		$this->running = TRUE;

		// stablilze the machine initially
	}

	public function stop()
	{
		$this->running = FALSE;
	}

	public function isRunning()
	{
		return $this->running;
	}

	/**
	 * @param Event $event
	 */
	public function injectExternalEvent(Event $event)
	{
		array_push($this->event_queue, $event);
	}

	protected function processQueuedEvents()
	{
		while ($this->hasNextEvent())
		{
			$this->configuration = $this->takeNextMacroStep($this->configuration, $this->pullNextEvent());
		}
	}

	/**
	 * Take next macrostep triggered by event.
	 *
	 * @param Event $event - external event that triggers this macrostep
	 * @return Configuration
	 * @throws ModelException
	 */
	public function takeNextMacroStep(Configuration $configuration, Event $event)
	{
		$transitions = $configuration->selectTransitions($event);

		$new_configuration = $configuration;
		if (!empty($transitions))  // if this is not an empty macrostep
		{
			$new_configuration = $this->takeNextMicrostep($configuration, $event);
			$new_configuration = $this->stabilize($new_configuration);
		}
		return $new_configuration;
	}

	/**
	 * Process events triggered by eventless transitions or transitions
	 * enabled by internal events until no more are left, leaving the
	 * machine in a "stable" - no further changes until an external event
	 * arrives.  This generally occurs when the state machine is
	 * initialized for execution (before the first external event
	 * is processed) and after each external event is processed
	 * and a microstep is taken.  Stabilization consists of taking a
	 * series of microsteps and is the final part of a macrostep.
	 *
	 *  @return Configuration
	 */
	protected function stabilize(Configuration $configuration)
	{
		/** @var array of Event $internal_event_queue */
		$internal_event_queue = array(new Event(NULL));  // start off with eventless transitions (taken in every microstep)

		// keep taking microsteps until there are no more internal events
		while (!empty($internal_event_queue))
		{
			$internal_event = array_pop($internal_event_queue);
			$configuration = $this->takeNextMicrostep($configuration, $internal_event);
			$internal_event_queue = ???
/*
  *fixme Each microstep results in a new configuration of states and a new internal event queue, the later consisting of the NULL event and all internal events added by the microstep...
  *fixme SO we need to get the configuration AND events from the microstep back.  Microstep should be an object with those two properties...?
  *fixme or better yet, maybe MicrostepResult is the object returned from taking a microstep?
  */

		}
	}

    /**
     *
     *
     * @param model\TransitionList $enabledTransitions
	 * @return Configuration
	 *
     */
    protected function takeNextMicrostep(Configuration $configuration)
    {

    }

    protected function exitStates(model\TransitionList $enabledTransitions)
    {

    }

    protected function enterStates(model\TransitionList $enabledTransitions)
    {

    }

	/**
	 * @return Event
	 */
	protected function pullNextEvent()
	{
		return array_pop($this->event_queue);
	}

	/**
	 * @return boolean
	 */
	protected function hasNextEvent()
	{
		return count($this->event_queue) > 0;
	}

}
