<?php
namespace ZfcDatagridTest\Column\Action;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Filter;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Action\AbstractAction
 */
class AbstractActionTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $column;

    public function setUp ()
    {
        $this->column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column->setUniqueId('colName');
    }

    public function testLink ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertEquals('#', $action->getLink());
        
        $action->setLink('/my/page/is/cool');
        $this->assertEquals('/my/page/is/cool', $action->getLink());
    }

    public function testAttributes ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertEquals(array(), $action->getAttributes());
        
        $this->assertEquals('', $action->getAttribute('something'));
        
        $action->setAttribute('class', 'error');
        $this->assertCount(1, $action->getAttributes());
        $this->assertEquals(array(
            'class' => 'error'
        ), $action->getAttributes());
        
        $this->assertEquals('error', $action->getAttribute('class'));
    }

    public function testTitle ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertEquals('', $action->getTitle());
        
        $action->setTitle('This is my action');
        $this->assertEquals('This is my action', $action->getTitle());
    }

    public function testAddClass ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertEquals('', $action->getAttribute('class'));
        
        $action->addClass('cssClass');
        $this->assertEquals('cssClass', $action->getAttribute('class'));
        
        $action->addClass('cssClass2');
        $this->assertEquals('cssClass cssClass2', $action->getAttribute('class'));
    }

    public function testShowOnValue ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertCount(0, $action->getShowOnValues());
        
        $this->assertFalse($action->hasShowOnValues());
        $action->addShowOnValue($this->column, '23', Filter::EQUAL);
        $this->assertTrue($action->hasShowOnValues());
    }

    public function testIsDisplayed ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->assertTrue($action->isDisplayed(array(
            $this->column->getUniqueId() => '23'
        )));
        
        // EQUAL
        $action->addShowOnValue($this->column, '23', Filter::EQUAL);
        
        $this->assertTrue($action->isDisplayed(array(
            $this->column->getUniqueId() => '23'
        )));
        
        $this->assertFalse($action->isDisplayed(array(
            $this->column->getUniqueId() => '33'
        )));
    }

    public function testIsDisplayedNotEqual ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $action->addShowOnValue($this->column, '23', Filter::NOT_EQUAL);
        
        $this->assertTrue($action->isDisplayed(array(
            $this->column->getUniqueId() => '32'
        )));
        
        $this->assertFalse($action->isDisplayed(array(
            $this->column->getUniqueId() => '23'
        )));
    }

    public function testIsDisplayedException ()
    {
        /* @var $action \ZfcDatagrid\Column\Action\AbstractAction */
        $action = $this->getMockForAbstractClass('ZfcDatagrid\Column\Action\AbstractAction');
        
        $this->setExpectedException('Exception');
        $action->addShowOnValue($this->column, '23', Filter::BETWEEN);
        $action->isDisplayed(array(
            $this->column->getUniqueId() => '32'
        ));
    }
}
