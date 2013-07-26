<?php
namespace ZfcDatagridTest\Column;

use ZfcDatagrid\Column;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Action
 */
class ActionTest extends PHPUnit_Framework_TestCase
{

    public function testConstructDefaultBoth ()
    {
        $column = new Column\Action();
        
        $this->assertEquals('action', $column->getUniqueId());
        $this->assertEquals('Actions', $column->getLabel());
        $this->assertFalse($column->isUserSortEnabled());
        $this->assertFalse($column->isUserFilterEnabled());
        $this->assertFalse($column->isRowClickEnabled());
    }

    public function testAddAction ()
    {
        $column = new Column\Action();
        
        $this->assertCount(0, $column->getActions());
        
        $action = $this->getMock('ZfcDatagrid\Column\Action\Button');
        $column->addAction($action);
        
        $this->assertCount(1, $column->getActions());
        
        $action2 = $this->getMock('ZfcDatagrid\Column\Action\Button');
        $column->addAction($action2);
        $action3 = $this->getMock('ZfcDatagrid\Column\Action\Button');
        $column->addAction($action3);
        
        $this->assertCount(3, $column->getActions());
    }
}
