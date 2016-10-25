<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\Type\Number;
use ZfcDatagrid\Filter;

/**
 * @covers \ZfcDatagrid\Filter
 */
class FilterTest extends PHPUnit_Framework_TestCase
{
    /** @var \ZfcDatagrid\Column\AbstractColumn|\PHPUnit_Framework_MockObject_MockObject  */
    private $column;

    public function setUp()
    {
        $this->column = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
    }

    public function tearDown()
    {
        $this->column = null;
    }

    public function testDefaultOperator()
    {
        $filter = new Filter();

        $this->assertFalse($filter->isColumnFilter());

        $filter->setFromColumn($this->column, 'test');
        $this->assertEquals($this->column, $filter->getColumn());
        $this->assertEquals(Filter::LIKE, $filter->getOperator());
//         $this->assertEquals('~ *test*', $filter->getDisplayColumnValue());
        $this->assertTrue(is_array($filter->getValues()));
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        $this->assertTrue($filter->isColumnFilter());
    }

    public function testEqualEmpty()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '=');
        $this->assertEquals($this->column, $filter->getColumn());
        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
//         $this->assertEquals('= ', $filter->getDisplayColumnValue());
        $this->assertTrue(is_array($filter->getValues()));
        $this->assertEquals([
            '',
        ], $filter->getValues());

        $this->assertTrue($filter->isColumnFilter());
    }

    public function testLike()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~test');
        $this->assertEquals(Filter::LIKE, $filter->getOperator());
//         $this->assertEquals('~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~*test*');
        $this->assertEquals(Filter::LIKE, $filter->getOperator());
//         $this->assertEquals('~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 3
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~%test%');
        $this->assertEquals(Filter::LIKE, $filter->getOperator());
//         $this->assertEquals('~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testLikeLeft()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~%test');
        $this->assertEquals(Filter::LIKE_LEFT, $filter->getOperator());
//         $this->assertEquals('~ *test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~*test');
        $this->assertEquals(Filter::LIKE_LEFT, $filter->getOperator());
//         $this->assertEquals('~ *test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testLikeRight()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~test%');
        $this->assertEquals(Filter::LIKE_RIGHT, $filter->getOperator());
//         $this->assertEquals('~ test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '~test*');
        $this->assertEquals(Filter::LIKE_RIGHT, $filter->getOperator());
//         $this->assertEquals('~ test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testNotLike()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~test');
        $this->assertEquals(Filter::NOT_LIKE, $filter->getOperator());
//         $this->assertEquals('!~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~*test*');
        $this->assertEquals(Filter::NOT_LIKE, $filter->getOperator());
//         $this->assertEquals('!~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 3
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~%test%');
        $this->assertEquals(Filter::NOT_LIKE, $filter->getOperator());
//         $this->assertEquals('!~ *test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testNotLikeLeft()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~%test');
        $this->assertEquals(Filter::NOT_LIKE_LEFT, $filter->getOperator());
//         $this->assertEquals('!~ *test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~*test');
        $this->assertEquals(Filter::NOT_LIKE_LEFT, $filter->getOperator());
//         $this->assertEquals('!~ *test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testNotLikeRight()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~test%');
        $this->assertEquals(Filter::NOT_LIKE_RIGHT, $filter->getOperator());
//         $this->assertEquals('!~ test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!~test*');
        $this->assertEquals(Filter::NOT_LIKE_RIGHT, $filter->getOperator());
//         $this->assertEquals('!~ test*', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testEqual()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '=test');
        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
//         $this->assertEquals('= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '==test');
        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
//         $this->assertEquals('= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Case 3
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '==test,value2');
        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
//         $this->assertEquals('= test,value2', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
            'value2',
        ], $filter->getValues());
    }

    public function testNotEqual()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!test');
        $this->assertEquals(Filter::NOT_EQUAL, $filter->getOperator());
//         $this->assertEquals('!= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());

        /*
         * Equal 2
         */
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!=test');
        $this->assertEquals(Filter::NOT_EQUAL, $filter->getOperator());
//         $this->assertEquals('!= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testGreaterEqual()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '>=test');
        $this->assertEquals(Filter::GREATER_EQUAL, $filter->getOperator());
//         $this->assertEquals('>= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testGreater()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '>test');
        $this->assertEquals(Filter::GREATER, $filter->getOperator());
//         $this->assertEquals('> test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testLessEqual()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '<=test');
        $this->assertEquals(Filter::LESS_EQUAL, $filter->getOperator());
//         $this->assertEquals('<= test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testLess()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '<test');
        $this->assertEquals(Filter::LESS, $filter->getOperator());
//         $this->assertEquals('< test', $filter->getDisplayColumnValue());
        $this->assertEquals([
            'test',
        ], $filter->getValues());
    }

    public function testIn()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '=(test,test2)');
        $this->assertEquals(Filter::IN, $filter->getOperator());
