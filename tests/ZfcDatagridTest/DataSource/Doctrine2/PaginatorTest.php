<?php
namespace ZfcDatagridTest\DataSource\Doctrine2;

use ZfcDatagrid\DataSource\Doctrine2\Paginator;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\Doctrine2\Paginator
 */
class PaginatorTest extends AbstractDoctrine2Test
{
    public function testConstruct()
    {
        $qb = $this->em->createQueryBuilder();

        $paginator = new Paginator($qb);

        $this->assertSame($qb, $paginator->getQueryBuilder());
    }
}
