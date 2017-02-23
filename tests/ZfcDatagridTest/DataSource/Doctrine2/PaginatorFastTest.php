<?php
namespace ZfcDatagridTest\DataSource\Doctrine2;

use ZfcDatagrid\DataSource\Doctrine2\PaginatorFast;

/**
 * @group DataSource
 * @covers \ZfcDatagrid\DataSource\Doctrine2\PaginatorFast
 */
class PaginatorFastTest extends AbstractDoctrine2Test
{
    public function testConstruct()
    {
        $qb = $this->em->createQueryBuilder();

        $paginator = new PaginatorFast($qb);

        $this->assertSame($qb, $paginator->getQueryBuilder());
    }

}
