<?php

namespace scphp\context;

/**
 *
 * @author bherring
 */
class NullContext implements \scphp\IContext
{
	/**
	 * Return the parent context of this one, if any.
	 *
	 * @return IContext - the parent context, or NULL if none
	 */
	public function getParent()
	{
		// TODO: Implement getParent() method.
	}

	/**
	 * Return an array of all the variables in this context
	 * indexed by name.
	 *
	 * @return array
	 */
	public function getVariables()
	{
		// TODO: Implement getVariables() method.
	}

	/**
	 * Get the value of a variable in this context.
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function get($name)
	{
		// TODO: Implement get() method.
	}

	/**
	 * Set the value of a variable in this context.
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function set($name, $value)
	{
		// TODO: Implement set() method.
	}

	/**
	 * Clear a variable from this context.
	 * @param $name
	 * @return void
	 */
	public function clear($name)
	{
		// TODO: Implement clear() method.
	}

	/**
	 * Clear all variables from this context.
	 * @return void
	 */
	public function clearAll()
	{
		// TODO: Implement clearAll() method.
	}

}
