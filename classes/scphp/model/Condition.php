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
}
