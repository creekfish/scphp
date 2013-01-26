<?php

namespace org\funmonkeys\scphp\model;

/**
 *
 * @author bherring
 */
class State extends CompoundTarget
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return TRUE if this is a final state (no triggerable transitions)
     *
     * @return boolean
     */
    public function isFinal()
    {
        return count($this->getChildren()) == 0;
    }

    /**
     * Return TRUE if this is a simple state (a leaf)
     *
     * @return boolean
     */
    public function isSimple()
    {
        return count($this->getChildren()) == 0;
    }

    /**
     * Return TRUE if this is a composite state (a non-leaf, has child states)
     *
     * @return boolean
     */
    public function isComposite()
    {
        return count($this->getChildren()) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function isRegion()
    {

    }

    /**
     *
     * @return boolean
     */
    public function isOrthogonal()
    {

    }

    /**
     *
     * @return Invoke
     */
    public function getInvoke()
    {

    }

    /**
     *
     * @param boolean $value
     */
    public function setFinal($value)
    {

    }

    /**
     *
     * @param Invoke $invoke
     */
    public function setInvoke(Invoke $invoke)
    {

    }
}
