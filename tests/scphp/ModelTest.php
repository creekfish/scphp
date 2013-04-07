<?php

use scphp\model\Condition;
use scphp\model\Event;
use scphp\model\Initial;
use scphp\model\Log;
use scphp\Model;
use scphp\model\Scxml;
use scphp\model\State;
use scphp\model\FinalState;
use scphp\model\Transition;

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $sut;

    public function setup()
    {
		$this->sut = new Model();

        $scxml = new Scxml();
        $scxml->setDocumentOrder(1);

    }

	private function setupFixureModel()
	{
		$scxml = new Scxml();
		$this->sut->addNode($scxml);
		$state1 = new State();
		$state1->setId('state1');
		$this->sut->addNode($state1, $scxml);
		$final = new FinalState();
		$this->sut->addNode($final, $state1);
		$transition1 = new Transition('state2', 'event1');
		$this->sut->addNode($transition1, $state1);
		$transition2 = new Transition('state1');
		$this->sut->addNode($transition2, $state1);
		$this->sut->addNode(new Log(), $transition1);
		$state2 = new State();
		$state2->setId('state2');
		$this->sut->addNode($state2, $scxml);
		$transition3 = new Transition('state1', 'event2');
		$this->sut->addNode($transition3, $state2);
		$transition4 = new Transition('state2', 'event1');
		$this->sut->addNode($transition4, $state2);
		$state3 = new State();
		$this->sut->addNode($state3, $state2);
		$transition5 = new Transition('state1');
		$this->sut->addNode($transition5, $state3);
		return array(
			'scxml' => $scxml,
			'state1' => $state1,
			'final' => $final,
			'transition1' => $transition1,
			'transition2' => $transition2,
			'state2' => $state2,
			'transition3' => $transition3,
			'transition4' => $transition4,
			'state3' => $state3,
			'transition5' => $transition5,
		);
	}

    public function testGetScxml()
    {
        $node = new Scxml();
		$this->sut->addNode($node);
		$this->assertEquals($node, $this->sut->getScxml());
    }


	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage SCXML node cannot have parent node.
	 */
	public function testAddNodeScxmlWithParent()
	{
		$node = new Scxml();
		$this->sut->addNode($node, $node);
	}

	public function testGetTarget()
	{
		$parent = new Scxml();
		$this->sut->addNode($parent);
		$node = new State();
		$node->setId('state1');
		$this->sut->addNode($node, $parent);
		$this->assertEquals($node, $this->sut->getTarget('state1'));
	}

	public function testGetTargets()
	{
		$node = $this->setupFixureModel();
		$this->assertEquals(
			array(
				'state1'	=> $node['state1'],
				'__TID__1'	=> $node['final'],
				'state2'	=> $node['state2'],
				'__TID__3'	=> $node['state3']
			),
			$this->sut->getTargets()
		);
	}

	public function testGetTransitions()
	{
		$node = $this->setupFixureModel();
		$this->assertEquals(
			array(
				$node['transition1'],
				$node['transition4']
			),
			$this->sut->getTransitions(new Event('event1'))
		);
	}

	public function testGetTriggers()
	{
		$this->setupFixureModel();
		$triggers = $this->sut->getTriggers();
		sort($triggers);
		$this->assertEquals(
			array(new Event('event1'), new Event('event2')),
			$triggers
		);
	}

	public function testGetAllTransitions()
	{
		$this->setupFixureModel();
		$trans = $this->sut->getTransitions(NULL);
		$this->assertEquals(5, count($trans));
	}

	public function testIsTarget()
	{
		$this->setupFixureModel();
		$this->assertTrue($this->sut->isTarget('state2'));
	}

	public function testValidateModelOK()
	{
		$this->setupFixureModel();
		try {
			$this->assertNull($this->sut->validateModel());
		} catch (\scphp\model\ModelValidationException $not_expected) {
			$this->fail('Unexpected ModelValidationException thrown: ' . $not_expected->getMessage());
		}
	}

	/**
	 * @expectedException \scphp\model\ModelValidationException
	 * @expectedExceptionMessage Invalid target for transition with event 'abc'. Transition target 'state4' does not exist in model.
	 */
	public function testValidateModelFailTarget()
	{
		$node = $this->setupFixureModel();
		$transition6 = new Transition('state4', 'abc');
		$this->sut->addNode($transition6, $node['state1']);
		$this->sut->validateTargets();
	}

	public function testValidateTargetsOK()
	{
		$this->setupFixureModel();
		try {
			$this->assertNull($this->sut->validateTargets());
		} catch (\scphp\model\ModelValidationException $not_expected) {
			$this->fail('Unexpected ModelValidationException thrown: ' . $not_expected->getMessage());
		}
	}

	/**
	 * @expectedException \scphp\model\ModelValidationException
	 * @expectedExceptionMessage Invalid target for transition with event 'abc'. Transition target 'state4' does not exist in model.
	 */
	public function testValidateTargetsFail()
	{
		$node = $this->setupFixureModel();
		$transition6 = new Transition('state4', 'abc');
		$this->sut->addNode($transition6, $node['state1']);
		$this->sut->validateTargets();
	}
}