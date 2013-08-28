<?php
namespace ZfcDatagridTest\DataSource;

use PHPUnit_Framework_TestCase;
use ZfcDatagrid\Filter;
use ZfcDatagrid\DataSource\Doctrine2;
use ZfcDatagridTest\DataSource\Doctrine2\AbstractDoctrine2Test;

/**
 * @group DataSource
 * @covers ZfcDatagrid\DataSource\Doctrine2
 */
class Doctrine2Test extends AbstractDoctrine2Test
{

    public function testConstruct()
    {
        $data = $this->em->createQueryBuilder();
        
        $source = new Doctrine2($data);

        $this->assertInstanceOf('Doctrine\ORM\QueryBuilder', $source->getData());
        $this->assertSame($data, $source->getData());
        
        $this->setExpectedException('InvalidArgumentException');
        $source = new Doctrine2(new \stdClass('something'));
        
    }
}