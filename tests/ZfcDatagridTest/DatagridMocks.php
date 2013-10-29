<?php
namespace ZfcDatagridTest;

use PHPUnit_Framework_MockObject_Generator;

/**
 * This should get a general mock collection, to lower the code in tests
 *
 * @author kecmar
 *        
 */
class DatagridMocks
{

    /**
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public static function getColBasic()
    {
        return PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
    }

    /**
     *
     * @return \Zend\Paginator\Paginator
     */
    public static function getPaginator()
    {
        $testCollection = range(1, 101);
        return new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($testCollection));
    }
}