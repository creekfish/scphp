<?php

namespace org\funmonkeys\scphp;

/**
 *
 * @author bherring
 */
interface IContext
{
    /**
     * Return the parent context of this one, if any.
     *
     * @return IContext - the parent context, or NULL if none
     */
    public function getParent();

    /**
     * Return an array of all the variables in this context
     * indexed by name.
     *
     * @return array
     */
    public function getVariables();

    /**
     * Get the value of a variable in this context.
     *
     * @param string $name
     * @return mixed
     */
    public function get($name);

    /**
     * Set the value of a variable in this context.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function set($name, $value);

    /**
     * Clear a variable from this context.
     * @param $name
     * @return void
     */
    public function clear($name);

    /**
     * Clear all variables from this context.
     * @return void
     */
    public function clearAll();
}
