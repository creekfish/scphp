<?php

use scphp\Configuration;
use scphp\Model;
use scphp\model\Event;
use scphp\model\Parallel;
use scphp\model\Scxml;
use scphp\model\State;
use scphp\model\Transition;

class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Configuration
     */
    private $sut;

	/**
	 * @var Scxml
	 */
	private $scxml;

    public function setup()
    {
		$this->sut = new Configuration();
		$this->scxml = new Scxml(NULL, 0);
		$this->scxml->addChild(new State('parent', 1));
		$this->scxml->addChild(new State('uncle', 2));
    }


	public function testAddStateWithAncestorsOnly()
	{
		$state = new State('state', 3);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($state);
		$this->sut->addState($state);
		$this->assertEquals(
				array('1' => $parent, '3' => $state),
				$this->sut->getStates()
		);
	}

	public function testAddStateTwoTimes()
	{
		$state = new State('state', 3);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($state);
		$this->sut->addState($state);
		$this->sut->addState($state);
		$this->assertEquals(
			array('1' => $parent, '3' => $state),
			$this->sut->getStates()
		);
	}

	public function testAddStateWithInitialDescendants()
	{
		$state = new State('state', 3);
		$child1 = new State('child1', 4);
		$state->addChild($child1);
		$child2 = new State('child2', 5);
		$state->addChild($child2);
		$state->setInitial('child2');
		$grand1 = new State('grand1', 6);
		$child2->addChild($grand1);
		$grand2 = new State('grand2', 7);
		$child2->addChild($grand2);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($state);
		$this->sut->addState($state);
		$this->assertEquals(
			array('1' => $parent, '3' => $state, '5' => $child2, '6' => $grand1),
			$this->sut->getStates()
		);
	}

	public function testAddParallelWithMultipleDescendants()
	{
		$parallel = new Parallel('state', 3);
		$child1 = new State('child1', 4);
		$parallel->addChild($child1);
		$child2 = new State('child2', 5);
		$parallel->addChild($child2);
		$grand1 = new State('grand1', 6);
		$child2->addChild($grand1);
		$grand2 = new State('grand2', 7);
		$child2->addChild($grand2);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($parallel);
		$this->sut->addState($parallel);
		$this->assertEquals(
			array('1' => $parent, '3' => $parallel, '4' => $child1, '5' => $child2, '6' => $grand1),
			$this->sut->getStates()
		);
	}

	public function testGetAtomicStatesWithInitialDescendants()
	{
		$state = new State('state', 3);
		$child1 = new State('child1', 4);
		$state->addChild($child1);
		$child2 = new State('child2', 5);
		$state->addChild($child2);
		$state->setInitial('child2');
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($state);
		$this->sut->addState($state);
		$this->assertEquals(
			array('5' => $child2),
			$this->sut->getAtomicStates()
		);
	}

	public function testGetAtomicStatesAncestorsOnly()
	{
		$state = new State('state', 3);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($state);
		$this->sut->addState($state);
		$this->assertEquals(
			array('3' => $state),
			$this->sut->getAtomicStates()
		);
	}

	public function testGetParallelAtomicStates()
	{
		$parallel = new Parallel('state', 3);
		$child1 = new State('child1', 4);
		$parallel->addChild($child1);
		$child2 = new State('child2', 5);
		$parallel->addChild($child2);
		$grand1 = new State('grand1', 6);
		$child2->addChild($grand1);
		$grand2 = new State('grand2', 7);
		$child1->addChild($grand2);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($parallel);
		$this->sut->addState($parallel);
		$this->assertEquals(
			array('6' => $grand1, '7' => $grand2),
			$this->sut->getAtomicStates()
		);
	}

	public function testSelectTransitions()
	{
		$parallel = new Parallel('state', 3);
		$child1 = new State('child1', 4);
		$parallel->addChild($child1);
		$child2 = new State('child2', 5);
		$parallel->addChild($child2);
		$grand1 = new State('grand1', 6);
		$child2->addChild($grand1);
		$grand2 = new State('grand2', 7);
		$child2->addChild($grand2);
		$parent = $this->scxml->getChild('parent');
		$parent->addChild($parallel);
		$this->sut->addState($parallel);

		$trans1 = new Transition('state', 'test');
		$trans1->setDocumentOrder(8);
		$child1->addTransition($trans1);
		$trans2 = new Transition('grand2', 'test');
		$trans2->setDocumentOrder(9);
		$child2->addTransition($trans2);
		$trans3 = new Transition('child1', 'test');
		$trans3->setDocumentOrder(10);
		$grand1->addTransition($trans3);
		$trans4 = new Transition('child1');
		$trans4->setDocumentOrder(11);
		$grand1->addTransition($trans4);
		$trans5 = new Transition('state', 'nomatch');
		$trans5->setDocumentOrder(12);
		$grand2->addTransition($trans5);
		$trans6 = new Transition('grand1', 'test');
		$trans6->setDocumentOrder(13);
		$grand2->addTransition($trans6);

		$this->assertEquals(
			array('8' => $trans1, '9' => $trans2, '10' => $trans3),
			$this->sut->selectTransitions(new Event('test'))
		);
	}

}
