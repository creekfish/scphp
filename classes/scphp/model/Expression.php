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
     * @var \scphp\IEvaluator
     */
    private $evaluator;


    /**
     * Constructor
     *
     * @param string $text the text to be evaluated in the expression
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

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
    public function getText()
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
    public function __toString()
    {
        //@todo this should evaluate the expression and then render it
        return $this->text;
    }
}
