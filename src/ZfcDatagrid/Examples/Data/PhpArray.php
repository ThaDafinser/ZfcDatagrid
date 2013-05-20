<?php
namespace ZfcDatagrid\Examples\Data;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PhpArray implements ServiceLocatorAwareInterface
{

    private $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator            
     */
    public function setServiceLocator (ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator ()
    {
        return $this->serviceLocator;
    }

    /**
     *
     * @return array
     */
    public function getPersons ()
    {
        $row = array(
            'id' => 1,
            'displayName' => 'Wayne? John!',
            'familyName' => 'Wayne',
            'givenName' => 'John',
            'email' => 'unknown@gmail.com',
            'gender' => 'm',
            'age' => 35,
            'weight' => 50,
            'birthday' => '1987-10-03 00:00:00',
            'changeDate' => '2001-04-19 14:30:41'
        );
        $row2 = array(
            'id' => 2,
            'displayName' => 'Franz Ferdinand',
            'familyName' => 'Ferdinand',
            'givenName' => 'Franz',
            'email' => 'unknown@gmail.com',
            'gender' => 'm',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31 00:00:00',
            'changeDate' => '1999-12-31 22:30:41'
        );
        $row3 = array(
            'id' => 3,
            'displayName' => 'Peter Kaiser',
            'familyName' => 'Kaiser',
            'givenName' => 'Peter',
            'email' => 'unknown@test.com',
            'gender' => 'm',
            'age' => 23,
            'weight' => 70.23,
            'birthday' => '1991-10-03 00:00:00',
            'changeDate' => '2013-04-19 09:30:41'
        );
        $row4 = array(
            'id' => 5,
            'displayName' => 'Martin Keckeis',
            'familyName' => 'Keckeis',
            'givenName' => 'Martin',
            'email' => 'martin.keckeis1@gmail.com',
            'gender' => 'm',
            'age' => 25,
            'weight' => 70,
            'birthday' => '1987-10-03 00:00:00',
            'changeDate' => '2001-04-19 14:30:41'
        );
        $row5 = array(
            'id' => 5,
            'displayName' => 'Anna Marie Franz',
            'familyName' => 'Franz',
            'givenName' => 'Anna Marie',
            'email' => 'unknown@test.com',
            'gender' => 'f',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31 00:00:00',
            'changeDate' => '1999-12-31 22:30:41'
        );
        $row6 = array(
            'id' => 6,
            'displayName' => 'Sarah Blumenfeld',
            'familyName' => 'Blumenfeld',
            'givenName' => 'Sarah',
            'email' => 'unknown@test.com',
            'gender' => 'f',
            'age' => 23,
            'weight' => 70.23,
            'birthday' => '1991-10-03 00:00:00',
            'changeDate' => '2013-04-19 09:30:41'
        );
        
        $data = array(
            $row,
            $row2,
            $row3,
            $row4,
            $row5,
            $row6
        );
        
        return $data;
    }

    /**
     *
     * @return array
     */
    public function getCategorys ()
    {
        $id = 0;
        
        $data = array();
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => null,
            'hasChildren' => 'y',
            'name' => 'Root'
        );
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => $id - 1,
            'hasChildren' => 'y',
            'name' => 'Second level: entry 1',
            'level' => 1
        );
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => $id - 1,
            'hasChildren' => 'n',
            'name' => 'Third level: entry 1'
        );
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => $id - 2,
            'hasChildren' => 'n',
            'name' => 'Third level: entry 2'
        );
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => null,
            'hasChildren' => 'y',
            'name' => 'Root2'
        );
        
        $data[] = array(
            'id' => ++ $id,
            'parentId' => $id - 1,
            'hasChildren' => 'n',
            'name' => 'Second level of Root2',
            'level' => 1
        );
        
        return $data;
    }

    public function getRandom ()
    {
        $maxRow = rand(100, 200);
        
        $persons = $this->getPersons();
        
        $data = array();
        for ($i = 1; $i <= $maxRow; $i ++) {
            $row = array(
                'id' => $i,
                'number' => rand(0, 20000),
                'name' => $persons[rand(0, 5)]['displayName']
            );
            
            $data[] = $row;
        }
        
        return $data;
    }
}
