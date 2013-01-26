<?php

namespace scphp\io;

use scphp\Model;
use scphp\model\ModelException;

/**
 *
 * @author bherring
 */
class Parser
{
    /**
     * @param string $xml - SCXML text to be parsed into model
     * @return Model
     * @throws ModelException
     */
    public function parse($xml)
    {
        $sxcml = simplexml_load_string($xml);


    }
}
