<?php
namespace ZfcDatagrid\Examples\Data\JqGrid;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PhpArray implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @return array
     */
    public function getData ()
    {
        $data[] = array(
            'id'      => 1,
            'invdate' => '2010-05-24',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 10.00,
            'total'   => 2111.00
        );
        $data[] = array(
            'id'      => 2,
            'invdate' => '2010-05-25',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 20.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 3,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 30.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 4,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 10.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 5,
            'invdate' => '2007-10-05',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 20.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 6,
            'invdate' => '2007-09-06',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 10.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 7,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 10.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 8,
            'invdate' => '2007-10-03',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 9,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 11,
            'invdate' => '2007-10-01',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 12,
            'invdate' => '2007-10-02',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 13,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 14,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 15,
            'invdate' => '2007-10-05',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 16,
            'invdate' => '2007-09-06',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 17,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 18,
            'invdate' => '2007-10-03',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 19,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 21,
            'invdate' => '2007-10-01',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 22,
            'invdate' => '2007-10-02',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 23,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 24,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 25,
            'invdate' => '2007-10-05',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 26,
            'invdate' => '2007-09-06',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );
        $data[] = array(
            'id'      => 27,
            'invdate' => '2007-10-04',
            'name'    => 'test',
            'note'    => 'note',
            'tax'     => 200.00,
            'total'   => 210.00
        );
        $data[] = array(
            'id'      => 28,
            'invdate' => '2007-10-03',
            'name'    => 'test2',
            'note'    => 'note2',
            'tax'     => 300.00,
            'total'   => 320.00
        );
        $data[] = array(
            'id'      => 29,
            'invdate' => '2007-09-01',
            'name'    => 'test3',
            'note'    => 'note3',
            'tax'     => 400.00,
            'total'   => 430.00
        );

        return $data;
    }
}
