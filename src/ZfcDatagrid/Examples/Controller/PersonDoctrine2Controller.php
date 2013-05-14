<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;

class PersonDoctrine2Controller extends AbstractActionController
{

    /**
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction ()
    {
        /* @var $dbAdapter \Zend\Db\Adapter\Adapter */
        $dbAdapter = $this->getServiceLocator()->get('zfcDatagrid_dbAdapter');
        
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setItemsPerPage(5);
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.doctrine2')
            ->getPersons(), $dbAdapter);
        
        $col = new Column\Standard('id', 'p');
        $col->setIdentity();
        $dataGrid->addColumn($col);
        
        {
            /**
             * Gravatar example
             * - take the email from the datasource
             * - object makes the rest :-)
             *
             * @note Could be whatever you want -> Grab data from everywhere you want with dynamic parameters :-)
             */
            $colEmail = new Column\Standard('email', 'p');
            $colEmail->setLabel('E-Mail');
            $colEmail->setHidden();
            
            $dataPopulation = new Column\DataPopulation\Object();
            $dataPopulation->setObject(new Column\DataPopulation\Object\Gravatar());
            $dataPopulation->addObjectParameterColumn('email', $colEmail);
            
            $col = new Column\Image('avatar');
            $col->setLabel('Avatar');
            $col->setDataPopulation($dataPopulation);
            $dataGrid->addColumn($col);
        }
        
        $col = new Column\Standard('displayName', 'p');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->addStyle(new Style\Bold());
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('familyName', 'p');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('givenName', 'p');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $col->setSortDefault(2, 'DESC');
        $dataGrid->addColumn($col);
        
        $dataGrid->addColumn($colEmail);
        
        $col = new Column\Standard('gender', 'p');
        $col->setLabel('Gender');
        $col->setWidth(10);
        $col->setReplaceValues(array(
            'm' => 'male',
            'f' => 'female'
        ));
        $col->setTranslationEnabled(true);
        $dataGrid->addColumn($col);
        
        {
            $col = new Column\Standard('age', 'p');
            $col->setLabel('Age');
            $col->setWidth(5);
            $col->setType(new Type\Number());
            $col->setFilterDefaultValue('>=20');
            
            $style = new Style\Color\Red();
            $style->setByValue($col, 20);
            $col->addStyle($style);
            
            $dataGrid->addColumn($col);
        }
        
        {
            $colType = new Type\Number();
            $colType->addAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
            $colType->setSuffix(' kg');
            
            $col = new Column\Standard('weight', 'p');
            $col->setLabel('Weight');
            $col->setWidth(10);
            $col->setType($colType);
            $dataGrid->addColumn($col);
        }
        
        $col = new Column\Standard('birthday', 'p');
        $col->setLabel('Birthday');
        $col->setWidth(10);
        $col->setType(new Type\Date());
        $col->setUserSortDisabled(true);
        $dataGrid->addColumn($col);
        
        {
            $colType = new Type\Date('Y-m-d H:i:s', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
            $colType->setSourceTimezone('Europe/Vienna');
            $colType->setOutputTimezone('UTC');
            
            $col = new Column\Standard('changeDate', 'p');
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
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.doctrine2')
            ->getPersons());
        
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
