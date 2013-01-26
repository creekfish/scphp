<?php

namespace org\funmonkeys\scphp\io;

/**
 *
 * @author bherring
 */
class Parser
{
    /**
     * @param string $xml - SCXML text to be parsed into model
     * @return org\funmonkeys\scphp\Model
     * @throws org\funmonkeys\scphp\model\ModelException
     */
    public function parse($xml)
    {
        $sxcml = simplexml_load_string($xml);


    }
}
