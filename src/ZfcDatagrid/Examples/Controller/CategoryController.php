<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CategoryController extends AbstractActionController
{

    /**
     *
     * @return \ZfcDatagrid\Datagrid
     */
    private function getGrid()
    {
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Categories');
        $grid->setDefaultItemsPerPage(5);
        $grid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getCategorys());

        $col = new Column\Select('id');
        $col->setIdentity();
        $grid->addColumn($col);

        $colParentId = new Column\Select('parentId');
        $colParentId->setHidden(true);
        $grid->addColumn($colParentId);

        $colHasChildren = new Column\Select('hasChildren');
        $colHasChildren->setHidden(true);
        $grid->addColumn($colHasChildren);

        $colTags = new Column\Select('tags');
        $colTags->setLabel('Tags');
        $colTags->setHidden(true);
        $colTags->setWidth(30);
        $colTags->setType(new Type\PhpArray());
        $grid->addColumn($colTags);

        $toggle = new Column\Action\Button();
        $toggle->setLabel('+');
        $toggle->addShowOnValue($colHasChildren, 'y');
        $toggle->setAttribute('onclick', 'console.log($(this).parent().parent().attr(\'id\'));$.get(\'/zfcDatagrid/category/tree\', function (data) {
            console.log(this);
            $.each(data.data, function (index, value) {
                $(\'#'.$grid->getId().'\').jqGrid(\'addRowData\', value.idConcated, value, \'after\', 5);
             });
        });');

        $col = new Column\Action();
        $col->setLabel(' ');
        $col->setUniqueId('expandAction');
        $col->addAction($toggle);
        $grid->addColumn($col);

        $col = new Column\Select('name');
        $col->setLabel('Name');
        $col->setWidth(50);
        $col->setSortDefault(1);
        $col->setRendererParameter('formatter', '
            function (cellvalue, options, rowObject) {
                var valuePrint = cellvalue;

                $.each(rowObject.'.$colTags->getUniqueId().', function (index, value) {
                    valuePrint += \' <span class="label">\' + value + \'</span>\';
                });

                return valuePrint;
            }
        ', 'jqGrid');
        $grid->addColumn($col);

        $toggle = new Column\Action\Button();
        $toggle->setLabel('other action...');
        $toggle->addShowOnValue($colHasChildren, 'y');
        $toggle->setAttribute('onclick', 'alert(\'clicked\');');

        $col = new Column\Action();
        $col->addAction($toggle);
        $grid->addColumn($col);

        return $grid;
    }

    /**
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction()
    {
        $grid = $this->getGrid();
        $grid->render();

        return $grid->getResponse();
    }

    public function jqgridAction()
    {
        $grid = $this->getGrid();
        $grid->setRendererName('jqGrid');
        $grid->setUserFilterDisabled(true);

        $grid->render();

        return $grid->getResponse();
    }

    public function consoleAction()
    {
        $viewModel = new ViewModel();

        $grid = $this->getGrid();
        $grid->render();

        return $grid->getResponse();
    }

    /**
     * Get the tree data from a record
     */
    public function treeAction()
    {
        $data = array();
        $data[] = array(
            'id' => 10,
            'idParent' => '',
            'hasChildren' => 'n',
            'name' => 'blubb',
            'tags' => array('Tag1', 'tag2...'),
            'expandAction' => '',
            'action' => '',
            'idConcated' => 'blubb'
        );

        $viewModel = new JsonModel();
        $viewModel->setVariable('data', $data);

        return $viewModel;
    }
}
