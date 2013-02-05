<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Transition extends CompoundNode
{

    /**
     * Triggering event for this transition (if any).
     * @var Event
     */
    private $event;

    /**
     * Guard condition for this transition (if any).
     * @var Condition
     */
    private $condition;

    /**
     * List of target ids (if any) for this transition in document order.
     * If more than one target is specified, they must belong to the
     * same parallel region.
     * @var array of string
     */
    private $targets;


    public function __construct()
    {
        $this->event = NULL;
        $this->condition = NULL;
        $this->targets = array();
        parent::__construct();
    }

    /**
     * Get the type of this transition.
     *
     * @see TransitionType
     *
     * @return TransitionType - Type of transition.
     */
    public function getType()
    {

    }

    /**
     * Get the trigger event for this transition (if any).  Note that
     * for the transition to be taken, the guard condition
     * must also be satisfied.
     *
     * @return Event - The event or NULL if no trigger event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Get the guard condition (if any) for this transition.
     *
     * @return Condition - The condition or NULL if no guard condition
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Set the trigger event for this transition. Set to NULL
     * if there is no event.
     *
     * @param string $event - Trigger event for this transition, or NULL
     */
    public function setEvent($event)
    {
        $this->event = new Event($event);
    }

    /**
     * Set the guard condition for this transition. Set to NULL
     * if there is no condition.
     *
     * @param Condition $condition
     */
    public function setCondition(Condition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * Set list of targets for this transition.
     *
     * @param array of string $targets
     */
    public function setTargets(array $targets)
    {
        $this->targets = $targets;
    }

    /**
     * Add a target id for this transition.
     *
     * @param string $target_id
     * @return void
     * @throws ModelException
     */
    public function addTarget($target_id)
    {
        if (empty($target_id))
        {
            throw new ModelException('Transition target id must not be empty.');
        }
        $this->targets[] = $target_id;
    }

    /**
     * Add a target or multiple targets (if in parallel region) for this transition
     * based on the "targets" attribute.
     *
     * @param string $target id (or list of ids) of the target for this transition
     * @return void
     * @throws ModelException
     */
    public function setTarget($target)
    {
        //todo Implement processing of multiple targets for parallel regions
        if (empty($target))
        {
            throw new ModelException('Transition target attribute must not be empty.');
        }
        if (!is_string($target))
        {
            throw new ModelException('Transition target attribute must be string.');
        }
        foreach (explode(' ', $target) as $target_id)
        {
            $this->addTarget($target_id);
        }
    }

    /**
     * Get list of targets for this transition (possibly none),
     * in document order.
     *
     * @return array of string - List of targets
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Get target for this transition,
     * in document order.
     *
     * @return TransitionTarget target matching the given id
     * @throws ModelException
     */
    public function getTarget($target_id)
    {
        if (in_array($target_id, $this->targets))
        {
            $model = $this->getModel();
            if (!isset($model))
            {
                throw new ModelException("Model not specified for transition; cannot find target.");
            }
            $target = $model->getTarget($target_id);
            return $target;
        }
        throw new ModelException("Transition target '{$target_id}' not valid for transition.");
    }

    /**
     * Return the first target node for this transition (first in the list of target ids).
     *
     * @return TransitionTarget The first target node | NULL if no targets
     * @throws ModelException
     */
    public function getFirstTarget()
    {
        if (isset($this->targets) && count($this->targets) > 0)
        {
            $first_array = array_slice($this->targets,0,1);
            return $this->getTarget(array_shift($first_array));
        }
        return NULL;
    }

    /**
     * Return TRUE if the provided node is a valid parent node type for this node.
     * @param CompoundNode $parent
     * @return boolean
     */
    public function isValidParent(CompoundNode $parent)
    {
        return ($parent instanceof TransitionTarget ||
            $parent instanceof Scxml
        );
    }

    /**
     * Return TRUE if the provided node is a valid child node type for this node.
     * @param CompoundNode $child
     * @return boolean
     */
    public function isValidChild(CompoundNode $child)
    {
        return $child instanceof ExecutableNode;
    }

    public function __toString()
    {
        $cond = ($this->getCondition() !== NULL) ? $this->getCondition()->getExpression() : 'NULL';
        $event = ($this->getEvent() !== NULL) ? $this->getEvent()->getName() : 'NULL';
        $target_str = implode(',' , $this->getTargets());
        return parent::__toString() . '; event:' . $event . '; cond:' . $cond . '; targets:' . $target_str;
    }
}
