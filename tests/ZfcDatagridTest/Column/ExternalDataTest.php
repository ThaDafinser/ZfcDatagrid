<?php
namespace ZfcDatagridTest\Column;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\DataPopulation;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\ExternalData
 */
class ExternalDataTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $col = new Column\ExternalData('myData');

        $this->assertEquals('myData', $col->getUniqueId());

        $this->assertFalse($col->isUserFilterEnabled());
        $this->assertFalse($col->isUserSortEnabled());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetDataPopulationException()
    {
        $col = new Column\ExternalData('myData');

        $col->getDataPopulation();
    }

    public function testSetGetData()
    {
        $col = new Column\ExternalData('myData');

        $object = new DataPopulation\Object();
        $object->setObject(new DataPopulation\Object\Gravatar());
        $this->assertEquals(false, $col->hasDataPopulation());

        $col->setDataPopulation($object);

        $this->assertEquals(true, $col->hasDataPopulation());
        $this->assertInstanceOf(\ZfcDatagrid\Column\DataPopulation\Object::class, $col->getDataPopulation());
    }

    /**
     * @expectedException \Exception
     */
    public function testException()
    {
        $col = new Column\ExternalData('myData');

        $object = new DataPopulation\Object();
        $col->setDataPopulation($object);
    }
}
