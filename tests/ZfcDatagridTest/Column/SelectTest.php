<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;


/**
 * @group Column
 * @covers ZfcDatagrid\Column\Select
 */
class SelectTest extends PHPUnit_Framework_TestCase
{

    public function testConstructDefaultBoth ()
    {
        $column = new Column\Standard('id', 'user');
        
        $this->assertEquals('user_id', $column->getUniqueId());
        $this->assertEquals('user', $column->getSelectPart1());
        $this->assertEquals('id', $column->getSelectPart2());
    }

    public function testConstructDefaultSingle ()
    {
        $column = new Column\Standard('title');
        
        $this->assertEquals('title', $column->getUniqueId());
        $this->assertEquals('title', $column->getSelectPart1());
    }

    public function testObject ()
    {
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Standard($expr, 'myAlias');
        
        $this->assertEquals($expr, $column->getSelectPart1());
        $this->assertEquals('myAlias', $column->getUniqueId());
    }

    public function testException ()
    {
        $this->setExpectedException('Exception');
        
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Standard($expr);
    }

    public function testExceptionNotString ()
    {
        $this->setExpectedException('Exception');
        
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Standard($expr, new \stdClass());
    }
}
