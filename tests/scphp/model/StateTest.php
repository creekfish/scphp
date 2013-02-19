<?php

use scphp\model\Condition;
use scphp\model\Event;
use scphp\model\Initial;
use scphp\model\Log;
use scphp\model\Parallel;
use scphp\model\Scxml;
use scphp\model\State;
use scphp\model\Transition;

class StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scxml
     */
    private $scxml;

    /**
     * @var State
     */
    private $sut;

    public function setup()
    {
		$this->scxml = new Scxml();
		$this->scxml->setDocumentOrder(1);

			$uncle = new State();
			$uncle->setDocumentOrder(2);
			$uncle->setId('uncle');
			$this->scxml->addChild($uncle);

			$parent = new State();
			$parent->setDocumentOrder(3);
			$parent->setId('parent');
			$this->scxml->addChild($parent);

				$this->sut = new State();
				$this->sut->setDocumentOrder(4);
				$this->sut->setId('sut');
				$parent->addChild($this->sut);

					$kid1 = new Parallel();
					$kid1->setDocumentOrder(5);
					$kid1->setId('kid1');
					$this->sut->addChild($kid1);

						$grand1 = new State();
						$grand1->setDocumentOrder(6);
						$grand1->setId('grand1');
						$kid1->addChild($grand1);

						$grand2 = new State();
						$grand2->setDocumentOrder(7);
						$grand2->setId('grand2');
						$kid1->addChild($grand2);

					$kid2 = new State();
					$kid2->setDocumentOrder(8);
					$kid2->setId('kid2');
					$this->sut->addChild($kid2);

						$grand3 = new State();
						$grand3->setDocumentOrder(9);
						$grand3->setId('grand3');
						$kid2->addChild($grand3);

				$brother = new State();
				$brother->setDocumentOrder(10);
				$brother->setId('brother');
				$parent->addChild($brother);
    }

    public function testIsAtomic()
    {
        $state = new State();
        $this->AssertTrue($state->isAtomic());
        $this->AssertFalse($state->isCompound());
    }

    public function testIsCompound()
    {
        $state = new State();
        $state->addChild(new State());
        $this->AssertFalse($state->isAtomic());
        $this->AssertTrue($state->isCompound());
    }

    public function testIsFinal()
    {
        $state = new State();
        $this->AssertTrue($state->isFinal());
    }

	public function testIsValidChild()
	{
		$this->AssertTrue($this->sut->isValidChild(new Transition()));
		$this->AssertTrue($this->sut->isValidChild(new State()));
		$this->AssertTrue($this->sut->isValidChild(new Parallel()));
		$this->AssertFalse($this->sut->isValidChild(new Log()));
	}

	public function testIsValidParent()
	{
		$this->AssertTrue($this->sut->isValidParent(new Scxml()));
		$this->AssertTrue($this->sut->isValidParent(new State()));
		$this->AssertTrue($this->sut->isValidParent(new Parallel()));
		$this->AssertFalse($this->sut->isValidParent(new Transition()));
	}

	public function testGetInitialDescendants()
	{
		$expected = array(
			5 => $this->sut->getChild('kid1'),
			6 => $this->sut->getChild('kid1')->getChild('grand1'),
			7 => $this->sut->getChild('kid1')->getChild('grand2')
		);
		$this->assertEquals($expected, $this->sut->getInitialDescendants());
	}

	public function testGetInitialDescendantsChangedDocOrder()
	{
		// move doc order to after kid2
		$this->sut->getChild('kid1')->setDocumentOrder(1000);
		// still should be in same order
		$expected = array(
			1000 => $this->sut->getChild('kid1'),
			6 => $this->sut->getChild('kid1')->getChild('grand1'),
			7 => $this->sut->getChild('kid1')->getChild('grand2')
		);
		$this->assertEquals($expected, $this->sut->getInitialDescendants());
	}

	public function testGetInitialDescendantsWithInitial()
	{
		$initial = new Initial();
		$trans = new Transition();

		// have to set up the model for the transition to lookup target by id
		$model = new \scphp\Model();
		$model->addTarget($this->sut->getChild('kid2'));
		$trans->setModel($model);

		$trans->setTarget('kid2');
		$initial->addTransition($trans);
		$this->sut->addChild($initial);

		$expected = array(
			8 => $this->sut->getChild('kid2'),
			9 => $this->sut->getChild('kid2')->getChild('grand3')
		);
		$this->assertEquals($expected, $this->sut->getInitialDescendants());
	}

	public function testGetInitialDescendantsWithInitialAttr()
	{
		$this->sut->setInitial('kid2');
		$expected = array(
			8 => $this->sut->getChild('kid2'),
			9 => $this->sut->getChild('kid2')->getChild('grand3')
		);
		$this->assertEquals($expected, $this->sut->getInitialDescendants());
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Invalid id 'grand1' specified in initial attribute.
	 */
	public function testSetBadInitialAttr()
	{
		$this->sut->setInitial('grand1');
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Cannot use initial attribute 'kid2' with initial child element.
	 */
	public function testSetInitialAttrAfterInitialNode()
	{
		$this->sut->addChild(new Initial());
		$this->sut->setInitial('kid2');
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Cannot use initial child element with initial attribute.
	 */
	public function testSetInitialNodeAfterInitialAttr()
	{
		$this->sut->setInitial('kid2');
		$this->sut->addChild(new Initial());
	}


    // TransitionTarget

    public function testGetFirstTransition()
    {
        $transition = new Transition($this->scxml->getChild('parent'), 'event1', new Condition());
        $transition2 = new Transition($this->scxml->getChild('uncle'), 'event2');
        $this->sut->addTransition($transition);
        $this->sut->addTransition($transition2);
        $this->assertEquals($transition, $this->sut->getFirstTransition());
    }

    public function testGetAllTransitions()
    {
        $transition = new Transition($this->scxml->getChild('parent'), 'event1', new Condition());
        $transition2 = new Transition($this->scxml->getChild('uncle'), 'event2');
        $this->sut->addTransition($transition);
        $this->sut->addTransition($transition2);
        $this->assertEquals(array($transition, $transition2), $this->sut->getTransitions());
    }

    public function testGetTransitionByEvent()
    {
		$transition = new Transition($this->scxml->getChild('parent'), 'event1', new Condition());
		$transition2 = new Transition($this->scxml->getChild('uncle'), 'event2');
		$this->sut->addTransition($transition);
		$this->sut->addTransition($transition2);
		$this->assertEquals(array($transition2), $this->sut->getTransitions(new Event('event2')));
    }

	public function testGetMultipleTransitionsByEvent()
	{
		$transition = new Transition($this->scxml->getChild('parent'), 'event1', new Condition());
		$transition2 = new Transition($this->scxml->getChild('uncle'), 'event1');
		$this->sut->addTransition($transition);
		$this->sut->addTransition(new Transition($this->scxml->getChild('uncle')));
		$this->sut->addTransition(new Transition($this->sut, 'event2'));
		$this->sut->addTransition($transition2);
		$this->assertEquals(array($transition, $transition2), $this->sut->getTransitions(new Event('event1')));
    }

	public function testGetTransitionNoMatchEvent()
	{
		$transition = new Transition($this->scxml->getChild('parent'), 'event1', new Condition());
		$transition2 = new Transition($this->scxml->getChild('uncle'), 'event2');
		$this->sut->addTransition($transition);
		$this->sut->addTransition($transition2);
		$this->assertEquals(array(), $this->sut->getTransitions(new Event('event3')));
    }

	// CompoundNode

    public function testGetAncestors()
    {
        $expected = array(
            $this->scxml,
            $this->scxml->getChild('parent')
        );
        $this->assertEquals($expected, $this->sut->getAncestors());
    }

    public function testGetAncestorsReverse()
    {
        $expected = array(
            $this->scxml->getChild('parent'),
            $this->scxml
        );
        $this->assertEquals($expected, $this->sut->getAncestors(TRUE));
    }

}