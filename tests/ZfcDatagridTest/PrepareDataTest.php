<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column\DataPopulation\Object;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\PrepareData;

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
        $this->colId = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->colId->setUniqueId('id');
        $this->colId->setIdentity(true);

        $this->col1 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->col1->setUniqueId('col1');

        $this->col2 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->col2->setUniqueId('col2');

        $this->col3 = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $this->col3->setUniqueId('col3');
        $this->col3->setType(new Type\PhpArray());

        $data   = [];
        $data[] = [
            'id'   => '1',
            'col1' => 'test',
            'col2' => 'n',
            'col3' => [
                'tag1',
                'tag2',
            ],
        ];
        $data[] = [
            'id'   => '2',
            'col1' => 'test',
            'col3' => [
                'tag3',
                'tag1',
            ],
        ];
        $data[] = [
            'id'   => '3',
            'col1' => 'test',
            'col2' => 'y',
            'col3' => [
                'tag2',
                'tag5',
            ],
        ];

        $this->data = $data;
    }

    public function testConstruct()
    {
        $prepare = new PrepareData([], [
            $this->col1,
            $this->col2,
        ]);

        $this->assertEquals([], $prepare->getData(true));
        $this->assertEquals([
            $this->col1,
            $this->col2,
        ], $prepare->getColumns());
    }

    public function testColumns()
    {
        $prepare = new PrepareData([], [
            $this->col1,
        ]);

        $prepare->setColumns([
            $this->col1,
            $this->col2,
        ]);
        $this->assertEquals([
            $this->col1,
            $this->col2,
        ], $prepare->getColumns());
    }

    public function testRendererName()
    {
        $prepare = new PrepareData([], [
            $this->col1,
        ]);

        $this->assertNull($prepare->getRendererName());

        $prepare->setRendererName('jqGrid');
        $this->assertEquals('jqGrid', $prepare->getRendererName());
    }

    public function testTranslator()
    {
        $prepare = new PrepareData([], [
            $this->col1,
        ]);

        $translator = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->getMock();

        $prepare->setTranslator($translator);
        $this->assertEquals($translator, $prepare->getTranslator());
    }

    public function testPrepareExecuteOnce()
    {
        $prepare = new PrepareData([], [
            $this->col1,
        ]);

        $this->assertTrue($prepare->prepare());
        $this->assertEquals([], $prepare->getData());
        $this->assertFalse($prepare->prepare());
    }

    public function testPrepareEmptyData()
    {
        $prepare = new PrepareData([], [
            $this->col1,
        ]);

        $this->assertEquals([], $prepare->getData());
    }

    public function testPrepareDefault()
    {
        $data = $this->data;

        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $this->col2,
        ]);

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
        $col2->setReplaceValues([
            'y' => 'yes',
            'n' => 'no',
        ], false);
        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $col2,
        ]);

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
        $col2->setReplaceValues([
            'y' => 'yes',
        ], true);
        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $col2,
        ]);

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
        $col3->setReplaceValues([
            'tag1' => 'Tag 1',
        ], true);
        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $this->col2,
            $col3,
        ]);

        $data[0]['idConcated'] = '1';
        $data[1]['idConcated'] = '2';
        $data[2]['idConcated'] = '3';

        $data[1]['col2'] = '';

        $data[0]['col3'] = [
            'Tag 1',
            '',
        ];

        $data[1]['col3'] = [
            '',
            'Tag 1',
        ];

        $data[2]['col3'] = [
            '',
            '',
        ];

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareReplaceTranslate()
    {
        $data = $this->data;

        $col2 = clone $this->col2;
        $col2->setTranslationEnabled(true);
        $col2->setReplaceValues([
            'y' => 'yes',
            'n' => 'no',
        ]);
        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $col2,
            $this->col3,
        ]);

        $translator = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->getMock();
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
        $col3->setReplaceValues([
            'tag1' => 'Tag 1',
        ], false);
        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $this->col2,
            $col3,
        ]);

        $translator = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->getMock();
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

        $data[0]['col3'] = [
            'Tag 1', // replaced
            'Tag 2', // translated
                ];

        $data[1]['col3'] = [
            'tag3',
            'Tag 1', // translated
                ];

        $data[2]['col3'] = [
            'Tag 2', // replaced
            'tag5',
        ];

        $this->assertEquals($data, $prepare->getData());
    }

    public function testPrepareDataPopulation()
    {
        $data = $this->data;

        $mock = $this->getMockBuilder(\ZfcDatagrid\Column\DataPopulation\Object\Gravatar::class)
            ->getMock();
        $mock->expects($this->any())
            ->method('toString')
            ->will($this->returnValue('myReturn'));

        $object = new Object();
        $object->setObject($mock);
        $object->addObjectParameterColumn('email', $this->col1);

        $col = $this->getMockBuilder(\ZfcDatagrid\Column\ExternalData::class)
            ->getMock();
        $col->expects($this->any())
            ->method('getUniqueId')
            ->will($this->returnValue('colPopulation'));
        $col->expects($this->any())
            ->method('getDataPopulation')
            ->will($this->returnValue($object));
        $col->expects($this->any())
            ->method('getType')
            ->will($this->returnValue(new Type\PhpString()));

        $prepare = new PrepareData($data, [
            $this->colId,
            $this->col1,
            $this->col2,
            $col,
        ]);

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
        $col1->addFormatter(new \ZfcDatagrid\Column\Formatter\Link());
        $prepare = new PrepareData($data, [
            $this->colId,
            $col1,
            $this->col2,
        ]);
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
