<?php
namespace ZfcDatagrid\Examples\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Input\StringInput;
use ZfcDatagrid\Examples\Entity\Person;

class Doctrine2 implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    private function createTables()
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_zfcDatagrid');

        /* @var $cli \Symfony\Component\Console\Application */
        $cli = $this->getServiceLocator()->get('doctrine.cli');
        $helperSet = $cli->getHelperSet();
        $helperSet->set(new EntityManagerHelper($entityManager), 'em');

        $fp = tmpfile();

        $input = new StringInput('orm:schema-tool:create');

        /* @var $command \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand */
        $command = $cli->get('orm:schema-tool:create');
        $command->run($input, new StreamOutput($fp));

        $phpArray = $this->getServiceLocator()->get('zfcDatagrid.examples.data.phpArray');
        $persons = $phpArray->getPersons();

        $this->createData(new Person(), $persons);
    }

    private function createData($entity, $data)
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_zfcDatagrid');

        foreach ($data as $row) {

            $newEntity = clone $entity;
            foreach ($row as $key => $value) {
                $method = 'set' . ucfirst($key);
                $newEntity->{$method}($value);
            }

            $entityManager->persist($newEntity);
        }

        $entityManager->flush();
    }

    /**
     *
     * @return QueryBuilder
     */
    public function getPersons()
    {
        /* @var $entityManager \Doctrine\ORM\EntityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_zfcDatagrid');
        $personRepo = $entityManager->getRepository('ZfcDatagrid\Examples\Entity\Person');

        // Test if the SqLite is ready...
        try {
            $personRepo->find(2);
        } catch (\Exception $e) {
            $this->createTables();
            $data = $personRepo->find(2);
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('p');
        $qb->from('ZfcDatagrid\Examples\Entity\Person', 'p');

        return $qb;
    }
}
