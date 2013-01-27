<?php

namespace scphp;

use scphp\model\Scxml;
use scphp\model\CompoundNode;
use scphp\model\TransitionTarget;

/**
 *
 * @author bherring
 */
class Model
{
    /**
     * @var Scxml
     */
    private $scxml;

    /**
     * @var array of TransitionTarget
     */
    private $targets;

    /**
     * @var string
     */
    private $initial_id;


    public function __construct()
    {
        $this->scxml = new Scxml();
    }

    /**
     *
     * @param string $version the version attribute of the scxml node
     * @param string $initial_id the initial attribute of the scxml node
     */
    public function initScxml($version, $initial_id)
    {
        $this->scxml->setVersion($version);
        $this->initial_id = $initial_id;
    }

    /**
     *
     * @param model\CompoundNode $node
     * @param model\CompoundNode $parent
     * @return void
     */
    public function addNode(CompoundNode $node, CompoundNode $parent)
    {
        if ($node instanceof TransitionTarget)
        {
            // keep up with all transition targets
            $targets[] = $node;
            if ($node->getId() === $this->initial_id)
            {
                // set the inital state machine target node based on the scxml node initial attribute
                $this->scxml->setInitial($node);
            }
        }
        $parent->addChild($node);
        $node->setParent($parent);
    }

}
