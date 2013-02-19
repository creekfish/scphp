<?php

use scphp\Model;
use scphp\model\Event;
use scphp\model\Transition;

class EventTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Event
	 */
	private $event;

    public function setup()
    {
		$this->event = new Event('first.second.third');
	}

    public function testMatchesExact()
    {
		$sut = new Event('first.second.third');
		$this->assertTrue($sut->matches($this->event));
    }

	public function testMatchesPartialOneToken()
	{
		$sut = new Event('first');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesEndingWildcard()
	{
		$sut = new Event('first.*');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesEndingDot()
	{
		$sut = new Event('first.');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesPartialTwoTokens()
	{
		$sut = new Event('first.second');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesWildcard()
	{
		$sut = new Event('*');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesEndingWildcardLastToken()
	{
		$sut = new Event('first.second.third.*');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchesEndingDotLastToken()
	{
		$sut = new Event('first.second.third.');
		$this->assertTrue($sut->matches($this->event));
	}

	public function testMatchFailsWithPartOfTokenSpecified()
	{
		$sut = new Event('fir');
		$this->assertFalse($sut->matches($this->event));
	}

	public function testMatchFailsWithExtraToken()
	{
		$sut = new Event('first.second.third.fourth');
		$this->assertFalse($sut->matches($this->event));
	}

	public function testMatchFailsWithExtraCharacters()
	{
		$sut = new Event('first.second.thirdextra');
		$this->assertFalse($sut->matches($this->event));
	}
}
