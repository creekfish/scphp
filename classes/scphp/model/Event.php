<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Event
{
	const DESCRIPTOR_TOKEN_SEPARATOR = '.';

    /**
     * Event descriptor, consisting of one or more tokens separated by '.'.
     * @var string
     */
    private $descriptor;

    /**
     * Additional context for this event.  Could be anything passed along with the event.
     * @var array
     */
    private $context;


    public function __construct($descriptor)
    {
        $this->descriptor = $descriptor;
        $this->context = NULL;
    }

    /**
     * Set the descriptor of this event.
     *
     * @param string $name
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Get the descriptor for this event as a string.
     *
     * @return string
     */
    public function getDescriptor()
    {
        return $this->descriptor;
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

	/**
	 * Return true if this event's descriptor matches the specified event's name.
	 * Each event attribute is a descriptor, which can match event names that
	 * are passed to the scxml instance.
	 *
	 * See http://www.w3.org/TR/scxml/#EventDescriptors (section 3.12.1)
	 * for more info.
	 *
	 *TODO test this and transtion isEnabledByEvent() against respective specs... with UNIT tests!
	 *
	 * @param Event $event The event (name) to match.
	 * @return boolean
	 */
	public function matches($event)
	{
		if ($this->getDescriptor() === '*')  // if wildcard match
		{
			return TRUE;
		}

		$my_tokens = $this->getDescriptorTokens();
		$match_tokens = $event->getDescriptorTokens();

		$my_token_count = count($my_tokens);
		if ($my_token_count > count($match_tokens))  // if this event has more tokens than match event
		{
			return FALSE;  // no way this can match
		}

		for ($i = 0 ; $i < $my_token_count ; $i++)
		{
			if ($my_tokens[$i] !== $match_tokens[$i])  // this token doesn't match
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Break the descriptor for this event into tokens.
	 *
	 * @return array of string list of the tokens in order they appear in the descriptor
	 */
	protected function getDescriptorTokens()
	{
		$tokens =  explode(self::DESCRIPTOR_TOKEN_SEPARATOR, $this->getDescriptor());
		$last_token = $tokens[count($tokens) - 1];
		if (empty($last_token) || $last_token === '*')
		{
			// if the last token is empty or wildcard
			array_pop($tokens);  // make it the equivalent of not having that token
		}
		return $tokens;
	}

    public function __toString()
    {
        return $this->getDescriptor();
    }
}
