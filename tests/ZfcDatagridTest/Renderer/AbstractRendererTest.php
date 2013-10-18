<?php
namespace ZfcDatagridTest\Renderer;

use ZfcDatagrid\Column\DataPopulation;
use ZfcDatagrid\Filter;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use PHPUnit_Framework_TestCase;

/**
 * @group Column
 * @covers ZfcDatagrid\Renderer\AbstractRenderer
 */
class AbstractRendererTest extends PHPUnit_Framework_TestCase
{

    public function testOptions()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->setOptions(array(
            'test'
        ));
        
        $this->assertEquals(array(
            'test'
        ), $renderer->getOptions());
    }

    public function testRendererOptions()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $this->assertEquals(array(), $renderer->getOptionsRenderer());
        
        $renderer->setOptions(array(
            'renderer' => array(
                'abstract' => array(
                    'test'
                )
            )
        ));
        
        $this->assertEquals(array(
            'test'
        ), $renderer->getOptionsRenderer());
    }

    public function testViewModel()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        
        $this->assertNull($renderer->getViewModel());
        
        $viewModel = $this->getMock('Zend\View\Model\ViewModel');
        $renderer->setViewModel($viewModel);
        $this->assertSame($viewModel, $renderer->getViewModel());
    }

    public function testTemplate()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $this->assertEquals('zfc-datagrid/renderer/abstract/layout', $renderer->getTemplate());
        $this->assertEquals('zfc-datagrid/toolbar/toolbar', $renderer->getToolbarTemplate());
        
        $renderer->setTemplate('blubb/layout');
        $this->assertEquals('blubb/layout', $renderer->getTemplate());
        
        $renderer->setToolbarTemplate('blubb/toolbar');
        $this->assertEquals('blubb/toolbar', $renderer->getToolbarTemplate());
    }

    public function testTemplateConfig()
    {
        $renderer = $this->getMockForAbstractClass('ZfcDatagrid\Renderer\AbstractRenderer');
        $renderer->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('abstract'));
        
        $renderer->setOptions(array(
            'renderer' => array(
                'abstract' => array(
                    'templates' => array(
                        'layout' => 'config/my/template',
                        'toolbar' => 'config/my/toolbar'
                    )
                )
            )
        ));
        
        $this->assertEquals('config/my/template', $renderer->getTemplate());
        $this->assertEquals('config/my/toolbar', $renderer->getToolbarTemplate());
    }
}