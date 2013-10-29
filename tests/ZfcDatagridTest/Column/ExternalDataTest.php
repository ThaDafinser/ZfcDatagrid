<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\DataPopulation;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\ExternalData
 */
class ExternalDataTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $column = new Column\ExternalData('myData');
        
        $this->assertEquals('myData', $column->getUniqueId());
        
        $this->assertFalse($column->isUserFilterEnabled());
        $this->assertFalse($column->isUserSortEnabled());
    }

    public function testSetGetData()
    {
        $column = new Column\ExternalData('myData');
        
        
        $object = new DataPopulation\Object();
        $object->setObject(new DataPopulation\Object\Gravatar());
        $this->assertEquals(false, $column->hasDataPopulation());
        
        $column->setDataPopulation($object);
        
        $this->assertEquals(true, $column->hasDataPopulation());
        $this->assertInstanceOf('ZfcDatagrid\Column\DataPopulation\Object', $column->getDataPopulation());
    }

    public function testException()
    {
         $column = new Column\ExternalData('myData');
        
        $object = new DataPopulation\Object();
        $this->setExpectedException('Exception');
        $column->setDataPopulation($object);
    }
}