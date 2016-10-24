<?php
namespace ZfcDatagridTest\DataSource\PhpArray;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\DataSource\PhpArray\Filter as FilterArray;
use ZfcDatagrid\Filter;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\PhpArray\Filter
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
        $this->column = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->column->setUniqueId('myCol');
    }

    public function testConstruct()
    {
        /* @var $filter \ZfcDatagrid\Filter */
        $filter = $this->getMockBuilder(\ZfcDatagrid\Filter::class)
            ->getMock();
        $filter->setFromColumn($this->column, 'myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertInstanceOf(\ZfcDatagrid\Filter::class, $filterArray->getFilter());
    }

    public function testLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123');
        $this->assertEquals(Filter::LIKE, $filter->getOperator());

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '1234',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~%myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'asdfsdf123',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'something.... myValue',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '1234',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '~myValue,123%');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123asdf',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'myValue....something',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'something.... myValue',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '4123',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    /**
     * Test NOT LIKE is just a copy from testLike -> because it's just swapped
     */
    public function testNotLike()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '1234',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testNotLikeLeft()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~%myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'asdfsdf123',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'something.... myValue',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '1234',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testNotLikeRight()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!~myValue,123%');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123asdf',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'myValue....something',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'something.... myValue',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '4123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '51237',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'myValue',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'myvalue',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '1234',
        ]));
    }

    public function testNotEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'myValue',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'myvalue',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '1234',
        ]));
    }

    public function testGreaterEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>=myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '322',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '11',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '00',
        ]));
    }

    public function testGreater()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '>myValue,123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '322',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '11',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '00',
        ]));
    }

    public function testLessEqual()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<=123');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '11',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '00',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '322',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'ZZZ',
        ]));
    }

    public function testLess()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '<123');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '322',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '11',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '00',
        ]));
    }

    public function testIN()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '=(myValue,123)');

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => 'myValue',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testNotIN()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '!=(myValue,123)');

        $filterArray = new FilterArray($filter);

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => 'myValue',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '123',
        ]));

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '321',
        ]));
    }

    public function testBetween()
    {
        $filter = new \ZfcDatagrid\Filter();
        $filter->setFromColumn($this->column, '15 <> 30');
        $this->assertEquals(Filter::BETWEEN, $filter->getOperator());

        $filterArray = new FilterArray($filter);

        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '15',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '20',
        ]));
        $this->assertTrue($filterArray->applyFilter([
            'myCol' => '30',
        ]));

        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '14',
        ]));
        $this->assertFalse($filterArray->applyFilter([
            'myCol' => '31',
        ]));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testException()
    {
        $filter = $this->getMockBuilder(\ZfcDatagrid\Filter::class)
            ->getMock();
        $filter->expects($this->any())
            ->method('getColumn')
            ->will($this->returnValue($this->column));
        $filter->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue([
            1,
        ]));
        $filter->expects($this->any())
            ->method('getOperator')
            ->will($this->returnValue(' () '));

        $filterArray = new FilterArray($filter);
        $filterArray->applyFilter([
            'myCol' => '15',
        ]);
    }
}
