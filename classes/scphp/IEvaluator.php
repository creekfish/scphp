<?php

namespace scphp;

/**
 *
 * @author bherring
 */
interface IEvaluator
{
	/**
	 * Evaluate the given expression in the given context.
	 *
	 * @param \scphp\IContext $context
	 * @param \scphp\model\Expression $expression
	 * @return mixed
	 */
    public function evaluate(IContext $context, model\Expression $expression);

}
