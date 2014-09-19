<?php
namespace ZfcDatagridTest\Column\Type;

use ZfcDatagrid\Filter;
// use ZfcDatagrid\Column\Type;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Type\AbstractType
 */
class AbstractTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\Column\Type\AbstractType
     */
    private $type;

    public function setUp()
    {
        $this->type = $this->getMockForAbstractClass('ZfcDatagrid\Column\Type\AbstractType');
    }

    public function testGetFilterDefaultOperation()
    {
        $this->assertEquals(Filter::LIKE, $this->type->getFilterDefaultOperation());
    }

    public function testGetFilterValue()
    {
        $this->assertEquals('01.05.12', $this->type->getFilterValue('01.05.12'));
    }

    public function testGetUserValue()
    {
        $this->assertEquals('01.05.12', $this->type->getUserValue('01.05.12'));
    }
}
