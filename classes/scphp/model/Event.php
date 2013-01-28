<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Event extends DocumentNode
{
    /**
     * Name of the event.
     * @var string
     */
    private $name;

    /**
     * Additional context for this event.  Could be anything passed along with the event.
     * @var array
     */
    private $context;


    public function __construct($name)
    {
        $this->name = $name;
        $this->context = NULL;
    }

    /**
     * Set the name of this event.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of this event.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set additional context for this event (if any).
     *
     * @param array $context
     * @return void
     */
    public function setExtraContext(array $context)
    {
        $this->context = $context;
    }

    /**
     * Get the additional context for this event (if any).
     *
     * @return array
     */
    public function getExtraContext()
    {
        return $this->context;
    }
}
