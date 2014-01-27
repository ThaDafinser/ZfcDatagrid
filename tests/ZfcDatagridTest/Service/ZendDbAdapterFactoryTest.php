<?php
namespace ZfcDatagridTest\Service;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Service\DatagridFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * @covers ZfcDatagrid\Service\DatagridFactory
 */
class DatagridFactoryTest extends PHPUnit_Framework_TestCase
{

    private $config = array(
        'ZfcDatagrid' => array(
            'cache' => array(
                'adapter' => array(
                    'name' => 'Filesystem'
                )
            )
        )
    );

    private $applicationMock;

    private $translatorMock;

    public function setUp()
    {
        $mvcEventMock = $this->getMock('Zend\Mvc\MvcEvent');
        
        $this->applicationMock = $this->getMock('Zend\Mvc\Application', array(), array(), '', false);
        $this->applicationMock->expects($this->any())
            ->method('getMvcEvent')
            ->will($this->returnValue($mvcEventMock));
        
        $this->translatorMock = $this->getMock('Zend\I18n\Translator\Translator', array(), array(), '', false);
    }

    public function testCanCreateService()
    {
        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        
        $factory = new DatagridFactory();
        $grid = $factory->createService($sm);
        
        $this->assertInstanceOf('ZfcDatagrid\Datagrid', $grid);
    }
}
