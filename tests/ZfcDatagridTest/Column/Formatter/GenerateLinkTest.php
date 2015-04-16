<?php


namespace ZfcDatagridTest\Column\Formatter;


use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Column\Formatter\GenerateLink;

/**
 * @group Column
 * @covers ZfcDatagrid\Column\Formatter\GenerateLink
 */
class GenerateLinkTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $generateLink = new GenerateLink(new ServiceManager(), 'route');

        $this->assertInstanceOf('Zend\ServiceManager\ServiceManager', $generateLink->getServiceManager());
        $this->assertEquals('route', $generateLink->getRoute());
        $this->assertEmpty($generateLink->getRouteKey());
        $this->assertEmpty($generateLink->getRouteParams());
    }

    public function testGetFormattedValue()
    {
        $col = $this->getMockForAbstractClass('ZfcDatagrid\Column\AbstractColumn');
        $col->setUniqueId('foo');

        $phpRenderer = $this->getMockBuilder('Zend\View\Renderer\PhpRenderer')
            ->disableOriginalConstructor()
            ->getMock();

        $phpRenderer->expects($this->any())
            ->method('url')
            ->will($this->returnValue(''));

        $serviceManager = new ServiceManager();
        $serviceManager->setService('ViewRenderer', $phpRenderer);

        $generateLink = new GenerateLink($serviceManager, 'route');
        $generateLink->setRowData([
            'foo' => 'bar',
        ]);

        $this->assertEquals('<a href="">bar</a>', $generateLink->getFormattedValue($col));
    }

}
