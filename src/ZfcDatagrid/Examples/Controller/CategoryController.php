<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;
use ZfcDatagrid\Example\Form\CategoryFilterForm;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class CategoryController extends AbstractActionController
{

    /**
     *
     * @return \ZfcDatagrid\Datagrid
     */
    private function getGrid ()
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
        $toggle->setAttribute('onclick', 'console.log($(this).parent().parent().attr(\'id\'));$.get(\'/zfcDatagrid/category/tree\', function(data){
            console.log(this);
            $.each(data.data, function(index, value){ 
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
            function(cellvalue, options, rowObject){
                var valuePrint = cellvalue;
                
                $.each(rowObject.'.$colTags->getUniqueId().', function(index, value){
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
//         $col->setLabel('A');
        $col->addAction($toggle);
        $grid->addColumn($col);
        
        return $grid;
    }

    /**
     * Simple bootstrap table
     *
     * @return \ZfcDatagrid\Controller\ViewModel
     */
    public function bootstrapAction ()
    {
        $grid = $this->getGrid();
//         $grid->setRendererName('bootstrapTable');
        $grid->render();
        
        return $grid->getResponse();
    }

    public function jqgridAction ()
    {
//         $form = new CategoryFilterForm();
        
        $viewModel = new ViewModel();
        
        $grid = $this->getGrid();
        $grid->setRendererName('jqGrid');
        $grid->setUserFilterDisabled(true);
        
        $grid->render();
        
        return $grid->getResponse();
    }

    /**
     * Get the tree data from a record
     */
    public function treeAction ()
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
        
        return '
       [
            {"id":1,"parentId":"","hasChildren":"y","name":"Root","tags":["Tag1","Tag2"," Martin"," ZfcDatagrid"],"expandAction":"","action":"\u003Ca class=\u0022btn\u0022 href=\u0022#\u0022 onclick=\u0022alert(\u0027clicked\u0027);\u0022\u003Eblubb\u003C\/a\u003E","idConcated":"1"}
            ,{"id":5,"parentId":"","hasChildren":"y","name":"Root2","tags":[""],"expandAction":"", action":"\u003Ca class=\u0022btn\u0022 href=\u0022#\u0022 onclick=\u0022alert(\u0027clicked\u0027);\u0022\u003Eblubb\u003C\/a\u003E","idConcated":"5"}
            ,{"id":6,"parentId":5,"hasChildren":"n","name":"Second level of Root2","tags":[""],"expandAction":"","action":"","idConcated":"6"}
            ,{"id":2,"parentId":1,"hasChildren":"y","name":"Second level: entry 1","tags":["Tag1","Root"," Root2"],"expandAction":"","action":"\u003Ca class=\u0022btn\u0022 href=\u0022#\u0022 onclick=\u0022alert(\u0027clicked\u0027);\u0022\u003Eblubb\u003C\/a\u003E","idConcated":"2"}
            ,{"id":3,"parentId":2,"hasChildren":"n","name":"Third level: entry 1","tags":["cool"," stuff"],"expandAction":"","action":"","idConcated":"3"}
        ];
            
        ';
        /*
         * var myData= [ 
         {"id":1,"parentId":"","hasChildren":"y","name":"&nbsp;&nbsp;&nbsp;&nbsp;<i class=\"icon-leaf\"></i> asdf <b>test</b>","action": "","idConcated":"10"} 
         ,{"id":5,"parentId":"","hasChildren":"y","name":"&nbsp;&nbsp;&nbsp;&nbsp;asdf2","action":"","idConcated":"50"} 
         ,{"id":6,"parentId":5,"hasChildren":"n","name":"&nbsp;&nbsp;&nbsp;&nbsp;asdf3 level of Root2","action":"","idConcated":"60"} ,{"id":2,"parentId":1,"hasChildren":"y","name":"&nbsp;&nbsp;&nbsp;&nbsp;asdf4 level: entry 1","action":"","idConcated":"20"} ,{"id":3,"parentId":2,"hasChildren":"n","name":"&nbsp;&nbsp;&nbsp;&nbsp;asdf5 level: entry 1","action":"","idConcated":"30"} 
         ]; 
         
         $.each(myData, function(index, value){ 
             $('#defaultGrid').jqGrid('addRowData', value.idConcated, value, 'after', 5); 
         }); 
         
         //$('#defaultGrid').jqGrid('addRowData', 'idConcated', myData, 'first', 5);
         */
    }
}
