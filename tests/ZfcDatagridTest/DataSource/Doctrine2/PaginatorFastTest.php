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

    public function testGetItems()
    {
        //         $qb = $this->em->createQueryBuilder();
//         $qb->select('table1');
//         $qb->from('ZfcDatagridTest\DataSource\Doctrine2\Assets\Entity\Category', 'table1');

//         $paginator = new PaginatorFast($qb);

//         $this->assertEquals(array(), $paginator->getItems(0, 5));
    }
}
