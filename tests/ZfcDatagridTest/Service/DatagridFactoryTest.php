<?php
namespace ZfcDatagridTest\Service;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Service\DatagridFactory;

/**
 * @covers \ZfcDatagrid\Service\DatagridFactory
 */
class DatagridFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var array */
    private $config = [
        'ZfcDatagrid' => [
            'cache' => [
                'adapter' => [
                    'name' => 'Filesystem',
                ],
            ],
            'generalParameterNames' => [
                'rendererType' => null,
            ],
            'settings' => [
                'default' => [
                    'renderer' => [
                        'http' => 'bootstrapTable',
                    ],
                ],
            ],
        ],
    ];

    private $applicationMock;

    private $rendererServiceMock;

    public function setUp()
    {
        $mvcEventMock = $this->getMockBuilder(\Zend\Mvc\MvcEvent::class)
            ->getMock();

        $this->applicationMock = $this->getMockBuilder(\Zend\Mvc\Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->applicationMock->expects($this->any())
            ->method('getMvcEvent')
            ->will($this->returnValue($mvcEventMock));

        $this->rendererServiceMock = $this->getMockBuilder(\ZfcDatagrid\Renderer\BootstrapTable\Renderer::class)
            ->getMock();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Config key "ZfcDatagrid" is missing
     */
    public function testCreateServiceException()
    {
        $sm = new ServiceManager();
        $sm->setService('config', []);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);
    }

    public function testCanCreateService()
    {
        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        $sm->setService('zfcDatagrid.renderer.bootstrapTable', $this->rendererServiceMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf(\ZfcDatagrid\Datagrid::class, $grid);
    }

    public function testCanCreateServiceWithTranslator()
    {
        $translatorMock = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        $sm->setService('zfcDatagrid.renderer.bootstrapTable', $this->rendererServiceMock);
        $sm->setService('translator', $translatorMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf(\ZfcDatagrid\Datagrid::class, $grid);
        $this->assertEquals($translatorMock, $grid->getTranslator());
    }

    public function testCanCreateServiceWithMvcTranslator()
    {
        $mvcTranslatorMock = $this->getMockBuilder(\Zend\I18n\Translator\Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sm = new ServiceManager();
        $sm->setService('config', $this->config);
        $sm->setService('application', $this->applicationMock);
        $sm->setService('zfcDatagrid.renderer.bootstrapTable', $this->rendererServiceMock);
        $sm->setService('translator', $mvcTranslatorMock);

        $factory = new DatagridFactory();
        $grid    = $factory->createService($sm);

        $this->assertInstanceOf(\ZfcDatagrid\Datagrid::class, $grid);
        $this->assertEquals($mvcTranslatorMock, $grid->getTranslator());
    }
}
