<?php
namespace ZfcDatagridTest\DataSource\PhpArray;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\PhpArray\Filter as FilterArray;
use ZfcDatagrid\Filter;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\PhpArray\Filter
 */
class FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $column;

    public function setUp()
    {
        $this->column = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->column->setUniqueId('myCol');
    }

    public function testConstruct()
    {
        /* @var $filter \ZfcDatagrid\Filter */
        $filter = $this->getMock('ZfcDatagrid\Filter');
        $filter->setFromColumn($this->column, 'myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertInstanceOf('ZfcDatagrid\Filter', $filterArray->getFilter());
    }

    public function testLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123');
        $this->assertEquals(Filter::LIKE, $filter->getOperator());

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~%myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'asdfsdf123',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'something.... myValue',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123%');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123asdf',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'myValue....something',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'something.... myValue',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '4123',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    /**
     * Test NOT LIKE is just a copy from testLike -> because it's just swapped
     */
    public function testNotLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testNotLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~%myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'asdfsdf123',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'something.... myValue',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testNotLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue,123%');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123asdf',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'myValue....something',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'something.... myValue',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '4123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '51237',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'myValue',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'myvalue',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));
    }

    public function testNotEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'myValue',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'myvalue',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '1234',
        )));
    }

    public function testGreaterEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '322',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '11',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '00',
        )));
    }

    public function testGreater()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '322',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '11',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '00',
        )));
    }

    public function testLessEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<=123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '11',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '00',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '322',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'ZZZ',
        )));
    }

    public function testLess()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '322',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '11',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '00',
        )));
    }

    public function testIN()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '=(myValue,123)');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => 'myValue',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testNotIN()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!=(myValue,123)');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => 'myValue',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '123',
        )));

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '321',
        )));
    }

    public function testBetween()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '15 <> 30');
        $this->assertEquals(Filter::BETWEEN, $filter->getOperator());

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '15',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '20',
        )));
        $this->assertTrue($filterArray->applyFilter(array(
            'myCol' => '30',
        )));

        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '14',
        )));
        $this->assertFalse($filterArray->applyFilter(array(
            'myCol' => '31',
        )));
    }

    public function testException()
    {
        $filter = $this->getMock('ZfcDatagrid\Filter');
        $filter->expects($this->any())
            ->method('getColumn')
            ->will($this->returnValue($this->column));
        $filter->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue(array(
            1,
        )));
        $filter->expects($this->any())
            ->method('getOperator')
            ->will($this->returnValue(' () '));

        $this->setExpectedException('InvalidArgumentException');

        $filterArray = new FilterArray($filter);
        $filterArray->applyFilter(array(
            'myCol' => '15',
        ));
    }
}
