<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Examples\Data;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;

class PersonController extends AbstractActionController
{

    private function getGrid()
    {
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setDefaultItemsPerPage(5);
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Select('id');
        $col->setIdentity();
        $dataGrid->addColumn($col);
        
        $action = new Column\Action\Checkbox();
        
        $col = new Column\Action('checkboxes');
        $col->addAction($action);
        $dataGrid->addColumn($col);
        
        {
            /**
             * Gravatar example
             * - take the email from the datasource
             * - object makes the rest :-)
             *
             * @note Could be whatever you want -> Grab data from everywhere you want with dynamic parameters :-)
             */
            $colEmail = new Column\Select('email');
            $colEmail->setLabel('E-Mail');
            $colEmail->setHidden(true);
            $dataGrid->addColumn($colEmail);
            
            $dataPopulation = new Column\DataPopulation\Object();
            $dataPopulation->setObject(new Column\DataPopulation\Object\Gravatar());
            $dataPopulation->addObjectParameterColumn('email', $colEmail);
            
            $col = new Column\ExternalData('avatar');
            $col->setLabel('Avatar');
            $col->setDataPopulation($dataPopulation);
            $col->setType(new Type\Image());
            $dataGrid->addColumn($col);
        }
        
        $col = new Column\Select('displayName');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->addStyle(new Style\Bold());
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('familyName');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('givenName');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $col->setSortDefault(2, 'DESC');
        $dataGrid->addColumn($col);
        
        $style = new Style\BackgroundColor(array(
            200,
            200,
            200
        ));
        $style->setByValue($col, 'Martin');
        $dataGrid->addRowStyle($style);
        
        $col = new Column\Select('gender');
        $col->setLabel('Gender');
        $col->setWidth(10);
        $col->setReplaceValues(array(
            'm' => 'male',
            'f' => 'female'
        ));
        $style = new Style\BackgroundColor(array(
            200,
            100,
            100
        ));
        $style->setByValue($col, 'male');
        $col->addStyle($style);
        $col->setTranslationEnabled(true);
        $dataGrid->addColumn($col);
        
        {
            $col = new Column\Select('age');
            $col->setLabel('Age');
            $col->setWidth(5);
            $col->setType(new Type\Number());
            $col->setFilterDefaultValue('>=20');
            
            $style = new Style\Color(Style\Color::$RED);
            $style->setByValue($col, 20);
            $col->addStyle($style);
            
            $dataGrid->addColumn($col);
        }
        
        {
            $colType = new Type\Number();
            $colType->addAttribute(\NumberFormatter::FRACTION_DIGITS, 2);
            $colType->setSuffix(' kg');
            
            $col = new Column\Select('weight');
            $col->setLabel('Weight');
            $col->setWidth(10);
            $col->setType($colType);
            $col->setFilterDefaultOperation(\ZfcDatagrid\Filter::GREATER_EQUAL);
            $dataGrid->addColumn($col);
        }
        
        $col = new Column\Select('birthday');
        $col->setLabel('Birthday');
        $col->setWidth(10);
        $col->setType(new Type\DateTime());
        $col->setUserSortDisabled(true);
        $dataGrid->addColumn($col);
        
        {
            $colType = new Type\DateTime('Y-m-d H:i:s', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
            $colType->setSourceTimezone('Europe/Vienna');
            $colType->setOutputTimezone('UTC');
            
            $col = new Column\Select('changeDate');
            $col->setLabel('Last change');
            $col->setWidth(15);
            $col->setType($colType);
            $dataGrid->addColumn($col);
        }
        
        $action = new Column\Action\Button();
        $action->setLabel('test');
        $action->setAttribute('href', '/someAction/id/' . $action->getRowIdPlaceholder());
        
        $col = new Column\Action();
        $col->setLabel('Actions');
        $col->setWidth(10);
        $col->addAction($action);
        $dataGrid->addColumn($col);
        
        $dataGrid->setRowClickAction($action);
        
        return $dataGrid;
    }

    /**
     * bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction()
    {
        $dataGrid = $this->getGrid();
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }

    /**
     * bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function jqgridAction()
    {
        $dataGrid = $this->getGrid();
        $dataGrid->setRenderer('jqgrid');
        
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
    public function consoleAction()
    {
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setDefaultItemsPerPage(5);
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Select('id');
        $col->setIdentity();
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('displayName');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->addStyle(new Style\Bold());
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('familyName');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('givenName');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $dataGrid->addColumn($col);
        
        $col = new Column\Select('age');
        $col->setLabel('Age');
        $col->setWidth(10);
        $col->setType(new Type\Number());
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }
}
