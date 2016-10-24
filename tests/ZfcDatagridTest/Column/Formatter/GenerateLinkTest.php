<?php
namespace ZfcDatagridTest\Column\Formatter;

use Zend\ServiceManager\ServiceManager;
use ZfcDatagrid\Column\Formatter\GenerateLink;

/**
 * @group Column
 * @covers \ZfcDatagrid\Column\Formatter\GenerateLink
 */
class GenerateLinkTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        /** @var \Zend\View\Renderer\PhpRenderer $phpRenderer */
        $phpRenderer = $this->getMockBuilder(\Zend\View\Renderer\PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $generateLink = new GenerateLink($phpRenderer, 'route');

        $this->assertEquals('route', $generateLink->getRoute());
        $this->assertEmpty($generateLink->getRouteKey());
        $this->assertEmpty($generateLink->getRouteParams());
    }
    public function testConstructorFallBackVersion()
    {
        $phpRenderer = $this->getMockBuilder(\Zend\View\Renderer\PhpRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sm = new ServiceManager();
        $sm->setService('ViewRenderer', $phpRenderer);

        $generateLink = new GenerateLink($sm, 'route');

        $this->assertEquals('route', $generateLink->getRoute());
        $this->assertEmpty($generateLink->getRouteKey());
        $this->assertEmpty($generateLink->getRouteParams());
    }

    public function testGetFormattedValue()
    {
        /** @var \ZfcDatagrid\Column\AbstractColumn $col */
        $col = $this->getMockForAbstractClass(\ZfcDatagrid\Column\AbstractColumn::class);
        $col->setUniqueId('foo');

        $phpRenderer = $this->getMockBuilder(\Zend\View\Renderer\PhpRenderer::class)
            ->disableOriginalConstructor()
            ->setMethods(['url'])
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
