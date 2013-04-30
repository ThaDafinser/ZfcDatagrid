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
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction ()
    {
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setItemsPerPage(5);
        $dataGrid->setRowClickLink('/zfcDatagrid/example/edit');
        $dataGrid->setDataSource($this->getDataArray());
        
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
        $col->setSortDefault(2, 'DESC');
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('gender');
        $col->setLabel('Gender');
        $col->setWidth(10);
        $col->setReplaceValues(array(
            'm' => 'male',
            'f' => 'female'
        ));
        $col->setTranslationEnabled(true);
        $dataGrid->addColumn($col);
        
        {
            $col = new Column\Standard('age');
            $col->setLabel('Age');
            $col->setWidth(5);
            $col->setType(new Type\Number());
            
            $style = new Style\Color\Red();
            $style->setByValue($col, 20);
            $col->addStyle($style);
            
            $dataGrid->addColumn($col);
        }
        
        {
            $colType = new Type\Number();
            $colType->addAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
            $colType->setSuffix(' kg');
            
            $col = new Column\Standard('weight');
            $col->setLabel('Weight');
            $col->setWidth(10);
            $col->setType($colType);
            $dataGrid->addColumn($col);
        }
        
        $col = new Column\Standard('birthday');
        $col->setLabel('Birthday');
        $col->setWidth(10);
        $col->setType(new Type\Date());
        $col->setUserSortDisabled(true);
        $dataGrid->addColumn($col);
        
        {
            $colType = new Type\Date('H:i:s d.m.y', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
            $colType->setSourceTimezone('Europe/Vienna');
            $colType->setOutputTimezone('UTC');
            
            $col = new Column\Standard('changeDate');
            $col->setLabel('Last change');
            $col->setWidth(15);
            $col->setType($colType);
            $dataGrid->addColumn($col);
        }
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }

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
        $dataGrid->setItemsPerPage(5);
        $dataGrid->setDataSource($this->getDataArray());
        
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

    private function getDataArray ()
    {
        $row = array(
            'id' => 1,
            'displayName' => 'Wayne? John!',
            'familyName' => 'Wayne',
            'givenName' => 'John',
            'gender' => 'm',
            'age' => 35,
            'weight' => 50,
            'birthday' => '1987-10-03',
            'changeDate' => '14:30:41 19.04.01'
        );
        $row2 = array(
            'id' => 2,
            'displayName' => 'Franz Ferdinand',
            'familyName' => 'Ferdinand',
            'givenName' => 'Franz',
            'gender' => 'm',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31',
            'changeDate' => '22:30:41 31.12.99'
        );
        $row3 = array(
            'id' => 3,
            'displayName' => 'Peter Kaiser',
            'familyName' => 'Kaiser',
            'givenName' => 'Peter',
            'gender' => 'm',
            'age' => 23,
            'weight' => 70.23,
            'birthday' => '1991-10-03',
            'changeDate' => '09:30:41 19.04.13'
        );
        $row4 = array(
            'id' => 5,
            'displayName' => 'Martin Keckeis',
            'familyName' => 'Keckeis',
            'givenName' => 'Martin',
            'gender' => 'm',
            'age' => 25,
            'weight' => 70,
            'birthday' => '1987-10-03',
            'changeDate' => '14:30:41 19.04.01'
        );
        $row5 = array(
            'id' => 5,
            'displayName' => 'Anna Marie Franz',
            'familyName' => 'Franz',
            'givenName' => 'Anna Marie',
            'gender' => 'f',
            'age' => 20,
            'weight' => 123.12222,
            'birthday' => '1981-01-31',
            'changeDate' => '22:30:41 31.12.99'
        );
        $row6 = array(
            'id' => 6,
            'displayName' => 'Sarah Blumenfeld',
            'familyName' => 'Blumenfeld',
            'givenName' => 'Sarah',
            'gender' => 'f',
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
        
        return $data;
    }
}
