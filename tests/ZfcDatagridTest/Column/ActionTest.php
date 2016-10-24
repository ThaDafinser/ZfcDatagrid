<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Action
 */
class ActionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructDefaultBoth()
    {
        $column = new Column\Action();

        $this->assertEquals('action', $column->getUniqueId());
        $this->assertEquals('Actions', $column->getLabel());
        $this->assertFalse($column->isUserSortEnabled());
        $this->assertFalse($column->isUserFilterEnabled());
        $this->assertFalse($column->isRowClickEnabled());
    }

    public function testAddRemoveAction()
    {
        $column = new Column\Action();

        $this->assertCount(0, $column->getActions());

        $action = $this->getMockBuilder(\ZfcDatagrid\Column\Action\Button::class)->getMock();
        $column->addAction($action);

        $this->assertCount(1, $column->getActions());

        $action2 = $this->getMockBuilder(\ZfcDatagrid\Column\Action\Button::class)->getMock();
        $column->addAction($action2);
        $action3 = $this->getMockBuilder(\ZfcDatagrid\Column\Action\Button::class)->getMock();
        $column->addAction($action3);

        $this->assertCount(3, $column->getActions());
        $this->assertEquals($action2, $column->getAction(1));
        $column->removeAction(2);
        $this->assertCount(2, $column->getActions());

        $actions = [
            $this->getMockBuilder(\ZfcDatagrid\Column\Action\Button::class)->getMock(),
            $this->getMockBuilder(\ZfcDatagrid\Column\Action\Button::class)->getMock(),
        ];
        $column->setActions($actions);
        $this->assertEquals($actions, $column->getActions());
        $column->clearActions();
        $this->assertCount(0, $column->getActions());
    }
}
