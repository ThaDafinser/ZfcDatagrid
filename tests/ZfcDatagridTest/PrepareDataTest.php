<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\PrepareData;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\DataPopulation\Object;

/**
 * @covers ZfcDatagrid\PrepareData
 */
class PrepareDataTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var array
     */
    private $data;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $colId;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $col1;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $col2;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    private $col3;

    public function setUp()
    {
        $this->colId = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->colId->setUniqueId('id');
        $this->colId->setIdentity(true);

        $this->col1 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->col1->setUniqueId('col1');

        $this->col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->col2->setUniqueId('col2');

        $this->col3 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $this->col3->setUniqueId('col3');
        $this->col3->setType(new Type\PhpArray());

        $data = array();
        $data[] = array(
            'id' => '1',
            'col1' => 'test',
            'col2' => 'n',
            'col3' => array(
                'tag1',
                'tag2',
            ),
        );
        $data[] = array(
            'id' => '2',
            'col1' => 'test',
            'col3' => array(
                'tag3',
                'tag1',
            ),
        );
        $data[] = array(
            'id' => '3',
            'col1' => 'test',
            'col2' => 'y',
            'col3' => array(
                'tag2',
                'tag5',
            ),
        );

        $this->data = $data;
    }

    public function testConstruct()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
            $this->col2,
        ));

        $this->assertEquals(array(), $prepare->getData(true));
        $this->assertEquals(array(
            $this->col1,
            $this->col2,
        ), $prepare->getColumns());
    }

    public function testColumns()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
        ));

        $prepare->setColumns(array(
            $this->col1,
            $this->col2,
        ));
        $this->assertEquals(array(
            $this->col1,
            $this->col2,
        ), $prepare->getColumns());
    }

    public function testRendererName()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
        ));

        $this->assertNull($prepare->getRendererName());

        $prepare->setRendererName('jqGrid');
        $this->assertEquals('jqGrid', $prepare->getRendererName());
    }

    public function testTranslator()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
        ));

        $translator = $this->getMock('Zend\I18n\Translator\Translator');

        $prepare->setTranslator($translator);
        $this->assertEquals($translator, $prepare->getTranslator());
    }

    public function testPrepareExecuteOnce()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
        ));

        $this->assertTrue($prepare->prepare());
        $this->assertEquals(array(), $prepare->getData());
        $this->assertFalse($prepare->prepare());
    }

    public function testPrepareEmptyData()
    {
        $prepare = new PrepareData(array(), array(
            $this->col1,
        ));

        $this->assertEquals(array(), $prepare->getData());
    }

    public function testPrepareDefault()
    {
        $data = $this->data;

        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $this->col2,
        ));

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[1]['col2'] = '';

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplace()
    {
        $data = $this->data;

        $col2 = clone $this->col2;
        $col2->setReplaceValues(array(
            'y' => 'yes',
            'n' => 'no',
        ), false);
        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $col2,
        ));

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[0]['col2'] = 'no';
        $data[1]['col2'] = '';
        $data[2]['col2'] = 'yes';

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplaceEmpty()
    {
        $data = $this->data;

        $col2 = clone $this->col2;
        $col2->setReplaceValues(array(
            'y' => 'yes',
        ), true);
        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $col2,
        ));

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[0]['col2'] = '';
        $data[1]['col2'] = '';
        $data[2]['col2'] = 'yes';

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplaceEmptyArray()
    {
        $data = $this->data;

        $col3 = clone $this->col3;
        $col3->setReplaceValues(array(
            'tag1' => 'Tag 1',
        ), true);
        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $this->col2,
            $col3,
        ));

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[1]['col2'] = '';

        $data[0]['col3'] = array(
            'Tag 1',
            '',
        );

        $data[1]['col3'] = array(
            '',
            'Tag 1',
        );

        $data[2]['col3'] = array(
            '',
            '',
        );

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplaceTranslate()
    {
        $data = $this->data;

        $col2 = clone $this->col2;
        $col2->setTranslationEnabled(true);
        $col2->setReplaceValues(array(
            'y' => 'yes',
            'n' => 'no',
        ));
        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $col2,
            $this->col3,
        ));

        $translator = $this->getMock('Zend\I18n\Translator\Translator');
        $translator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(function ($name) {
            switch ($name) {

                case 'yes':
                    return 'ja';
                    break;

                case 'no':
                    return 'nein';
                    break;

                case '':
                    return '';
                    break;
            }

            return $name;
        }));

        $prepare->setTranslator($translator);

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[0]['col2'] = 'nein';
        $data[1]['col2'] = '';
        $data[2]['col2'] = 'ja';

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplaceTranslateArray()
    {
        $data = $this->data;

        $col3 = clone $this->col3;
        $col3->setTranslationEnabled(true);
        $col3->setReplaceValues(array(
            'tag1' => 'Tag 1',
        ), false);
        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $this->col2,
            $col3,
        ));

        $translator = $this->getMock('Zend\I18n\Translator\Translator');
        $translator->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(function ($name) {
            switch ($name) {

                case 'tag2':
                    return 'Tag 2';
                    break;
            }

            return $name;
        }));

        $prepare->setTranslator($translator);

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[1]['col2'] = '';

        $data[0]['col3'] = array(
            'Tag 1', // replaced
            'Tag 2', // translated
                );

        $data[1]['col3'] = array(
            'tag3',
            'Tag 1', // translated
                );

        $data[2]['col3'] = array(
            'Tag 2', // replaced
            'tag5',
        );

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareDataPopulation()
    {
        $data = $this->data;

        $mock = $this->getMock('ZfcDatagrid\Column\DataPopulation\Object\Gravatar');
        $mock->expects($this->any())
            ->method('toString')
            ->will($this->returnValue('myReturn'));

        $object = new Object();
        $object->setObject($mock);
        $object->addObjectParameterColumn('email', $this->col1);

        $col = $this->getMock('ZfcDatagrid\Column\ExternalData');
        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('colPopulation'));
        $col->expects($this->any())
            ->method('getDataPopulation')
            ->will($this->returnValue($object));
        $col->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(new Type\String()));

        $prepare = new PrepareData($data, array(
            $this->colId,
            $this->col1,
            $this->col2,
            $col,
        ));

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[1]['col2'] = '';

        $data[0]['colPopulation'] = 'myReturn';
        $data[1]['colPopulation'] = 'myReturn';
        $data[2]['colPopulation'] = 'myReturn';

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareDataFormatter()
    {
        $data = $this->data;

        $col1 = clone $this->col1;
        $col1->setFormatter(new \ZfcDatagrid\Column\Formatter\Link());
        $prepare = new PrepareData($data, array(
            $this->colId,
            $col1,
            $this->col2,
        ));
        $prepare->setRendererName('jqGrid');

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[0]['col1'] = '<a href="test">test</a>';
        $data[1]['col1'] = '<a href="test">test</a>';
        $data[2]['col1'] = '<a href="test">test</a>';

        $data[1]['col2'] = '';

        $this->assertEquals($data, $prepare->getData());
    }
}
