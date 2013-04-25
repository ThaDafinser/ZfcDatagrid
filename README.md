ZfcDatagrid
===========

A datagrid for ZF2 where the data input and output can be whatever you want :-)


Features
===========
* Datasources: Zend\Sql\Select, Doctrine\ORM\QueryBuilder, plain arrays,...
* Render/output as: Bootstrap table, jqGrid, tcPDF, PHPExcel, ...
* Pagination
* Custom views/templates possible
* configuration
* extending the service
* writing own sources or outputs
* ...TBD

Example
===========

```PHP
namespace MyModule\Controller;

class ExampleController extends AbstractActionController
{

    public function listAction ()
    {
        $queryBuilder = new \Doctrine\ORM\QueryBuilder();
        //@todo write the query
        
        $dataGrid = $this->getServiceLocator()->get('ZfcDatagrid');
        
        $dataGrid->setTitle('Title test');
        $dataGrid->setDataSource($queryBuilder);
        
        $col = new \ZfcDatagrid\Column\Standard('id', 'a');
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('displayName', 'a');
        $col->setLabel('Displayname');
        $col->setWidth(50);
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('familyName', 'a');
        $col->setLabel('Familyname');
        $col->setWidth(25);
        $dataGrid->addColumn($col);
        
        $col = new \ZfcDatagrid\Column\Standard('givenName', 'a');
        $col->setLabel('Givenname');
        $col->setWidth(25);
        $dataGrid->addColumn($col);
        
        $dataGrid->execute();

        return $dataGrid->getViewModel();
    }
}
```
