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
	 * The data context for this expression.
	 * @var \scphp\IContext
	 */
	private $context;

    /**
     * Constructor
     *
     * @param string $text The text to be evaluated in the expression
	 * @param \scphp\IEvaluator $evaluator The evaluator for this expression
	 * @param \scphp\IContext $context The data context of this expression
	 */
    public function __construct($text, \scphp\IEvaluator $evaluator = NULL, \scphp\IContext $context = NULL)
    {
        $this->text = $text;
		$this->setEvaluator($evaluator);
		$this->setContext($context);
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
     * @param \scphp\IEvaluator $evaluator
     */
    public function setEvaluator(\scphp\IEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * Get the text of this expression.
     *
     * @return \scphp\IEvaluator
     */
    public function getEvaluator()
    {
        return $this->evaluator;
    }

	/**
	 * Set the data context for this expression.
	 *
	 * @param \scphp\IContext $context
	 */
	public function setContext($context)
	{
		if ($context === NULL)
		{
			$context = new \scphp\context\NullContext();  // default context
		}
		$this->context = $context;
	}

	/**
	 * Return the data context for this expression.
	 *
	 * @return \scphp\IContext
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Evaluate this expression and return the result.
	 *
	 * @return mixed
	 */
	public function evaluate()
	{
		return $this->getEvaluator()->evaluate($this->getContext(), $this->getText());
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