//         $this->assertEquals('=(test,test2)', $filter->getDisplayColumnValue());

        // @todo wrong?
        $this->assertEquals([
            'test',
            'test2',
        ], $filter->getValues());
    }

    public function testNotIn()
    {
        $filter = new Filter();

        $filter->setFromColumn($this->column, '!=(test,test2)');
        $this->assertEquals(Filter::NOT_IN, $filter->getOperator());
//         $this->assertEquals('!=(test,test2)', $filter->getDisplayColumnValue());

        // @todo wrong?
        $this->assertEquals([
            'test',
            'test2',
        ], $filter->getValues());
    }

    public function testBetween()
    {
        $filter = new Filter();
        $filter->setFromColumn($this->column, '2<>3');

        $this->assertEquals(Filter::BETWEEN, $filter->getOperator());
//         $this->assertEquals('2 <> 3', $filter->getDisplayColumnValue());
        $this->assertEquals([
            '2',
            '3',
        ], $filter->getValues());

        $filter = new Filter();
        $filter->setFromColumn($this->column, '2<>3 <>4');

        $this->assertEquals(Filter::BETWEEN, $filter->getOperator());
//         $this->assertEquals('2 <> 4', $filter->getDisplayColumnValue());
        $this->assertEquals([
            '2',
            '4',
        ], $filter->getValues());
    }

    public function testIsArrayComma()
    {
        $filter = new Filter();
        $filter->setFromColumn($this->column, '=2,5');

        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
        $this->assertEquals([2, 5], $filter->getValues());
    }

    public function testIsArrayCommaWithNumber()
    {
        $number = new Number();
        $number->setLocale('en');

        $this->column->setType($number);

        $filter = new Filter();
        $filter->setFromColumn($this->column, '=2,5');

        $this->assertEquals(Filter::EQUAL, $filter->getOperator());
        $this->assertSame(['2,5'], $filter->getValues());
    }

    public function testIsApplyLike()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('123', '123', Filter::LIKE));
        $this->assertTrue($filter->isApply('123asdf', '123', Filter::LIKE));
        $this->assertTrue($filter->isApply('text123text', '123', Filter::LIKE));
        $this->assertTrue($filter->isApply('asdf myString', 'myString', Filter::LIKE));
        $this->assertTrue($filter->isApply('smallWritten', 'SMALLWRITTEN', Filter::LIKE));

        $this->assertFalse($filter->isApply('smallWritten', 'somethingDifferent', Filter::LIKE));
    }

    public function testIsApplyLikeLeft()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('123', '123', Filter::LIKE_LEFT));
        $this->assertTrue($filter->isApply('asdf123', '123', Filter::LIKE_LEFT));
        $this->assertTrue($filter->isApply('smallWritten', 'SMALLWRITTEN', Filter::LIKE_LEFT));

        $this->assertFalse($filter->isApply('text123text', '123', Filter::LIKE_LEFT), '123 %~ text123text');
        $this->assertFalse($filter->isApply('myString asdf', 'myString', Filter::LIKE_LEFT), 'myString %~ myString asdf');
        $this->assertFalse($filter->isApply('smallWritten', 'somethingDifferent', Filter::LIKE_LEFT), 'smallWritten %~ somethingDifferent');
    }

    public function testIsApplyLikeRight()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('123', '123', Filter::LIKE_RIGHT));
        $this->assertTrue($filter->isApply('123asdf', 123, Filter::LIKE_RIGHT));
        $this->assertTrue($filter->isApply('smallWritten', 'SMALLWRITTEN', Filter::LIKE_RIGHT));

        $this->assertFalse($filter->isApply('text123text', '123', Filter::LIKE_RIGHT));
        $this->assertFalse($filter->isApply('asdf myString', 'myString', Filter::LIKE_RIGHT));
        $this->assertFalse($filter->isApply('smallWritten', 'somethingDifferent', Filter::LIKE_RIGHT));
    }

    public function testIsApplyNotLike()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply(123, 456, Filter::NOT_LIKE));

        $this->assertFalse($filter->isApply('test123', 123, Filter::NOT_LIKE));
        $this->assertFalse($filter->isApply('123test', 123, Filter::NOT_LIKE));
        $this->assertFalse($filter->isApply('test123test', 123, Filter::NOT_LIKE));
        $this->assertFalse($filter->isApply(123, '123', Filter::NOT_LIKE));
    }

    public function testIsApplyNotLikeLeft()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply(123, 456, Filter::NOT_LIKE_LEFT));
        $this->assertTrue($filter->isApply(1234, 123, Filter::NOT_LIKE_LEFT));

        $this->assertFalse($filter->isApply(123, '123', Filter::NOT_LIKE_LEFT));
        $this->assertFalse($filter->isApply('asdf123', 123, Filter::NOT_LIKE_LEFT));
    }

    public function testIsApplyNotLikeRight()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply(123, 456, Filter::NOT_LIKE_RIGHT));
        $this->assertTrue($filter->isApply(4123, 123, Filter::NOT_LIKE_RIGHT));

        $this->assertFalse($filter->isApply(123, '123', Filter::NOT_LIKE_RIGHT));
        $this->assertFalse($filter->isApply('123asdf', 123, Filter::NOT_LIKE_RIGHT));
    }

    public function testIsApplyEqual()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('123', 123, Filter::EQUAL));
        $this->assertTrue($filter->isApply(123, '123', Filter::EQUAL));
        $this->assertTrue($filter->isApply('myString', 'myString', Filter::EQUAL));

        $this->assertFalse($filter->isApply('myString', 'MYSTRING', Filter::EQUAL));
        $this->assertFalse($filter->isApply('smallWritten', 'SMALLWRITTEN', Filter::EQUAL));
        $this->assertFalse($filter->isApply('smallWritten', 'somethingDifferent', Filter::EQUAL));
    }

    public function testIsApplyNotEqual()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('smallWritten', 'somethingDifferent', Filter::NOT_EQUAL));
        $this->assertTrue($filter->isApply('myString', 'MYSTRING', Filter::NOT_EQUAL));
        $this->assertTrue($filter->isApply('smallWritten', 'SMALLWRITTEN', Filter::NOT_EQUAL));

        $this->assertFalse($filter->isApply('123', 123, Filter::NOT_EQUAL));
        $this->assertFalse($filter->isApply(123, '123', Filter::NOT_EQUAL));
        $this->assertFalse($filter->isApply('myString', 'myString', Filter::NOT_EQUAL));
    }

    public function testIsApplyGreaterEqual()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply('123', 123, Filter::GREATER_EQUAL));
        $this->assertTrue($filter->isApply(123, 100, Filter::GREATER_EQUAL));
        $this->assertTrue($filter->isApply('123.5', 100, Filter::GREATER_EQUAL));
        $this->assertTrue($filter->isApply('myString', 'myString', Filter::GREATER_EQUAL));

        $this->assertFalse($filter->isApply('123', 150, Filter::GREATER_EQUAL));
        $this->assertFalse($filter->isApply(149.99, 150, Filter::GREATER_EQUAL));
    }

    public function testIsApplyGreater()
    {
        $filter = new Filter();

        $this->assertTrue($filter->isApply(123, 100, Filter::GREATER));
        $this->assertTrue($filter->isApply('123.5', 100, Filter::GREATER));
        $this->assertTrue($filter->isApply('nyString', 'myString', Filter::GREATER));

        $this->assertFalse($filter->isApply('150', 150, Filter::GREATER));
        $this->assertFalse($filter->isApply(149, 150, Filter::GREATER));
    }

    public function testIsApplyLessEqual()
    {
        $filter = new Filter();

        $this->assertFalse($filter->isApply(123, 100, Filter::LESS_EQUAL));
        $this->assertFalse($filter->isApply('123.5', 100, Filter::LESS_EQUAL));

        $this->assertTrue($filter->isApply('myString', 'myString', Filter::LESS_EQUAL));
        $this->assertTrue($filter->isApply('123', 123, Filter::LESS_EQUAL));
        $this->assertTrue($filter->isApply('123', 150, Filter::LESS_EQUAL));
        $this->assertTrue($filter->isApply(149.99, 150, Filter::LESS_EQUAL));
    }

    public function testIsApplyLess()
    {
        $filter = new Filter();

        $this->assertFalse($filter->isApply(123, 100, Filter::LESS));
        $this->assertFalse($filter->isApply('123.5', 100, Filter::LESS));
        $this->assertFalse($filter->isApply('nyString', 'myString', Filter::LESS));
        $this->assertFalse($filter->isApply('150', 150, Filter::LESS));

        $this->assertTrue($filter->isApply(149, 150, Filter::LESS));
    }

    public function testIsApplyBetween()
    {
        $filter = new Filter();
        $this->assertTrue($filter->isApply(70, [
            50,
            100,
        ], Filter::BETWEEN));
        $this->assertTrue($filter->isApply(50, [
            50,
            100,
        ], Filter::BETWEEN));
        $this->assertTrue($filter->isApply(100, [
            50,
            100,
        ], Filter::BETWEEN));

        $this->assertFalse($filter->isApply(49, [
            50,
            100,
        ], Filter::BETWEEN));
        $this->assertFalse($filter->isApply(101, [
            50,
            100,
        ], Filter::BETWEEN));

        $this->assertFalse($filter->isApply(49.99, [
            50,
            100,
        ], Filter::BETWEEN));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsApplyBetweenInvalidArgumentException()
    {
        $filter = new Filter();

        $filter->isApply(123, 100, Filter::BETWEEN);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsApplyInvalidArgumentException()
    {
        $filter = new Filter();

        $filter->isApply(123, 100, 'UndefinedFilter');
    }
}
