<?php

namespace scphp\evaluator;

/**
 *
 * @author bherring
 */
class PhpEvaluator implements \scphp\IEvaluator
{
	/**
	 * Evaluate the given expression in the given context.
	 *
	 * @param \scphp\IContext $context
	 * @param \scphp\model\Expression $expression
	 * @return mixed
	 */
	public function evaluate(\scphp\IContext $context, \scphp\model\Expression $expression)
	{
		$eval_string = $expression->getText();
		$wrapped_eval_string = 'return (' . $eval_string . ');';
		return eval($wrapped_eval_string);
	}
}
