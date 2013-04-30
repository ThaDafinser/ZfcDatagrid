ZfcDatagrid
===========

A datagrid for ZF2 where the data input and output can be whatever you want :-)

WORK IN PROGRESS....

Test it!
===========

Installation
--------
* Download it (composer or zip)
    * composer: "zf-commons/zfc-datagrid": "dev-master"
    * Packagist: https://packagist.org/packages/zf-commons/zfc-datagrid
* Activate the module in YOUR-PROJECT/config/application.config.php
* Create the folder: YOUR-PROJECT/data/ZfcDatagrid

Test if it works
--------
* HTTP rendering http://YOUR-PROJECT/zfcDatagrid/example/bootstrap
* Console rendering
    * go to: YOUR-PROJECT/public
    * php index.php show example grid
    * php index.php show example grid --page 2

Features
===========

Currently available
--------
* datasources: 
    * php arrays
* pagination
* output formats: 
    * Bootstrap table
    * plain array
    * console
* different column types
    * DateTime
    * Number
    * String
* styling the data output by column and/or value
    * bold
    * color red
* custom views/templates possible
* custom configuration
* extending the service
* ...

TODO  List
--------
* datasources: 
    * Zend\Sql\Select
    * Doctrine\ORM\QueryBuilder
    * ...
* output formats: 
    * jqGrid
    * tcPDF
    * PHPExcel
    * ...
* different columns
    * [WIP] custom object as source
    * Buttons / Icons / Links
    * HTML
    * Images
* styling the data output by column and/or value
    * italic
    * more colors (yellow, green, ...)

Examples
===========

Examples will be provided here:
https://github.com/ThaDafinser/ZfcDatagrid/blob/master/src/ZfcDatagrid/Controller/ExampleController.php

Preview:
```PHP
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
}
```

Dependencies
===========
Required
--------
* PHP >= 5.3
* PHP intl extension
* ZF2
    * MVC (model, request, response)
    * Paginator
    * Cache
    * Session
    * Translator
* Twitter Bootstrap (currently only output mode)

Optional
--------
* ZF2
* Doctrine2 + DoctrineModule (if used as datasource)
* 
