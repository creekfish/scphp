<?php

use scphp\model\Condition;
use scphp\model\Expression;

class ConditionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Condition
     */
    private $sut;

    public function setup()
    {
		$this->sut = new Condition();
    }

	public function testIsTrueWithNoExpression()
	{
//TODO check this assumption against the spec (if empty expression string, return true)
		$this->assertTrue($this->sut->isTrue());
	}
}