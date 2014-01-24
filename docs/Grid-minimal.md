# A minimal ZfcDatagrid

This is an example to just start off and see if the datagrid works.
But it shows you how easy it's to easy.

```php
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
        
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Minimal grid');
        
        //you can change here the renderer (allowed: bootstrapTable / jqgrid...default is bootstrapTable)
        //$grid->setRenderer('jqgrid');
        $grid->setDataSource($data);
        
        $col = new Column\Standard('displayName');
        $col->setLabel('Name');
        $grid->addColumn($col);
        
        $grid->render();
        
        return $grid->getResponse();
    }
}
```