<?php

namespace scphp;

/**
 *
 * @author bherring
 */
interface IEvaluator
{
    /**
     *
     * @param IContext $context
     * @param model\Expression $expression
     * @return mixed
     */
    public function evaluate(IContext $context, model\Expression $expression);

}
