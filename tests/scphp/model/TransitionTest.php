<?php

use scphp\Model;
use scphp\model\Event;
use scphp\model\Condition;
use scphp\model\Expression;
use scphp\model\Log;
use scphp\model\Scxml;
use scphp\model\State;
use scphp\model\Transition;

class TransitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Transition
     */
    private $sut;

    public function setup()
    {
		$do = 1;

		$this->scxml = new Scxml();
		$this->scxml->setDocumentOrder($do++);

		$parent = new State();
		$parent->setDocumentOrder($do++);
		$parent->setId('parent');
		$this->scxml->addChild($parent);

		$uncle = new State();
		$uncle->setDocumentOrder($do++);
		$uncle->setId('uncle');
		$this->scxml->addChild($uncle);

		$child = new State();
		$child->setDocumentOrder($do++);
		$child->setId('child');
		$parent->addChild($child);

		$this->model = new Model();
		$this->model->addTarget($parent);
		$this->model->addTarget($uncle);
		$this->model->addTarget($child);

		$this->sut = new Transition();
		$this->sut->setModel($this->model);
	}

    public function testGetEvent()
    {
		$this->sut->setEvent('wedding');
		$this->assertEquals(new Event('wedding'), $this->sut->getEvent());
    }

	public function testGetCondition()
	{
		$this->sut->setCondition('x < 0');
		$this->assertEquals(new Condition(new Expression('x < 0')), $this->sut->getCondition());
	}

	public function testGetTargetsSingle()
	{
		$this->sut->setTarget('parent');
		$this->assertEquals(array($this->model->getTarget('parent')), $this->sut->getTargets());
	}

	public function testGetTargetsMultiple()
	{
		$this->sut->setTarget('parent uncle');  // looks bogus... should be parallel sates, but just a test
		$this->assertEquals(array($this->model->getTarget('parent'), $this->model->getTarget('uncle')), $this->sut->getTargets());
	}

	public function testGetTargetById()
	{
		$this->sut->setTarget('parent uncle');
		$this->assertEquals($this->model->getTarget('uncle'), $this->sut->getTarget('uncle'));
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Transition target 'child' not valid for transition.
	 */
	public function testGetTargetByWrongId()
	{
		$this->sut->setTarget('parent uncle');
		$this->assertEquals(array($this->model->getTarget('uncle')), $this->sut->getTarget('child'));
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Transition target 'not_in_model' not valid for transition.
	 */
	public function testGetTargetByNonexistentId()
	{
		$this->sut->setTarget('parent uncle');
		$this->sut->setModel(NULL);  // shouldn't matter...
		$this->assertEquals(array($this->model->getTarget('uncle')), $this->sut->getTarget('not_in_model'));
	}

	/**
	 * @expectedException \scphp\model\ModelException
	 * @expectedExceptionMessage Model not specified for transition; cannot find target.
	 */
	public function testGetTargetByIdNoModel()
	{
		$this->sut->setTarget('parent uncle');
		$this->sut->setModel(NULL);
		$this->sut->getTarget('parent');
	}

	public function testGetFirstTarget()
	{
		$this->sut->setTarget('parent child uncle');  // looks bogus... should be parallel sates, but just a test
		$this->assertEquals($this->model->getTarget('parent'), $this->sut->getFirstTarget());
	}

	public function testIsValidChild()
	{
		$this->AssertFalse($this->sut->isValidChild(new Transition()));
		$this->AssertFalse($this->sut->isValidChild(new State()));
		$this->AssertTrue($this->sut->isValidChild(new Log()));
	}

	public function testIsValidParent()
	{
		$this->AssertTrue($this->sut->isValidParent(new Scxml()));
		$this->AssertTrue($this->sut->isValidParent(new State()));
		$this->AssertFalse($this->sut->isValidParent(new Transition()));
	}

}