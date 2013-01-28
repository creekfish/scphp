<?php

namespace scphp;

use scphp\model\Scxml;
use scphp\model\CompoundNode;
use scphp\model\TransitionTarget;
use \scphp\model\ModelValidationException;

/**
 *
 * @author bherring
 */
class Model
{
    /**
     * @var int
     */
    private $doc_order;

    /**
     * @var Scxml
     */
    private $scxml;

    /**
     * @var array of TransitionTarget
     */
    private $targets;


    public function __construct()
    {
        $this->doc_order = 0;
        $this->scxml = new Scxml();
    }

    /**
     *
     * @param model\CompoundNode $node
     * @param model\CompoundNode $parent
     * @return void
     */
    public function addNode(CompoundNode $node, CompoundNode $parent = NULL)
    {
        $node->setDocumentOrder($this->doc_order++);
        $node->setModel($this);
        if ($node instanceof TransitionTarget)
        {
            // keep up with all transition targets
            $this->addTarget($node);
        }
        if ($parent === NULL)
        {
            // this is the "parent" for the SCXML doc node
            $this->scxml = $node;
        }
        else
        {
            $parent->addChild($node);
        }
    }

    /**
     * @param \scphp\model\TransitionTarget $target
     */
    public function addTarget(TransitionTarget $target)
    {
        $idx = $target->getId();
        if (empty($idx))
        {
            // ensure valid index for target without id (cannot be referenced in transitions)
            $idx = '__TID__' . count($this->targets);
        }
        $this->targets[$idx] = $target;
    }

    /**
     * Return a list of all TransitionTargets for this model.
     *
     * @return array of TransitionTarget
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Return the TransitionTarget that matches the given
     * transition target id.
     *
     * @param string $target_id
     * @return TransitionTarget
     */
    public function getTarget($target_id)
    {
        return $this->targets[$target_id];
    }

    /**
     * Return TRUE if the target id is a valid transiton
     * target for this model.
     *
     * @param string $target_id
     * @return boolean
     */
    public function isTarget($target_id)
    {
        if (!isset($this->targets[$target_id]))
        {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Throw exception if the finalized model fails validation.
     *
     * @throws model\ModelValidationException
     */
    public function validateModel()
    {
        $this->validateTargets();
    }

    /**
     * Throw exception if any transitions specify targets
     * that do not exist in the model.
     *
     * @throws model\ModelValidationException
    */
    public function validateTargets()
    {
        // validate that all transitions target valid nodes in the model

        // traverse the model to check all transitions

//        throw new ModelValidationException("Model validation failed: " .
//                "invalid transition target '{$target_id}' for '{$transition_id}'"
//        );
    }

    public function __toString()
    {
        return $this->recursiveToString($this->scxml);
    }

    /**
     * @param model\CompoundNode $node
     * @param int $depth
     * @return string
     */
    private function recursiveToString(CompoundNode $node, $depth = 0)
    {
        $indent = str_repeat('    ', $depth);
        $ret = '';
        $ret .= $indent . (string) $node . PHP_EOL;
        foreach ($node->getChildren() as $child)
        {
//            $ret .= $indent . (string) $child . PHP_EOL;
            $ret .= $this->recursiveToString($child, $depth+1);
        }
        return $ret;
    }

}
