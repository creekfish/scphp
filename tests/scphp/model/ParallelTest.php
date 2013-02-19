<?php

use scphp\model\Condition;
use scphp\model\Event;
use scphp\model\Initial;
use scphp\model\Log;
use scphp\model\Parallel;
use scphp\model\Scxml;
use scphp\model\State;
use scphp\model\Transition;

class ParallelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scxml
     */
    private $scxml;

    /**
     * @var Parallel
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

			$this->sut = new Parallel();
			$this->sut->setDocumentOrder(4);
			$this->sut->setId('sut');
			$parent->addChild($this->sut);

				$kid1 = new State();
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

/*
 *
 *TODO parallel testing
 *
 * Need to test trans target selects:
 * 1) parallel itself
 * 2) all parallel children
 * 3) valid config of parallel chidren and grandchildren
 * 4) only one parallel child (fail)
 * 5) invalid config(s) of parallel children and grandchildren
 *
 * This can all be statically validated by model validation, right?  Doesn't have to be caught at runtime?
 * Are 4 and 5 even a problem?  What does the spec say?
 */



	/**
	 * @expectedException \scphp\model\ModelValidationException
	 * @expectedExceptionMessage Parallel node must have child nodes.
	 */
    public function testValidateHasNoChildNodes()
    {
        $parallel = new Parallel();
        $parallel->validate();
    }

    public function testValidateOK()
    {
		$this->AssertTrue($this->sut->validate());
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
			8 => $this->sut->getChild('kid2'),
			9 => $this->sut->getChild('kid2')->getChild('grand3')
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
			8 => $this->sut->getChild('kid2'),
			9 => $this->sut->getChild('kid2')->getChild('grand3')
        );
        $this->assertEquals($expected, $this->sut->getInitialDescendants());
    }

}