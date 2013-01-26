<?php

namespace scphp\model;

/**
 *
 * @author bherring
 */
class Expression
{
    /**
     * Text of the expression.
     * @var string
     */
    private $text;

    /**
     * Evaluator for this expression.
     * @var string
     */
    private $evaluator;

    /**
     * Set the text of this expression.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get the text of this expression.
     *
     * @return string
     */
    public function getName()
    {
        return $this->text;
    }

    /**
     * Set the evaluator for this expression.
     *
     * @param IEvaluator $evaluator
     */
    public function setEvaluator(IEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * Get the text of this expression.
     *
     * @return IEvaluator
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     * Render this expression as a string.
     *
     * @return string
     */
    public function toString()
    {
        //@todo this should evaluate the expression and then render it
        return $this->text;
    }
}
