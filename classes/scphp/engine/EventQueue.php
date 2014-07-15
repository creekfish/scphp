<?php

namespace scphp\engine;

use scphp\model\Event;

/**
 * Queue of model events.
 *
 * @author bherring
 */
class EventQueue
{
	/**
	 * @var array
	 */
	private $events;

	public function __construct()
	{
		$this->events = array();
	}

	/**
	 * @param \scphp\model\Event $event
	 */
	public function addEvent(Event $event)
	{
		$this->events[] = $event;
	}

	/**
	 * @return \scphp\model\Event
	 */
	public function nextEvent()
	{
		return array_shift($this->events);
	}

	/**
	 * @return bool
	 */
	public function hasEvents()
	{
		return count($this->events) > 0;
	}
}
