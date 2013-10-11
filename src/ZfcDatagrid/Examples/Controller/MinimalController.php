<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;

class MinimalController extends AbstractActionController
{

    /**
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction ()
    {
        $row = array(
            'displayName' => 'Wayne? John!',
        );
        $row2 = array(
            'displayName' => 'Name2',
        );
        
        $data = array(
            $row,
            $row2
        );
        
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Minimal grid');
        
        //you can change here the renderer (allowed: bootstrapTable / jqgrid...default is bootstrapTable
        $dataGrid->setRenderer('jqgrid');
        $dataGrid->setDataSource($data);
        
        $col = new Column\Select('displayName');
        $col->setLabel('Name');
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();
        
        return $dataGrid->getResponse();
    }
}
