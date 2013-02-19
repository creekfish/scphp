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


	public function __construct()
	{
		$this->children = array();
		$this->parent = NULL;
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
		// add child node with id (if any) as index
		$idx = $child->getId();
		if (empty($idx))
		{
			// ensure valid index for child node without id
			$idx = '__NID__' . count($this->children);
		}
		$this->children[$idx] = $child;
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
        if (array_key_exists($target_id, $this->children))
        {
		    return $this->children[$target_id];
        }
        return NULL;
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
	 * Return TRUE if this is a simple node (a leaf)
	 *
	 * @return boolean
	 */
	public function isSimple()
	{
		return count($this->getChildren()) == 0;
	}

	/**
	 * Return TRUE if this is a composite node (a non-leaf, has child states)
	 *
	 * @return boolean
	 */
	public function isComposite()
	{
		return count($this->getChildren()) > 0;
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
