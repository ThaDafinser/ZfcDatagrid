<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;

class DataSourceTestCase extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    protected $colVolumne;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    protected $colEdition;

    /**
     *
     * @var \ZfcDatagrid\Column\AbstractColumn
     */
    protected $colUserDisplayName;

    public function setUp()
    {
        $data = array();
        $data[] = array(
            'volume' => 67,
            'edition' => 2,
            'unneededCol' => 'something',
        );
        $data[] = array(
            'volume' => 86,
            'edition' => 1,
            'unneded' => 'blubb',
        );
        $data[] = array(
            'volume' => 85,
            'edition' => 6,
        );
        $data[] = array(
            'volume' => 98,
            'edition' => 2,
        );
        $data[] = array(
            'volume' => 86,
            'edition' => 6,
        );
        $data[] = array(
            'volume' => 67,
            'edition' => 7,
            'user' => array(
                'displayName' => 'Martin',
            ),
        );

        $this->data = $data;

        $col1 = new Column\Select('volume');
        // intl dependency...
        // $col1->setType(new Type\Number());
        $this->colVolumne = $col1;

        $col2 = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col2->setUniqueId('edition');
        $col2->setSelect('edition');
        $this->colEdition = $col2;

        $col3 = new Column\Select('displayName', 'user');
        $this->colUserDisplayName = $col3;
    }
}
