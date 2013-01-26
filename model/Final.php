<?php

namespace org\funmonkeys\scphp\model;

/**
 *
 * @author bherring
 */
class FinalState extends State
{

    public function addTransition($transition)
    {
        throw ModelException('Cannot add transition to Final');
    }

    public function addChild($child)
    {
        throw ModelException('Cannot add child state to Final');
    }
}
