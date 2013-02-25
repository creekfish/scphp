<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Condition
{
    /**
     * Expression to be evaluated for this condition.
     * @var Expression
     */
    private $expression;


    /**
     * Constructor
     *
     * @param Expression $expression expression for this condition
     */
    public function __construct(Expression $expression = NULL)
    {
        $this->expression = $expression;
    }

    /**
     * Set the expression for this condition.
     *
     * @param Expression $expression
     */
    public function setExpression(Expression $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Get the expression for this condition.
     *
     * @return Expression
     */
    public function getExpression()
    {
        return $this->expression;
    }

	/**
	 * Return TRUE if this condition evaluates to true (using the
	 * given evaluator).
	 *
	 * @return boolean
	 */
	public function isTrue()
	{
		if (empty($this->expression))
		{
//TODO check this assumption against the spec (if empty expression string, return true)
			return TRUE;
		}

		return $this->expression->evaluate() == TRUE;
	}
}
