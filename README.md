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
        /* @var $grid \ZfcDatagrid\Datagrid */
        $grid = $this->getServiceLocator()->get('ZfcDatagrid\Datagrid');
        $grid->setTitle('Persons');
        $grid->setDefaultItemsPerPage(5);
        $grid->setRowClickLink('/zfcDatagrid/example/edit');
        $grid->setDataSource($this->getServiceLocator()
            ->get('zfcDatagrid.examples.data.phpArray')
            ->getPersons());
        
        $col = new Column\Select('id');
        $col->setIdentity();
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
            $colEmail->setHidden();
            
            $dataPopulation = new Column\DataPopulation\Object();
            $dataPopulation->setObject(new Column\DataPopulation\Object\Gravatar());
            $dataPopulation->addObjectParameterColumn('email', $colEmail);
            
            $col = new Column\Image('avatar');
            $col->setLabel('Avatar');
            $col->setDataPopulation($dataPopulation);
            $grid->addColumn($col);
        }
        
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
        $col->setSortDefault(2, 'DESC');
        $grid->addColumn($col);
        
        $grid->addColumn($colEmail);
        
        $col = new Column\Select('gender');
        $col->setLabel('Gender');
        $col->setWidth(10);
        $col->setReplaceValues(array(
            'm' => 'male',
            'f' => 'female'
        ));
        $col->setTranslationEnabled(true);
        $grid->addColumn($col);
        
        {
            $col = new Column\Select('age');
            $col->setLabel('Age');
            $col->setWidth(5);
            $col->setType(new Type\Number());
            
            $style = new Style\Color\Red();
            $style->setByValue($col, 20);
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
        
        $grid->render();
        
        return $grid->getResponse();
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

    private function getDataArray ()
    {}
}

```


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/ThaDafinser/zfcdatagrid/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

