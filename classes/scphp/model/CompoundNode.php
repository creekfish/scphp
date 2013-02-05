<?php

namespace scphp\model;

/**
 * Base class for all compound nodes, which may
 * have zero or more "child" nodes.
 *
 * @author bherring
 */
abstract class CompoundNode extends DocumentNode
{

	/**
	 * Child "sub-nodes" of this node in document order
	 * @var array of CompoundNode
	 */
	private $children;

	/**
	 * The parent node of this compound node.
	 * @var CompoundNode
	 */
	private $parent;

	/**
	 * The initial child "substate" of this node.
	 * @var CompoundNode
	 */
	private $initial;


	public function __construct()
	{
		$this->children = array();
		$this->parent = NULL;
		$this->initial = NULL;
		parent::__construct();
	}

	/**
	 * Add a child node to this node.
	 *
	 * @param CompoundNode $child
	 */
	public function addChild(CompoundNode $child)
	{
		if (!$this->isValidChild($child))
		{
			throw new ModelException(get_class($child) . ' is not a valid child type for ' . get_class($this));
		}
		if ($child instanceof Initial)
		{
			// initial sub-node specified by initial child element
			if (isset($this->initial) && $this->initial instanceof Initial)
			{
				throw new ModelException("Cannot use initial child element with initial attribute.");
			}
			$this->initial = $child;
		}
		else
		{
			// add child node with id (if any) as index
			$idx = $child->getId();
			if (empty($idx))
			{
				// ensure valid index for child node without id
				$idx = '__NID__' . count($this->children);
			}
			$this->children[$idx] = $child;
		}
		$child->setParent($this);
	}

	/**
	 * Get a list of child nodes in document order.
	 *
	 * @return array of CompoundNode
	 */
	public function getChildren()
	{
		return $this->children;
	}

	/**
	 * Fetch a child by id.
	 *
	 * @param string $target_id
	 * @return CompoundNode | NULL
	 */
	public function getChild($target_id)
	{
		return $this->children[$target_id];
	}

	/**
	 * Get the parent node of this node.
	 *
	 * @return CompoundNode
	 */
	public function getParent()
	{
		return $this->parent;
	}

	/**
	 * Set the parent node of this node.
	 *
	 * @param CompoundNode $parent
	 */
	public function setParent(CompoundNode $parent = NULL)
	{
		if ($parent !== NULL && !$this->isValidParent($parent))
		{
			throw new ModelException(get_class($parent) . ' is not a valid parent type for ' . get_class($this));
		}
		$this->parent = $parent;
	}

	/**
	 * Get the initial element of this node
     * (either the initial element or NULL).
     * To get the initial child node, use getInitialChild()
	 *
	 * @return CompoundNode
	 */
	public function getInitial()
	{
		if ($this->initial instanceof Initial)
        {
            return $this->initial;
        }
        return NULL;
	}

	/**
	 * Set the initial child node of this node from
	 * the id specified in the initial attribute value.
	 *
	 * @param string $initial_attr_value
	 */
	public function setInitial($initial_attr_value)
	{
		$initial = $this->getChild($initial_attr_value);
		if (isset($initial))
		{
			if (isset($this->initial) && $this->initial instanceof Initial)
			{
				throw new ModelException("Cannot use initial attribute '{$initial_attr_value}' with initial child element.");
			}
			$this->initial = $initial;
		}
		else
		{
			throw new ModelException("Invalid id '{$initial_attr_value}' specified in initial attribute.");
		}
	}

	/**
	 * Get the initial child node of this node.
	 *
	 * @return CompoundNode
	 */
	public function getInitialChild()
	{
		$initial = $this->initial;

		if (!isset($initial))
		{
            // default initial node is the first child node
            return $this->getFirstChild();
        }

        if ($initial instanceof Initial)
        {
            // follow the initial element's transition to the initial child
            $transition = $initial->getFirstTransition();
            if (isset($transition))
            {
                return $transition->getFirstTarget();
            }
            // initial node has a bad transition
            return NULL;
        }

        // initial child specified by initial attribute of this compound node
        return $initial;
	}

    /**
     * Return the first child node of this node (first in document order).
     *
     * @return CompoundNode The first child node | NULL if no children
     */
    public function getFirstChild()
    {
        if (isset($this->children) && count($this->children) > 0)
        {
            $first_array = array_slice($this->children,0,1);
            return array_shift($first_array);
        }
        return NULL;
    }

	/**
	 * Return a list of ancestor nodes of this node in document order.
	 *
	 * @param bool $reverse_doc_order return list in reverse document order
	 *      if TRUE (default is FALSE)
	 * @return array of State
	 */
	public function getAncestors($reverse_doc_order = FALSE)
	{
		$ret = array();
		$node = $this->getParent();
		while ($node !== NULL)
		{
			if ($reverse_doc_order)
			{
				array_push($ret, $node);
			}
			else
			{
				array_unshift($ret, $node);
			}
			$node = $node->getParent();
		}
		return $ret;
	}

	/**
	 * Return a list of ancestor nodes of this node in document order.
	 *
	 * @param bool $reverse_doc_order return list in reverse document order
	 *      if TRUE (default is FALSE)
	 * @return array of State
	 */
	public function getInitialDescendants($reverse_doc_order = FALSE)
	{
		$ret = array();
		$node = $this->getInitialChild();
		while ($node !== NULL)
		{
			if ($reverse_doc_order)
			{
				array_unshift($ret, $node);
			}
			else
			{
				array_push($ret, $node);
			}
			$node = $node->getInitialChild();
		}
		return $ret;
	}

    public function __toString()
    {
        $init = $this->getInitialChild();
        return parent::__toString() . '; intitial:' . ((isset($init)) ? $init->getId() : 'N/A');
    }

	/**
	 * Return TRUE if the provided node is a valid child node type for this node.
	 * @param CompoundNode $child
	 * @return boolean
	 */
	abstract public function isValidChild(CompoundNode $child);

	/**
	 * Return TRUE if the provided node is a valid parent node type for this node.
	 * @param CompoundNode $parent
	 * @return boolean
	 */
	abstract public function isValidParent(CompoundNode $parent);
}
