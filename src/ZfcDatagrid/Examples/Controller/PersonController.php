<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Column\Formatter\Email;

class PersonController extends AbstractActionController
{

    private function getGrid()
    {
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Persons');
        $grid->setDefaultItemsPerPage(5);
        $grid->setTableClasses(array('table', 'table-striped', 'table-bordered', 'table-condensed'));
        $grid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Select('id');
        $col->setIdentity();
        $grid->addColumn($col);
        
        $action = new Column\Action\Checkbox();
        
        $col = new Column\Action('checkboxes');
        $col->addAction($action);
        $grid->addColumn($col);
        
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
            $grid->addColumn($colEmail);
            
            $dataPopulation = new Column\DataPopulation\Object();
            $dataPopulation->setObject(new Column\DataPopulation\Object\Gravatar());
            $dataPopulation->addObjectParameterColumn('email', $colEmail);
            
            $col = new Column\ExternalData('avatar');
            $col->setLabel('Avatar');
            $col->setDataPopulation($dataPopulation);
            $col->setType(new Type\Image());
            $grid->addColumn($col);
        }
        
        $col = new Column\Select('displayName');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->setFormatter(new Email());
        $col->addStyle(new Style\Bold());
        $grid->addColumn($col);
        
        $col = new Column\Select('familyName');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $grid->addColumn($col);
        
        $col = new Column\Select('givenName');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $col->setSortDefault(2, 'DESC');
        $grid->addColumn($col);
        
        $style = new Style\BackgroundColor(array(
            200,
            200,
            200
        ));
        $style->addByValue($col, 'Martin');
        $grid->addRowStyle($style);
        
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
        $style->addByValue($col, 'male');
        $col->addStyle($style);
        $col->setTranslationEnabled(true);
        $grid->addColumn($col);
        
        {
            $col = new Column\Select('age');
            $col->setLabel('Age');
            $col->setWidth(5);
            $col->setType(new Type\Number());
            $col->setFilterDefaultValue('>=20');
            
            $style = new Style\Color(Style\Color::$RED);
            $style->addByValue($col, 20);
            $col->addStyle($style);
            
            $grid->addColumn($col);
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
            $grid->addColumn($col);
        }
        
        $col = new Column\Select('birthday');
        $col->setLabel('Birthday');
        $col->setWidth(10);
        $col->setType(new Type\DateTime());
        $col->setUserSortDisabled(true);
        $grid->addColumn($col);
        
        {
            $colType = new Type\DateTime('Y-m-d H:i:s', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
            $colType->setSourceTimezone('Europe/Vienna');
            $colType->setOutputTimezone('UTC');
            
            $col = new Column\Select('changeDate');
            $col->setLabel('Last change');
            $col->setWidth(15);
            $col->setType($colType);
            $grid->addColumn($col);
        }
        
        $action = new Column\Action\Button();
        $action->setLabel('test');
        $action->setAttribute('href', '/someAction/id/' . $action->getRowIdPlaceholder());
        
        $col = new Column\Action();
        $col->setLabel('Actions');
        $col->setWidth(10);
        $col->addAction($action);
        $grid->addColumn($col);
        
        $grid->setRowClickAction($action);
        
        return $grid;
    }

    /**
     * bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction()
    {
        $grid = $this->getGrid();
        
        $grid->render();
        
        return $grid->getResponse();
    }

    /**
     * bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function jqgridAction()
    {
        $grid = $this->getGrid();
        $grid->setRendererName('jqGrid');
        
        $grid->render();
        
        return $grid->getResponse();
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
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Persons');
        $grid->setDefaultItemsPerPage(5);
        $grid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Select('id');
        $col->setIdentity();
        $grid->addColumn($col);
        
        $col = new Column\Select('displayName');
        $col->setLabel('Displayname');
        $col->setWidth(25);
        $col->setSortDefault(1, 'ASC');
        $col->addStyle(new Style\Bold());
        $grid->addColumn($col);
        
        $col = new Column\Select('familyName');
        $col->setLabel('Familyname');
        $col->setWidth(15);
        $grid->addColumn($col);
        
        $col = new Column\Select('givenName');
        $col->setLabel('Givenname');
        $col->setWidth(15);
        $grid->addColumn($col);
        
        $col = new Column\Select('age');
        $col->setLabel('Age');
        $col->setWidth(10);
        $col->setType(new Type\Number());
        $grid->addColumn($col);
        
        $grid->render();
        
        return $grid->getResponse();
    }
}
