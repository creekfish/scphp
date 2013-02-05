<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class TransitionList extends \ArrayIterator
{

    /**
     * @param Transition $t
     * @return void
     */
    public function append(Transition $t)
    {
        var_dump($t);
        parent::append($t);
    }

    /**
     * @return Transition
     */
    public function current()
    {
        return parent::current();
    }

}
