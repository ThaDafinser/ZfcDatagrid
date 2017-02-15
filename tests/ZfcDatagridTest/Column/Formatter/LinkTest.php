<?php
namespace ZfcDatagridTest\Column\Formatter;

use PHPUnit_Framework_TestCase;
use Zend\Router\Http\HttpRouterFactory;
use Zend\Router\Http\Segment;
use Zend\Router\RoutePluginManagerFactory;
use ZfcDatagrid\Column\Formatter;
use ZfcDatagridTest\Util\ServiceManagerFactory;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\Link
 */
class LinkTest extends PHPUnit_Framework_TestCase
{
    public function testGetValidRendererNames()
    {
        $formatter = new Formatter\Link();

        $this->assertEquals([
            'jqGrid',
            'bootstrapTable',
        ], $formatter->getValidRendererNames());
    }

    public function getRouter()
    {
        $config = array(
            'router' => array(
                'routes' => array(
                    'myTestRoute' => array(
                        'type'    => Segment::class,
                        'options' => array(
                            'route'    => '/foo[/:bar]',
                            'defaults' => array(
                                'controller' => 'MyController',
                                'action'     => 'index',
                                'bar'        => 'baz',
                            ),
                        ),
                    ),
                ),
            ),
        );

        // Setup service manager, we need that for the route
        ServiceManagerFactory::setConfig( $config );
        $serviceLocator = ServiceManagerFactory::getServiceManager();

        $routePluginManager = new RoutePluginManagerFactory();
        $serviceLocator->setService('RoutePluginManager', $routePluginManager->createService($serviceLocator));
        $routerFactory = new HttpRouterFactory();

        return $routerFactory->createService($serviceLocator);
    }

    public function testRouteToLinkConversion()
    {
        // Setup a mock column
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        // Setup the formatter
        $formatter = new Formatter\Link();
        $formatter->setRouter($this->getRouter());
        $formatter->setRoute('myTestRoute');
        $formatter->setRouteParams(array('bar' => 'xyz'));
        $formatter->setRowData(array('myCol' => 'Test'));

        $this->assertEquals('<a href="/foo/xyz">Test</a>', $formatter->getFormattedValue($col));
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('myCol');

        $formatter = new Formatter\Link();
        $formatter->setRowData([
            'myCol' => 'http://example.com',
        ]);

        $this->assertEquals('<a href="http://example.com">http://example.com</a>', $formatter->getFormattedValue($col));
    }
}
