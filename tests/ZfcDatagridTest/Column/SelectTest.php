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
        $column = new Column\Select('id', 'user');
        
        $this->assertEquals('user_id', $column->getUniqueId());
        $this->assertEquals('user', $column->getSelectPart1());
        $this->assertEquals('id', $column->getSelectPart2());
    }

    public function testConstructDefaultSingle ()
    {
        $column = new Column\Select('title');
        
        $this->assertEquals('title', $column->getUniqueId());
        $this->assertEquals('title', $column->getSelectPart1());
    }

    public function testObject ()
    {
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Select($expr, 'myAlias');
        
        $this->assertEquals($expr, $column->getSelectPart1());
        $this->assertEquals('myAlias', $column->getUniqueId());
    }

    public function testException ()
    {
        $this->setExpectedException('Exception');
        
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Select($expr);
    }

    public function testExceptionNotString ()
    {
        $this->setExpectedException('Exception');
        
        $expr = new \Zend\Db\Sql\Expression('Something...');
        $column = new Column\Select($expr, new \stdClass());
    }
}
