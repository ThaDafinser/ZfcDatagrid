<?php
namespace ZfcDatagrid\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Renderer\AbstractRenderer;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;

class ExampleController extends AbstractActionController
{

    /**
     * Usage
     * php index.php show example grid --page 1
     * php index.php show example grid --page 2
     * 
     * @return \Zend\Http\Response\Stream
     */
    public function consoleAction ()
    {
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setRowsPerPage(5);
        
        $row = array(
            'id' => 1,
            'displayName' => 'Very loooooong name..........',
            'familyName' => 'Family',
            'givenName' => '123',
            'age' => 35,
            'weight' => 50,
            'birthday' => '1987-10-03',
            'changeDate' => '14:30:41 19.04.01'
        );
        $row2 = array(
            'id' => 2,
            'displayName' => 'Aaa',
            'givenName' => 'Bbb',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31',
            'changeDate' => '22:30:41 31.12.99'
        );
        $row3 = array(
            'id' => 3,
            'displayName' => 'Aaa',
            'givenName' => 'Aaaa',
            'age' => 23,
            'weight' => 70.23,
            'birthday' => '1991-10-03',
            'changeDate' => '09:30:41 19.04.13'
        );
        $row4 = array(
            'id' => 5,
            'displayName' => 'Zzz',
            'familyName' => 'Family',
            'givenName' => '123',
            'age' => 35,
            'weight' => 50,
            'birthday' => '1987-10-03',
            'changeDate' => '14:30:41 19.04.01'
        );
        $row5 = array(
            'id' => 5,
            'displayName' => 'Aaa',
            'givenName' => 'Ccc',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31',
            'changeDate' => '22:30:41 31.12.99'
        );
        $row6 = array(
            'id' => 6,
            'displayName' => 'Aaa',
            'givenName' => 'Aaaa',
            'age' => 23,
            'weight' => 70.23,
            'birthday' => '1991-10-03',
            'changeDate' => '09:30:41 19.04.13'
        );
        $data = array(
            $row,
            $row2,
            $row3,
            $row4,
            $row5,
            $row6
        );
        $dataGrid->setDataSource($data);
        
        $col = new Column\Standard('id');
        $col->setIdentity();
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('displayName');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->addStyle(new Style\Bold());
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('familyName');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('givenName');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('age');
        $col->setLabel('Age');
        $col->setWidth(10);
        $col->setType(new Type\Number());
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }
}
