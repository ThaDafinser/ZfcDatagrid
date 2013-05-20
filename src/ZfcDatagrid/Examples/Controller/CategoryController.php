<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Examples\Data;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;

class CategoryController extends AbstractActionController
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
        $dataGrid->setDefaultItemsPerPage(5);
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getCategorys());
        $dataGrid->setRenderer('jqgrid');
        
        $col = new Column\Standard('id');
        $col->setIdentity();
        $dataGrid->addColumn($col);
        
        $colParentId = new Column\Standard('parentId');
        $colParentId->setHidden(true);
        $dataGrid->addColumn($colParentId);
        
        $colHasChildren = new Column\Standard('hasChildren');
        $colHasChildren->setHidden(true);
        $dataGrid->addColumn($colHasChildren);
        
        $toggle = new Column\Action\Button();
        $toggle->setLabel('+');
        $toggle->addShowOnValue($colHasChildren, 'y');
        
        $col = new Column\Action();
        $col->setLabel('');
        $col->addAction($toggle);
        $dataGrid->addColumn($col);
        
        $col = new Column\Standard('name');
        $col->setLabel('Name1');
        $col->setWidth(80);
        $col->setSortDefault(1);
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }
}
