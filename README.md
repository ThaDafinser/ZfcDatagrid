# Datagrid module for Zend Framework 2 
[![Master Branch Build Status](https://secure.travis-ci.org/ThaDafinser/ZfcDatagrid.png?branch=master)](http://travis-ci.org/ThaDafinser/ZfcDatagrid)
[![Coverage Status](https://coveralls.io/repos/ThaDafinser/ZfcDatagrid/badge.png)](https://coveralls.io/r/ThaDafinser/ZfcDatagrid)

A datagrid for ZF2 where the data input and output can be whatever you want...:-)

To get started, please follow the installation and the small ["Getting started guide"](https://github.com/ThaDafinser/ZfcDatagrid/blob/master/docs/Getting-started.md)

## Features
* Datasources: Doctrine2 (QueryBuilder + Collections), Zend\Db, PhpArray, ... (others possible)
* Output types: jqGrid, Bootstrap table, PDF, Excel, console, ... (others possible)
* different column types
* custom formatting, type based formatting (string, date, number, array...)
* column/row styling for all or based on value comparison
* column filtering  and sorting
* external data can be included to the dataset (like gravator or your own)
* pagination
* custom toolbar / view
* ...

## Installation
### (optional) Create a new ZF2 project
To get started with a ZF2 application, please see the [Skeleton application](http://framework.zend.com/manual/2.1/en/user-guide/skeleton-application.html)

### Get ZfcDatagrid
Install it with ``composer`` is easy
```sh
php composer.phar require thadafinser/zfc-datagrid:dev-master
```
(If you don't have composer:
Download it as a zip from github and put in into ``vendor/ZfcDatagrid`` and make sure that autoloading works)

Then enable the module

Add `ZfcDatagrid` to your `config/application.config.php`

Create the folder: `data/ZfcDatagrid`

### Test if it works
####Browser####
**PhpArray** http://YOUR-PROJECT/zfcDatagrid/person/bootstrap

**Doctrine2** http://YOUR-PROJECT/zfcDatagrid/personDoctrine2/bootstrap

**Zend\Db\Sql\Select** http://YOUR-PROJECT/zfcDatagrid/personZend/bootstrap
> Attention! The Zend\Db\Sql\Select example only works with Doctrine2 working (Doctrine will create the sqlite database....)

####Console####
```sh
cd YOUR-PROJECT/public/
php index.php datagrid person
php index.php datagrid person --page 2
```

## Screenshots
![ScreenShot](https://raw.github.com/ThaDafinser/ZfcDatagrid/master/docs/screenshots/ZfcDatagrid_bootstrap.jpg)
![ScreenShot](https://raw.github.com/ThaDafinser/ZfcDatagrid/master/docs/screenshots/ZfcDatagrid_console.jpg)

## Examples

Examples will be provided here:
https://github.com/ThaDafinser/ZfcDatagrid/blob/master/src/ZfcDatagrid/Examples/Controller/

## Code example
```PHP
<?php
namespace ZfcDatagrid\Examples\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcDatagrid\Column;
use ZfcDatagrid\Column\Type;
use ZfcDatagrid\Column\Style;

class PersonController extends AbstractActionController
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
        $dataGrid->setRowClickLink('/zfcDatagrid/example/edit');
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Standard('id');
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
            $colEmail = new Column\Standard('email');
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
        
        $dataGrid->addColumn($colEmail);
        
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
        $col->setType(new Type\DateTime());
        $col->setUserSortDisabled(true);
        $dataGrid->addColumn($col);
        
        {
            $colType = new Type\DateTime('Y-m-d H:i:s', \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
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

    /**
     * Usage
     * php index.php datagrid person
     * php index.php datagrid person --page 2
     *
     * @return \Zend\Http\Response\Stream
     */
    public function consoleAction ()
    {
        /* @var $dataGrid \ZfcDatagrid\Datagrid */
        $dataGrid = $this->getServiceLocator()->get('zfcDatagrid');
        $dataGrid->setTitle('Persons');
        $dataGrid->setDefaultItemsPerPage(5);
        $dataGrid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
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

    private function getDataArray ()
    {}
}

```


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/ThaDafinser/zfcdatagrid/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

