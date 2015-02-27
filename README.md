# Datagrid/Datatable module for Zend Framework 2 
[![Master Branch Build Status](https://secure.travis-ci.org/ThaDafinser/ZfcDatagrid.png?branch=master)](http://travis-ci.org/ThaDafinser/ZfcDatagrid)
[![HHVM Status](http://hhvm.h4cc.de/badge/thadafinser/zfc-datagrid.svg)](http://hhvm.h4cc.de/package/thadafinser/zfc-datagrid)
[![Coverage Status](https://coveralls.io/repos/ThaDafinser/ZfcDatagrid/badge.png?branch=master)](https://coveralls.io/r/ThaDafinser/ZfcDatagrid?branch=master)
[![Total Downloads](https://poser.pugx.org/thadafinser/zfc-datagrid/downloads.png)](https://packagist.org/packages/thadafinser/zfc-datagrid)
[![Latest Stable Version](https://poser.pugx.org/thadafinser/zfc-datagrid/v/stable.png)](https://packagist.org/packages/thadafinser/zfc-datagrid)
[![Latest Unstable Version](https://poser.pugx.org/thadafinser/zfc-datagrid/v/unstable.png)](https://packagist.org/packages/thadafinser/zfc-datagrid)
[![License](https://poser.pugx.org/thadafinser/zfc-datagrid/license.png)](https://packagist.org/packages/thadafinser/zfc-datagrid)

A datagrid for ZF2 where the data input and output can be whatever you want...:-)

Over ***330 tests and 1000 assertions*** testing the stability currently! 

If you need help, please use following ressources
- [Installation](https://github.com/ThaDafinser/ZfcDatagrid#installation) 
-  ["Getting started guide"](https://github.com/ThaDafinser/ZfcDatagrid/blob/master/docs/02.%20Quick%20Start.md)
- [Documentation](https://github.com/ThaDafinser/ZfcDatagrid/blob/master/docs/)
- [Code examples](https://github.com/ThaDafinser/ZfcDatagrid/blob/master/src/ZfcDatagrid/Examples/Controller/)
- [Issues/Help](https://github.com/ThaDafinser/ZfcDatagrid/issues)

If you want to help out on this project:
- seek through the [issues](https://github.com/ThaDafinser/ZfcDatagrid/issues)
- [documentation](https://github.com/ThaDafinser/ZfcDatagrid/blob/master/docs/)
- ...any other help

## Features
* Datasources: Doctrine2 (QueryBuilder + Collections), Zend\Db, PhpArray, ... (others possible)
* Output types: jqGrid, Bootstrap table, PDF, Excel, CSV, console, ... (others possible)
  *  Bootstrap table with Daterange Filter need to load manually js and css
* different column types
* custom formatting, type based formatting (string, date, number, array...)
* column/row styling for all or based on value comparison
* column filtering  and sorting
* external data can be included to the dataset (like gravator or any other)
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

Add `ZfcDatagrid` to your `config/application.config.php`

Finally create the folder: `data/ZfcDatagrid`

### Test if it works
####Browser####

> Attention! Only PhpArray works out of the box!
> For Zend\Db\Sql\Select and Doctrine2 you need to install DoctrineORMModule (Doctrin2 creates the database for Zend\Db\Sql\Select)

**PhpArray** http://YOUR-PROJECT/zfcDatagrid/person/bootstrap

**Doctrine2** http://YOUR-PROJECT/zfcDatagrid/personDoctrine2/bootstrap

**Zend\Db\Sql\Select** http://YOUR-PROJECT/zfcDatagrid/personZend/bootstrap


####Console####
If you just type `php index.php` a help for all commands will be shown
```sh
cd YOUR-PROJECT/public/
php index.php datagrid person
php index.php datagrid person --page 2
php index.php datagrid person --sortBys=age
php index.php datagrid person --sortBys=age,givenName --sortDirs=ASC,DESC
```

## Screenshots
![ScreenShot](https://raw.github.com/ThaDafinser/ZfcDatagrid/master/docs/screenshots/ZfcDatagrid_bootstrap.jpg)
![ScreenShot](https://raw.github.com/ThaDafinser/ZfcDatagrid/master/docs/screenshots/ZfcDatagrid_console.jpg)
